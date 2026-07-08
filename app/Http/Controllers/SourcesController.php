<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Source;
use App\Models\MomReport;
use App\Models\Money;
use App\Models\User;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Relation;
use App\Models\Logs;
use App\Models\LhsReport;
use App\Models\SubmanagerPermissions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;
use DataTables;
use App\Imports\CampaignImport;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
ini_set('memory_limit', '1024M');

class SourcesController extends Controller
{



    public function __construct()
    {
        $this->middleware('auth');
    }
    public function manageleadchart(Request $request)
    {
        $employeelist = User::select('id', 'first_name', 'last_name')->where('user_id', Auth::id())->orderby('first_name')->get();
        return view('manageleadchart.index', compact('employeelist'));
    }
    public function filtercampaign(Request $request)
    {
        // dd($request->all());

        $dates = is_string($request->date) ? explode(', ', $request->date) : (array) $request->date;
        $dates = array_filter($dates); // Remove empty values
        if (empty($dates)) {
            $dates = [now()->toDateString()]; // Fallback to current date if empty
        }

        $result = [];
        $i = 0;
        foreach ($dates as $date) {

            $sources = Lead::select('leads.source_id', DB::raw('COUNT(*) as leadscount'))
                ->join('notes', 'leads.id', '=', 'notes.lead_id')
                ->where('leads.asign_to', $request->employee_id)
                ->where('notes.user_id', $request->employee_id)
                ->where(DB::raw('DATE(notes.created_at)'), $date)
                ->whereHas('source', function ($query) {
                    $query->where('is_active', '1');
                })
                ->groupBy('leads.source_id')
                ->with('source:id,source_name,description')
                ->get();

            $result[$i] = $sources;
            $i++;
        }

        $sourceschart = Lead::select('leads.source_id', DB::raw('COUNT(*) as leadscount'))
            ->join('notes', 'leads.id', '=', 'notes.lead_id')
            ->where('leads.asign_to', $request->employee_id)
            ->where('notes.user_id', $request->employee_id)
            ->whereIn(DB::raw('DATE(notes.created_at)'), $dates)
            ->whereHas('source', function ($query) {
                $query->where('is_active', '1');
            })
            ->groupBy('leads.source_id')
            ->with('source:id,source_name,description')
            ->get();


        echo json_encode(['status' => 200, 'data' => $result, 'datachart' => $sourceschart]);
        exit;
    }

    public function index()
    {
        $admin = User::where(['is_admin' => Null, 'id' => auth()->user()->id])->first('id');
        if (!empty($admin)) {
            $data = Source::with('leadNotImported')->select("*", DB::raw('(SELECT SUM(amount) FROM money WHERE money.source_id = sources.id) as amount'))->with('closed_leads')->orderBy('source_name', 'asc')->paginate(50);

        } else {
            $data = Source::where(function ($query) {
                $query->where(['user_id' => auth()->user()->id])
                    ->orWhere(['assign_to_manager' => auth()->user()->id])
                    ->where('is_active', 1); // Condition for is_active = 1
            })
                ->select("*", DB::raw('(SELECT SUM(amount) FROM money WHERE money.source_id = sources.id) as amount'))
                ->with('closed_leads')
                ->with('leadNotImported')
                ->orderBy('source_name', 'asc')->paginate(50);  //->limit(5)->get()->where('id', 362)->get();
        }
        // dd($data);
        $managers = DB::table('users')->where('is_admin', 2)->where('deleted_at', NULL)->where(function ($query) {
            $query->where('manager_type', 1)
                ->orWhereNull('manager_type');
        })->get()->toArray();
        // dd($data);
        $externalManagers = DB::table('users')->where('is_admin', 2)->where('deleted_at', NULL)->where('manager_type', 2)->get()->toArray();
        $totalCampaigns = Source::count();

        $active = Source::where('is_active', 1)->count();
        $inactive = Source::where('is_active', 2)->count();
        return view('sources.list_new')->with(['datas' => $data, 'managers' => $managers, 'externalManagers' => $externalManagers, 'active' => $active, 'inactive' => $inactive, 'totalCampaigns' => $totalCampaigns]);
    }


    public function create()
    {
        return view('sources.create');
    }
    public function create_campaign($id)
    {
        $source = Source::where('id', $id)->first();
        return view('sources.add_campaign')->with(['source' => $source]);
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'source_name' => 'required',
            'description' => 'required',
        ]);

        $validator->after(function ($validator) use ($request) {
            $exists = Source::whereRaw('LOWER(source_name) = ?', [strtolower($request->source_name)])
                ->whereRaw('LOWER(description) = ?', [strtolower($request->description)])
                ->exists();
            if ($exists) {
                $validator->errors()->add('source_name', 'Campaign already exists.');
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => $validator->errors()->all()
                ]);
            }
            return redirect()->route('sources.create')->withErrors($validator)->withInput()->with('error', $validator->errors()->first());
        }

        // Create Campaign
        $data = [
            'user_id' => auth()->id(),
            'source_name' => $request->source_name,
            'description' => $request->description
        ];

        $source = Source::create($data);

        // File Upload
        if ($request->hasFile('lead_file') && $request->file('lead_file')->isValid()) {

            $file = $request->file('lead_file');

            $importService = new CampaignImport($source->id);

            $importService->importLeads(
                $file,
                $request->import_duplicate ?? 0,
                $request->source_name ?? null
            );
        }

        // Logs
        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->description = 'A new campaign ' . $request->source_name . '-' . $request->description . ' is added';
        $logs->type = 14;
        $logs->source_id = $source->id;
        $logs->save();

        return redirect()->route('leads.assign_lead_emp', ['id' => $source->id])->with('success', 'Campaign added successfully');
    }


    public function show($id)
    {
        //
    }


    public function camp_assign(Request $request, $id)
    {
        $data = Source::where(['id' => $id])->first();
        if ($data) {
            if (isset($request->external_manager) && $request->external_manager == true) {
                $data->assign_to_external_manager = $request->assignedTo;
                $data->accessible_fields = serialize(json_decode($_GET['selected_fields']));
                Lead::where('source_id', $id)->update(['assign_to_external_manager' => $request->assignedTo]);
            } else {
                $data->assign_to_manager = $request->assignedTo;
                Lead::where('source_id', $id)->update(['asign_to_manager' => $request->assignedTo]);
            }
            $data->save();
        }
        return redirect('sources')->with('success', 'Campaign Assigned Successfully');
    }

    public function assignManager(Request $request)
    {
        $data = Source::where(['id' => $request->campaign_id])->first();

        if ($data) {
            $data->assign_to_manager = $request->manager_id;
            $data->save();
            Lead::where('source_id', $request->campaign_id)->update(['asign_to_manager' => $request->manager_id]);
        }

        echo json_encode(['status' => 200, 'message' => 'Manager Assigned']);
        exit;
    }

    public function destroy($id)
    {
        //
    }

    public function delete($id)
    {

        $source = Source::findOrFail($id);
        $lead = Lead::where('source_id', $id);
        $relation = Relation::where('assign_to_cam', $id);
        $relation->delete();
        $lead->delete();
        $source->delete();
        return redirect('sources')->with('success', 'Source Deleted Successfully.');
    }

    public function updateAmount(Request $request)
    {

        /*$validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);


        if ($validator->passes()) {

            $data = array(
                'status'=>$request->status
            );

             //Lead::where('id', $request->lead_id)->update(['status'=>$request->status]);
            return response()->json(['success'=>'Updated Successfully.']);
        }
        return response()->json(['error'=>$validator->errors()->all()]);

        */

        $data = array(
            'source_id' => $request->source_id,
            'amount' => $request->amount,
            'date' => $request->date
        );

        //print_r($data); dd();
        Money::create($data);

        //Lead::where('source_id', $request->source_id)->update([]);
        return response()->json(['success' => 'Updated Successfully.']);
    }
    public function campaignsAjaxPagination(Request $request)
    {
        ini_set('memory_limit', '512M');

        if ($request->ajax()) {
            if ($request->status_filter == 'total') {
                $status = [1, 2];
            } elseif ($request->status_filter == 'active') {
                $status = [1];
            } else {
                $status = [2];
            }
            if (Auth::user()->is_admin == null) {

                $query = Source::select(
                    "id",
                    "source_name",
                    "description",
                    "created_at",
                    "updated_at",
                    "assign_to_manager",
                    "is_active",
                    DB::raw("'N/A' as company_distribution"),
                    DB::raw("(SELECT COUNT(*) FROM leads WHERE leads.source_id = CAST(sources.id AS CHAR)) as total_leads"),
                    DB::raw("EXISTS(SELECT 1 FROM mom_report JOIN leads ON mom_report.lead_id = leads.id WHERE leads.source_id = CAST(sources.id AS CHAR) AND mom_report.mom_file_path IS NOT NULL) as has_mom_report")
                )
                    ->whereIn('is_active', $status)
                    ->with(['leadNotImported', 'manager']);

            } else {

                $query = Source::where(function ($query) {
                    $query->where('user_id', auth()->user()->id)
                        ->orWhere('assign_to_manager', auth()->user()->id);
                })
                    ->select(
                        "id",
                        "source_name",
                        "description",
                        "created_at",
                        "updated_at",
                        "assign_to_manager",
                        "is_active",
                        DB::raw("'N/A' as company_distribution"),
                        DB::raw("(SELECT COUNT(*) FROM leads WHERE leads.source_id = CAST(sources.id AS CHAR)) as total_leads"),
                        DB::raw("EXISTS(SELECT 1 FROM mom_report JOIN leads ON mom_report.lead_id = leads.id WHERE leads.source_id = CAST(sources.id AS CHAR) AND mom_report.mom_file_path IS NOT NULL) as has_mom_report"),
                        DB::raw("(SELECT SUM(amount) FROM money WHERE money.source_id = sources.id) as amount")
                    )
                    ->whereIn('is_active', $status)
                    ->with(['leadNotImported', 'manager']);
            }

            return DataTables::eloquent($query)

                ->filter(function ($query) use ($request) {
                    if (!empty($request->search['value'])) {
                        $keyword = strtolower($request->search['value']);

                        $query->where(function ($q) use ($keyword) {
                            // Search in Source Name
                            $q->whereRaw('LOWER(sources.source_name) LIKE ?', ["%{$keyword}%"])
                                // OR Search in Description
                                ->orWhereRaw('LOWER(sources.description) LIKE ?', ["%{$keyword}%"]);
                        });
                    }
                })

                /* Total leads column */
                ->editColumn('total_leads_new', function ($data) {
                    return '<p data-tippy-content="View Leads" data-sid="' . $data->id . '" onclick="clickmodal(' . $data->id . ');" style="color:#5FBC01 !important">' . $data->total_leads . '</p>';
                })

                /* Source Name */
                ->editColumn('source_name_new', function ($data) {
                    return '<div class="tooltip1 source-item source-item-' . $data->id . '" 
                                data-tippy-content="Source: ' . $data->source_name . '" 
                                data-source-id="' . $data->id . '"
                                style="text-decoration: underline dotted;cursor:pointer">
                                ' . $data->source_name . '
                            </div>';
                })

                ->editColumn('created_at_new', function ($data) {
                    return \Carbon\Carbon::parse($data->created_at)->format('d-m-Y');
                })

                ->editColumn('updated_at_new', function ($data) {
                    return \Carbon\Carbon::parse($data->updated_at)->format('d-m-Y');
                })

                ->addColumn('manager_name', function ($row) {
                    if (!empty($row->assign_to_manager) && !empty($row->manager)) {
                        return $row->manager->name;
                    } else {
                        if (Auth::user()->is_admin == null) {

                            $assignmanager = '<a href="#" onclick="assignmanager(' . $row->id . ');" style="background-color:black;color:white">
                                    <span class="label label-warning" data-tippy-content="Assign to Manager">
                                        Assign
                                    </span>
                                  </a>';
                            return $assignmanager;
                        } else {
                            return 'N/A';
                        }
                    }

                })

                ->addColumn('status', function ($row) {
                    $checked = $row->is_active == 1 ? 'checked' : '';
                    return '<input data-sid="' . $row->id . '" class="switchery" type="checkbox" ' . $checked . ' 
                            onchange="updatestatus(' . $row->id . ');">';
                })

                /* ACTION COLUMN WITH ALL TIPPY ADDED */
                ->addColumn('action', function ($row) {
                    $html = '
                    
                        <a href="' . url('/sources/' . $row->id . '/leadview') . '" target="_blank">
                            <span class="label" data-tippy-content="View Leads" style="color:#000;font-size:15px;">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </a>
    
                        <a href="' . url('/lead/exportCsv/' . $row->id . '/report_down') . '">
                            <span class="label" data-tippy-content="Download Excel" style="color:#000;font-size:15px;">
                                <i class="fa-solid fa-file-excel"></i>
                            </span>
                        </a>
    
                        <a href="' . url('/lead/export/' . $row->id . '/pdf_down') . '">
                            <span class="label" data-tippy-content="Download Word File" style="color:#55ce63;font-size:15px;">
                                <i class="fa-solid fa-file-word"></i>
                            </span>
                        </a>';

                    if (!empty($row->has_mom_report)) {
                        $html .= '
                            <a href="' . url('download-mom-report', ['source_id' => $row->id]) . '">
                                <span class="label" data-tippy-content="Download MOM Report" style="color:blue;font-size:15px;">
                                    <i class="fa-solid fa-file-download"></i>
                                </span>
                            </a>';
                    }

                    if (Auth::user()->is_admin == 2) {
                        $html .= '
                            <a href="' . url('/add_leads/' . $row->id) . '" target="_blank">
                                <span class="label" data-tippy-content="Upload Leads" style="color:#000;font-size:15px;">
                                    <i class="fa-solid fa-upload"></i>
                                </span>
                            </a>';
                    }

                    if (!empty($row->leadNotImported)) {
                        $html .= '
                            <a href="' . route('download.csv', ['filename' => $row->leadNotImported->file_name]) . '">
                                <span class="label" data-tippy-content="Download Raw Leads" style="color:#ac2609;font-size:15px;">
                                    <i class="fa-solid fa-file-download"></i>
                                </span>
                            </a>';
                    }

                    if (Auth::user()->is_admin == null) {
                        $html .= '
                            <a href="' . url('sources/delete', ['id' => $row->id]) . '" onclick="return confirm(\'Are you sure?\')">
                                <span class="label" data-tippy-content="Delete Source" style="color:#dc3545;font-size:15px;">
                                    <i class="fa-solid fa-trash"></i>
                                </span>
                            </a>
    
                            <a href="' . url('sources/source-edit', $row->id) . '">
                                <span class="label" data-tippy-content="Edit Source" style="color:#000;font-size:15px;">
                                    <i class="fa-solid fa-pen"></i>
                                </span>
                            </a>';
                    }

                    return $html;
                })

                ->addColumn('transfer', function ($row) {
                    return '<button style="background-color:#192e62;color:#fff;border-radius:3px"
                                data-tippy-content="Transfer Leads"
                                onclick="transfermodal(' . $row->id . ', \'' . addslashes($row->source_name) . '\');">
                                Transfer
                            </button>';
                })

                ->rawColumns(['total_leads_new', 'action', 'status', 'source_name_new', 'transfer', 'manager_name'])
                ->toJson();
        }
    }





    public function sourceEdit($id)
    {
        $data = Source::where(['id' => $id])->first();
        return view('sources.edit')->with(['data' => $data]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'source_name' => 'required',
                'description' => 'required',
            ],
            $messages = [
                'required' => 'The :attribute field is required.',
            ]
        );

        $validator->after(function ($validator) use ($request, $id) {
            $exists = Source::whereRaw('LOWER(source_name) = ?', [strtolower($request->source_name)])
                ->whereRaw('LOWER(description) = ?', [strtolower($request->description)])
                ->where('id', '!=', $id)
                ->exists();
            if ($exists) {
                $validator->errors()->add('source_name', 'Campaign already exists.');
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => $validator->errors()->all()
                ]);
            }
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->first());
        }

        $input = $request->all();

        $data = Source::find($id);

        $data->source_name = $input['source_name'];
        $data->description = $input['description'];
        if (isset($input['start_date']) && !empty($input['start_date'])) {
            $data->start_date = date('Y-m-d', strtotime($input['start_date']));
        } else {
            $data->start_date = NULL;
        }
        if (isset($input['end_date']) && !empty($input['end_date'])) {
            $data->end_date = date('Y-m-d', strtotime($input['end_date']));
        } else {
            $data->end_date = NULL;
        }
        $data->save();

        return redirect('sources')->with('success', 'Source Updated Successfully.');
    }

    public function statusUpdate(Request $request)
    {
        $campaign = Source::findOrFail($request->source_id);
        if ($campaign->is_active == 1) {
            $campaign->is_active = 2;
            $message = $campaign->source_name . '-' . $campaign->description . ' is deactivated';

            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->description = $message;
            $logs->type = 13;
            $logs->source_id = $request->source_id;
            $logs->save();
        } else {
            $message = $campaign->source_name . '-' . $campaign->description . ' is activated';

            $campaign->is_active = 1;
            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->description = $message;
            $logs->type = 13;
            $logs->source_id = $request->source_id;
            $logs->save();
        }
        $campaign->save();
        echo json_encode(['status' => 200, 'message' => 'Campaign ' . $message . '.']);
        exit;
    }

    /**
     * @param mixed $id
     */

    public function getLeadBySourceId($id)
    {
        $leads = Lead::where('source_id', $id)
            ->where('status', '<>', '3')
            ->select('id', 'company_name')
            ->selectRaw(" SUM( CASE WHEN status = '1' THEN 1 ELSE 0 END) AS pending_leads ")
            ->selectRaw(" SUM( CASE WHEN status = '2' THEN 1 ELSE 0 END) AS failed_leads ")
            ->selectRaw(" SUM( CASE WHEN status = '4' THEN 1 ELSE 0 END) AS inprogress_leads ")
            ->selectRaw(' COUNT(source_id) as total_leads')
            ->groupBy('company_name')
            ->get();
        $html = '<div class="table-responsive m-t-40" id="table_data">
                    <table id="sources" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0"
                        width="100%">
                        <thead class="heading-custom">
                            <tr>
                                <th width="100" style="text-align:center">Company Name</th>
                                <th width="50" style="text-align:center">Pending Leads</th>
                                <th width="50" style="text-align:center">Failed Leads</th>
                                <th width="50" style="text-align:center">Inprogress Leads</th>
                                <th width="50" style="text-align:center">Action</th>
                            </tr>
                        </thead>
                        <tbody>';
        if ($leads->count() > 0) {
            foreach ($leads as $lead) {
                $html .= '<tr>          
                                    <td style="white-space: pre-wrap" width="200">' . $lead->company_name . '</td>
                                    <td>' . $lead->pending_leads . ' <fieldset>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="' . $lead->company_name . '[]" required class="custom-control-input">
                                            <span class="custom-control-label"></span>
                                        </label>
                                    </fieldset></td>
                                    <td>' . $lead->failed_leads . ' <fieldset>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" value="2" name="' . $lead->company_name . '[]" required class="custom-control-input">
                                            <span class="custom-control-label"></span>
                                        </label>
                                    </fieldset></td>
                                    <td>' . $lead->inprogress_leads . ' <fieldset>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" value="4" name="' . $lead->company_name . '[]" required class="custom-control-input">
                                            <span class="custom-control-label"></span>
                                        </label>
                                    </fieldset></td>
                                    <td>
                                        <a href="javascript:void(0);" data-company="' . $lead->company_name . '" onclick="getAlredayAssignedUsers(this)">
                                            <span class="label btn-success">Users</span>
                                        </a>
                                    </td>
                                </tr>';
            }
        } else {
            $html .= '<tr>
                                        <td colspan="5" style="text-align:center; padding:20px; font-weight:bold;">
                                            No records found
                                        </td>
                                      </tr>';
        }

        $html .= '</tbody>
            </table>
        </div>';
        return $html;
    }
    /**
     * @param mixed Request
     */

    public function getLeadBySourceIdtransfer($id)
    {
        $leads = Lead::where('source_id', $id)
            ->select('id', 'company_name')
            ->groupBy('company_name')
            ->get();
        $source = Source::where('id', $id)->first();
        $source_list = Source::where(['source_name' => $source->source_name])
            ->where('id', '!=', $id)
            ->get();

        $html = '<div class="table-responsive m-t-40" id="table_data">
                     <table id="sources" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0"
                         width="100%">
                         <thead class="heading-custom">
                             <tr>
                                 <th width="100" style="text-align:center">Company Name</th>
                                 <th width="100" style="text-align:center">Select Company</th>
                             </tr>
                         </thead>
                         <tbody>';

        if ($leads->isEmpty()) {
            $html .= '<tr>
                                <td colspan="2" style="text-align:center; font-weight:600;">
                                    No company found
                                </td>
                            </tr>';
        } else {
            foreach ($leads as $lead) {
                $html .= '<tr>
                                    <td width="200">' . htmlspecialchars($lead->company_name) . '</td>
                                    <td width="200">
                                        <fieldset>
                                            <label class="custom-control custom-checkbox">
                                                <input type="checkbox" value="' . htmlspecialchars($lead->company_name) . '" name="test[]" class="custom-control-input">
                                                <span class="custom-control-label"></span>
                                            </label>
                                        </fieldset>
                                    </td>
                                </tr>';
            }
        }


        $html .= '</tbody>
                 </table>
             </div>';

        // Add source select dropdown BELOW the table
        $sourcediv = '<div class="form-group mt-3">
                     <label for="source_select">Select Transfer Source</label>
                     <select name="source_select" id="sourcelistid"  class="form-control" required>
                         <option value="" selected>-- Select Source --</option>';

        foreach ($source_list as $src) {
            $sourcediv .= '<option value="' . htmlspecialchars($src->id) . '">' . htmlspecialchars($src->source_name . ' - ' . $src->description) . '</option>';
        }

        $sourcediv .= '   </select>
                 </div>';


        echo json_encode(['status' => 200, 'html' => $html, 'sourcediv' => $sourcediv]);
        exit;

    }

    public function transferleads(Request $request)
    {
        if (isset($request->leads) && !empty($request->leads)) {
            foreach ($request->leads as $leads) {
                $sources = Lead::where('company_name', $leads)->where('source_id', $request->old_source_id)->update(['source_id' => $request->new_source_id]);

            }
        }
        $source = Source::where('id', $request->new_source_id)->first();
        $companynames = implode(', ', $request->leads);

        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->description = $companynames . ' companies are transfered to ' . $source->source_name . '-' . $source->description;
        $logs->type = 12;
        $logs->source_id = $request->new_source_id;
        $logs->save();
        echo json_encode(['status' => 200, 'message' => 'Leads Transfered']);
        exit;
    }

    public function assignLeadsToUser(Request $request)
    {
        if ($request->filled('leadsType')) {
            $sourceId = $request->input('sourceId');
            $userId = $request->input('userId');
            $companyName = $request->input('companyName');
            $leadsTypeString = $request->input('leadsType');
            $assignedUserId = $request->input('assignedUserId');
            $leadsType = explode(',', $leadsTypeString);
            $leadsToAssign = Lead::where([
                ['source_id', '=', $sourceId],
                ['company_name', '=', $companyName],
                ['asign_to', '=', $assignedUserId],
            ])->whereIn('status', $leadsType)->pluck('id');

            if ($leadsToAssign->isEmpty()) {
                return [
                    'status' => false,
                    'message' => 'No matching leads found to assign.'
                ];
            }

            // Assign request user to leads
            $result = Lead::whereIn('id', $leadsToAssign)->update(['asign_to' => $userId]);

            return [
                'status' => true,
                'message' => 'Leads assigned successfully.'
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Please select at least one lead type.',
            ];
        }
    }


    /**
     * @param mixed Request
     */
    public function getSourceAssignedUsers(Request $request)
    {
        $assignedLeadsUsers = Lead::where([
            ['source_id', "=", $request->sourceId],
            ['company_name', "=", $request->companyName],
            ['status', "<>", LEAD_STATUS_CLOSED],
        ])
            ->whereNotNull('asign_to')
            ->select('asign_to')
            ->groupBy('asign_to')
            ->with([
                'user' => function ($query) {
                    $query->select('id', DB::raw('CONCAT(`first_name`," ", `last_name`) as user_name'));
                }
            ])
            ->get();
        return view('sources.lead_assigned_users')->with(['assignedLeadsUsers' => $assignedLeadsUsers]);
    }



    public function camp_assign_emp($id)
    {
        return view('employeemodule.compaignlist');
    }

    public function camp_assign_list(Request $request)
    {
        $userId = Auth::id();

        // Optimized query: Only select necessary fields, reduce unnecessary joins
        $data = Lead::select('source_id', DB::raw('COUNT(*) as totalLeads'))
            ->where('asign_to', $userId)
            ->whereHas('source', function ($query) {
                $query->where('is_active', '1');
            })
            ->groupBy('source_id')
            ->with('source:id,source_name,description')  // Fetch only needed fields from the related 'source' table
            ->get();

        // Process the data
        $data->transform(function ($row) {
            $row->source_name = '<a href="' . url('campaign/camp_assign_emp/' . $row->source_id) . '" class="set_camp_id" target="_blank">
                                <span class="label" data-toggle="tooltip" data-placement="top" title="View Campaign" style="color:#000;font-size: 15px;">
                                    ' . htmlspecialchars(optional($row->source)->source_name) . '
                                </span>
                            </a>';
            $row->description = optional($row->source)->description;
            return $row;
        });

        // Use DataTables to send the response
        return DataTables::of($data)
            ->addColumn('source_name', function ($row) {
                return $row->source_name; // Return HTML string
            })
            ->addColumn('description', function ($row) {
                return $row->description;
            })
            ->addColumn('totalLeads', function ($row) {
                return $row->totalLeads;
            })
            ->rawColumns(['source_name']) // Enable raw HTML rendering for specific columns
            ->make(true);
    }


    public function view_camp($id)
    {
        // Check if the request is an AJAX call from DataTable
        if (request()->ajax()) {
            $query = Lead::with(['source', 'latestNote'])
                ->where('asign_to', auth()->user()->id)
                ->where('source_id', $id);

            if (!empty(request('cName'))) {
                $query->where('company_name', 'LIKE', '%' . request('cName') . '%');
            }

            if (!empty(request('timeZone'))) {
                $query->where('timezone', 'LIKE', '%' . request('timeZone') . '%');
            }


            // Apply search and filter conditions
            if (!empty(request('search')['value'])) {
                $search = trim(request('search')['value']);

                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('CONCAT(prospect_first_name, " ", prospect_last_name)'), 'LIKE', '%' . $search . '%')
                        ->orWhere('timezone', 'LIKE', '%' . $search . '%')
                        ->orWhere('designation', 'LIKE', '%' . $search . '%')
                        ->orWhere('contact_number_1', 'LIKE', '%' . $search . '%');
                });
            }


            return datatables()->of($query)
                ->addColumn('source_name', function ($row) {
                    return $row->source->name ?? 'N/A'; // Example: Adjust 'name' as per your source model
                })
                ->addColumn('action', function ($row) {
                    $notedetails = $row->latestNote;

                    $notesButton = '';

                    if ($notedetails) {

                        $createdAt = \Carbon\Carbon::parse($notedetails->created_at);

                        // check if note created within last 24 hours
                        if ($createdAt->diffInHours(now()) <= 24) {

                            $notesButton = '<a class="viewnotes shake-note" onclick="shownoteslist(' . $row->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye label-new" style="color:#8B8000 !important"></i>
                            </a>';

                        } else {

                            $notesButton = '<a class="viewnotes" onclick="shownoteslist(' . $row->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye label-new"></i>
                            </a>';

                        }
                    }
                    $quickNoteButton = '<a class="addnotes" onclick="showaddmodal(' . $row->id . ')" data-toggle="modal">
                                     <i class="fas fa-comment label-new" aria-hidden="true"></i>
                                 </a>';
                    $status = '<a class="changestatus" label-info" onclick="showstatusmodal(' . $row->id . ')" data-toggle="modal" data-target="#status-modal" style="color:black"><i class="fa fa-refresh label-new"></i></a>';
                    return $notesButton . ' ' . $quickNoteButton . ' ' . $status;


                })
                ->editColumn('prospect_first_name', function ($row) {
                    $leadName = '<a href="' . url('/leads', [$row->id]) . '" target="_blank">' . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';
                    $linkedinAddress = $row->linkedin_address ?? ''; // Ensure the variable exists
                    $linkedinIcon = '';

                    if (strpos($linkedinAddress, 'linkedin') === false) {
                        $linkedinIcon = '<a href="javascript:void(0)"><i style="color: #000" alt="LinkedIn" title="LinkedIn Address Not Valid" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    } else {
                        $linkedinUrl = strpos($linkedinAddress, 'http://') !== 0 && strpos($linkedinAddress, 'https://') !== 0
                            ? 'https://' . $linkedinAddress
                            : $linkedinAddress;
                        $linkedinIcon = '<a href="' . $linkedinUrl . '" target="_blank"><i alt="LinkedIn" title="LinkedIn" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    }

                    return $leadName . '  ' . $linkedinIcon;
                })

                ->addColumn('update_note_date', function ($row) {
                    return $row->note_created_date;
                })
                ->addColumn('note_created_date_new', function ($row) {

                    $notedetails = $row->latestNote;

                    if ($notedetails) {

                        $createdAt = \Carbon\Carbon::parse($notedetails->created_at);
                        return $createdAt;
                    } else {
                        return 'N/A';
                    }

                })
                ->editColumn('contact_number_1', function ($row) {
                    if (empty($row->contact_number_1)) {
                        return 'N/A';
                    }

                    // Split by '/-' if present, otherwise fall back to comma, semicolon, space
                    if (strpos($row->contact_number_1, '/-') !== false) {
                        $numbers = explode('/-', $row->contact_number_1);
                    } else {
                        $numbers = preg_split('/[,\s;]+/', $row->contact_number_1);
                    }
                    $numbers = array_map('trim', $numbers);
                    
                    // Remove all hyphens from each number
                    $numbers = array_map(function ($num) {
                        return str_replace('-', '', $num);
                    }, $numbers);

                    // Filter out any elements that do not contain a digit (e.g. only '-', '/' etc.)
                    $numbers = array_values(array_filter($numbers, function ($num) {
                        return preg_match('/\d/', $num);
                    }));

                    $count = count($numbers);

                    if ($count == 0) {
                        return 'N/A';
                    }

                    $firstNumber = $numbers[0];
                    $contact = json_encode(implode(', ', $numbers));
                    $badgeText = $count <= 1 ? 'Dial' : '+ show more';
    
                    return "{$firstNumber} 
            <span class='badge' 
                  style='cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px;' 
                  onclick='showAllNumbers({$contact}, {$row->id})'>
                  {$badgeText}
            </span>";
                })
                ->editColumn('contact_number_2', function ($row) {
                    if (empty($row->contact_number_2)) {
                        return 'N/A';
                    }

                    // Split by '/-' if present, otherwise fall back to comma, semicolon, space
                    if (strpos($row->contact_number_2, '/-') !== false) {
                        $numbers = explode('/-', $row->contact_number_2);
                    } else {
                        $numbers = preg_split('/[,\s;]+/', $row->contact_number_2);
                    }
                    $numbers = array_map('trim', $numbers);
                    
                    // Remove all hyphens from each number
                    $numbers = array_map(function ($num) {
                        return str_replace('-', '', $num);
                    }, $numbers);

                    // Filter out any elements that do not contain a digit (e.g. only '-', '/' etc.)
                    $numbers = array_values(array_filter($numbers, function ($num) {
                        return preg_match('/\d/', $num);
                    }));

                    $count = count($numbers);

                    if ($count == 0) {
                        return 'N/A';
                    }

                    $firstNumber = $numbers[0];
                    $contact = json_encode(implode(', ', $numbers));
                    $badgeText = $count <= 1 ? 'Dial' : '+ show more';
    
                    return "{$firstNumber} 
            <span class='badge' 
                  style='cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px;' 
                  onclick='showAllNumbers({$contact}, {$row->id})'>
                  {$badgeText}
            </span>";
                })
                ->rawColumns(['action', 'prospect_first_name', 'contact_number_1', 'contact_number_2']) // To render HTML in the actions column
                ->make(true);
        }

        $comapnyName = Lead::whereIn('status', [1, 4])
            ->where('asign_to', auth()->user()->id)
            ->where('source_id', $id)
            ->select('company_name')
            ->orderBy('company_name', 'asc')
            ->groupBy('company_name')
            ->get();

        $timeZone = Lead::where(['status' => '1', 'asign_to' => auth()->user()->id, 'source_id' => $id])
            ->select('timezone')
            ->orderBy('timezone', 'asc')
            ->groupBy('timezone')
            ->get();
        $source = Source::where('id', $id)->first();

        return view('leads.campaignlisting', compact('id', 'comapnyName', 'timeZone', 'source'));

    }
    public function employeeclosedleads(Request $request)
    {
        if (auth()->user()->is_admin == null) {
            $employee_ids = User::where(function ($query) {
                $query->whereIn('is_admin', ['1', '3']);
            })
                ->where('is_active', 1)
                ->pluck('id');
        } else {
            $employee_ids = User::where(function ($query) {
                // 1️⃣ Tumhare direct employees aur submanagers
                $query->where('user_id', auth()->id())
                    ->whereIn('is_admin', ['1', '3']);
            })
                ->orWhereIn('user_id', function ($subquery) {
                    // 2️⃣ Submanagers ke under wale employees
                    $subquery->select('id')
                        ->from('users')
                        ->where('user_id', auth()->id())
                        ->where('is_admin', '3');
                })
                ->where('is_active', 1)
                ->pluck('id');
        }

        $comapnyName = Lead::select('company_name')
            ->where('status', 3)
            ->whereIn('asign_to', $employee_ids)
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->distinct()
            ->orderBy('company_name', 'asc')
            ->get();

        $sourceIds = Lead::where('status', 3)
            ->whereIn('asign_to', $employee_ids)
            ->whereNotNull('source_id')
            ->where('source_id', '!=', '')
            ->distinct()
            ->pluck('source_id')
            ->toArray();

        $sourceNames = Source::whereIn('id', $sourceIds)
            ->select('source_name')
            ->distinct()
            ->orderBy('source_name')
            ->get();

        $timeZone = Lead::select('timezone')->where(['status' => 3])
            ->whereIn('asign_to', $employee_ids)
            ->whereNotNull('timezone')
            ->where('timezone', '!=', '')
            ->distinct()
            ->orderBy('timezone', 'asc')
            ->get();

        $closedon = Lead::join('lhs_report', 'lhs_report.lead_id', 'leads.id')->where(['leads.status' => 3])
            ->whereIn('leads.asign_to', $employee_ids)
            ->selectRaw('DATE(lhs_report.created_at) as date')  // Extract the date part
            ->distinct()  // Ensure distinct dates
            ->orderBy('date', 'DESC')  // Order by the extracted date
            ->get()
            ->toArray();
        // Check if the request is an AJAX call from DataTable
        if (request()->ajax()) {
            $query = Lead::with(['user', 'lhsreport'])->select('leads.*', 'sources.source_name', 'sources.description')->join('sources', 'leads.source_id', '=', 'sources.id')
                ->where('status', 3)->whereIn('asign_to', $employee_ids);
            if (!empty(request('cName'))) {
                $query->where('company_name', '=', request('cName'));
            }

            if (!empty(request('timeZone'))) {
                $query->where('timezone', '=', request('timeZone'));
            }

            if (!empty(request('campaign_name'))) {
                $query->where('sources.source_name', '=', request('campaign_name'));
            }

            if (!empty(request('closedon'))) {
                // Ensure 'closedon' is in a valid format (Y-m-d) and match only the date part of updated_at
                // $query->whereRaw('DATE(leads.updated_at) = ?', [request('closedon')]);
                $query->join('lhs_report', 'lhs_report.lead_id', '=', 'leads.id')
                    ->whereDate('lhs_report.created_at', request('closedon'))
                    ->groupBy('leads.id');


            }

            if (!empty(request('invitation_date'))) {
                $query->whereDate('invitation_date', request('invitation_date'));
            }

            if (!empty(request('confirmation_status'))) {
                if (request('confirmation_status') == 'sent') {
                    $query->whereNotNull('invitation_date');
                } elseif (request('confirmation_status') == 'waiting') {
                    $query->whereNull('invitation_date');
                }
            }

            if (!empty(request('date_from')) && !empty(request('date_to'))) {
                $query->where(function ($q) {
                    $q->whereBetween('leads.closed_on', [request('date_from') . ' 00:00:00', request('date_to') . ' 23:59:59'])
                        ->orWhere(function ($sub) {
                            $sub->whereNull('leads.closed_on')
                                ->whereBetween('leads.updated_at', [request('date_from') . ' 00:00:00', request('date_to') . ' 23:59:59']);
                        });
                });
            }
            $columnIndex = $request->input('order.0.column'); // this will be 9
            $direction = $request->input('order.0.dir');      // this will be 'desc'

            if (!is_null($columnIndex) && $columnIndex == 9 && $direction == 'desc') {
                $query->orderbyDesc('leads.closed_on')->orderByDesc('leads.updated_at');

            }

            if (!is_null($columnIndex) && $columnIndex == 9 && $direction == 'asc') {
                $query->orderBy('leads.closed_on')->orderBy('leads.updated_at');

            }






            // Apply search and filter conditions
            $search = $request->input('search.value');

            if (!empty($search)) {

                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('CONCAT(prospect_first_name, " ", prospect_last_name)'), 'LIKE', '%' . $search . '%')
                        ->orWhere('timezone', 'LIKE', '%' . $search . '%')
                        ->orWhere('designation', 'LIKE', '%' . $search . '%')
                        ->orWhere('contact_number_1', 'LIKE', '%' . $search . '%')
                        ->orWhere('sources.description', 'LIKE', '%' . $search . '%')
                        ->orWhere('sources.source_name', 'LIKE', '%' . $search . '%');
                });
            }


            $userPermissions = null;
            if (Auth::user()->is_admin == SUBMANAGER) {
                $permissions = SubmanagerPermissions::where('user_id', Auth::id())->first();
                if ($permissions) {
                    $userPermissions = json_decode($permissions->user_permissions);
                }
            }

            return datatables()->of($query)
                ->addColumn('updated_at_new', function ($row) {
                    // if (!empty($row->closed_on)) {
                    //     return date('d/m/Y', strtotime($row->closed_on));
                    // } 
                    $checklhs = $row->lhsreport ? $row->lhsreport->created_at : null;

                    if ($checklhs) {
                        return \Carbon\Carbon::parse($checklhs)->format('d/m/Y');
                    } else {
                        return date('d/m/Y', strtotime($row->updated_at));

                    }
                })
                ->editColumn('contact_number_1', function ($row) {
                    if (empty($row->contact_number_1)) {
                        return 'N/A';
                    }

                    // Split by '/-' if present, otherwise fall back to comma, semicolon, space
                    if (strpos($row->contact_number_1, '/-') !== false) {
                        $numbers = explode('/-', $row->contact_number_1);
                    } else {
                        $numbers = preg_split('/[,\s;]+/', $row->contact_number_1);
                    }
                    $numbers = array_map('trim', $numbers);
                    
                    // Remove all hyphens from each number
                    $numbers = array_map(function ($num) {
                        return str_replace('-', '', $num);
                    }, $numbers);

                    // Filter out any elements that do not contain a digit (e.g. only '-', '/' etc.)
                    $numbers = array_values(array_filter($numbers, function ($num) {
                        return preg_match('/\d/', $num);
                    }));

                    $count = count($numbers);

                    if ($count == 0) {
                        return 'N/A';
                    }

                    $firstNumber = $numbers[0];
                    if ($count <= 1) {
                        return $firstNumber;
                    }

                    $contact = json_encode(implode(', ', $numbers));
                    // Pass the rest of the numbers as a JSON array to the JS function
    
                    return "{$firstNumber} 
            <span class='badge' 
                  style='cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px;' 
                  onclick='showAllNumbers({$contact})'>
                  + show more
            </span>";
                })

                ->addColumn('action', function ($row) {

                    $notesButton = '';

                    // 📝 Notes
                    $notesButton .= '
                        <a onclick="shownoteslist(' . $row->id . ')" class="notes_id">
                            <i class="fas fa-comment label-new" data-tippy-content="Notes"></i>
                        </a>';

                    // 👁 View
                    $notesButton .= '
                        <a href="' . url('leads', $row->id) . '" class="notes_id">
                            <i class="fas fa-eye label-new" data-tippy-content="View Lead"></i>
                        </a>';

                    // ⬇ Download (only if status = 3)
                    if ($row->status == 3) {
                        $notesButton .= '
                            <a href="' . url('employee/export/' . $row->id . '/word_single_down') . '?employee_id=&campaign_id=&date_from=&date_to=" class="notes_id">
                                <i class="fa fa-arrow-down label-new" style="color:green" data-tippy-content="Download"></i>
                            </a>';
                    }

                    // ✏ Edit permission
                    if (Auth::user()->is_admin == SUBMANAGER) {
                        if ($userPermissions && !empty($userPermissions->lead->edit) && $userPermissions->lead->edit == 1) {
                            $notesButton .= '
                                <a href="' . url('editlead', $row->id) . '" class="notes_id">
                                    <i class="fas fa-edit label-new" data-tippy-content="Edit Lead"></i>
                                </a>';
                        }
                    } else {
                        $notesButton .= '
                            <a href="' . url('editlead', $row->id) . '" class="notes_id">
                                <i class="fas fa-edit label-new" data-tippy-content="Edit Lead"></i>
                            </a>';
                    }

                    // 🗑 Delete permission
                    if (Auth::user()->is_admin == SUBMANAGER) {
                        if ($userPermissions && !empty($userPermissions->lead->delete) && $userPermissions->lead->delete == 1) {
                            $notesButton .= '
                                <a href="#"
                                   class="notes_id"
                                   onclick="deleteLead(' . $row->id . ')">
                                    <i class="fa fa-trash label-new" style="color:red;font-size:15px"
                                       data-tippy-content="Delete Lead"></i>
                                </a>';
                        }
                    } else {
                        $notesButton .= '
                            <a href="#"
                               class="notes_id"
                               onclick="deleteLead(' . $row->id . ')">
                               <i class="fa fa-trash label-new" style="color:red;font-size:15px"
                                   data-tippy-content="Delete Lead"></i>
                            </a>';
                    }

                    return $notesButton;
                })

                ->editColumn('prospect_first_name_new', function ($row) {
                    $leadName = '<a href="' . url('/leads', [$row->id]) . '" target="_blank">' . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';
                    $linkedinAddress = $row->linkedin_address ?? ''; // Ensure the variable exists
                    $linkedinIcon = '';

                    if (strpos($linkedinAddress, 'linkedin') === false) {
                        $linkedinIcon = '<a href="javascript:void(0)"><i style="color: #000" alt="LinkedIn" title="LinkedIn Address Not Valid" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    } else {
                        $linkedinUrl = strpos($linkedinAddress, 'http://') !== 0 && strpos($linkedinAddress, 'https://') !== 0
                            ? 'https://' . $linkedinAddress
                            : $linkedinAddress;
                        $linkedinIcon = '<a href="' . $linkedinUrl . '" target="_blank"><i alt="LinkedIn" title="LinkedIn" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    }

                    return $leadName . '  ' . $linkedinIcon;
                })

                ->addColumn('update_note_date', function ($row) {
                    return 'N/A';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return 'Pending';
                    } elseif ($row->status == 2) {
                        return 'Failed';
                    } else {
                        return 'Closed';
                    }
                })
                ->addColumn('closed_by', function ($row) {
                    $employeedetails = $row->user;
                    return $employeedetails ? ($employeedetails->first_name . ' ' . $employeedetails->last_name) : 'N/A';
                })
                ->addColumn('confirmation_status', function ($row) {
                    if (!empty($row->invitation_date)) {
                        return '<span class="badge bg-success">Confirmed</span>';
                    } else {
                        $colorClass = 'bg-warning text-dark';
                        if ($row->lhs_sent_at) {
                            $lhsSentAt = \Carbon\Carbon::parse($row->lhs_sent_at);
                            if ($lhsSentAt->diffInHours(now()) >= 48) {
                                $colorClass = 'bg-danger text-white';
                            }
                        }
                        return '<span class="badge ' . $colorClass . '">Waiting for Confirmation</span>';
                    }
                })
                ->addColumn('reminder_status', function ($row) {
                    if (!empty($row->invitation_date)) {
                        return 'N/A';
                    }

                    if ($row->lhs_sent_at) {
                        $lhsSentAt = \Carbon\Carbon::parse($row->lhs_sent_at);

                        // If LHS sent less than 24 hours ago and no reminder sent yet
                        if (!$row->lhs_reminder_sent_at && $lhsSentAt->diffInHours(now()) < 24) {
                            return '<span class="badge bg-secondary">LHS Sent (Wait 24h)</span>';
                        }

                        if ($row->lhs_reminder_sent_at) {
                            $reminderSentAt = \Carbon\Carbon::parse($row->lhs_reminder_sent_at);

                            // If reminder sent more than 24 hours ago, show RED button
                            if ($reminderSentAt->diffInHours(now()) >= 24) {
                                return '<button class="btn btn-sm btn-danger send-lhs-reminder" data-id="' . $row->id . '">
                                            Send Reminder
                                        </button>
                                        <br><small class="text-muted">Last sent: ' . $reminderSentAt->format('d/m/Y H:i') . '</small>';
                            } else {
                                // Show last sent time
                                return '<span class="badge bg-success">Reminder Sent on ' . $reminderSentAt->format('d/m/Y H:i') . '</span>';
                            }
                        }

                        // Default: Show Blue button (LHS sent > 24h and no reminder sent yet)
                        return '<button class="btn btn-sm btn-primary send-lhs-reminder" data-id="' . $row->id . '">
                                    Send Reminder
                                </button>';
                    } else {
                        return 'N/A';
                    }
                })
                ->addColumn('send_lhs', function ($row) {
                    if ($row->lhs_sent_at) {
                        return \Carbon\Carbon::parse($row->lhs_sent_at)->format('d/m/Y H:i');
                    } else {
                        return '<button class="btn btn-sm btn-info send-lhs" data-id="' . $row->id . '">
                                    Send LHS
                                </button>';
                    }
                })
                ->addColumn('invitation_date', function ($row) {
                    if (isset($row->invitation_date) && !empty($row->invitation_date)) {
                        return '<span class="badge bg-success">' . \Carbon\Carbon::parse($row->invitation_date)->format('d M, Y h:i A') . '</span>';
                    } else {
                        $disabled = $row->lhs_sent_at ? '' : 'disabled';
                        return '<button class="btn btn-sm btn-secondary add-invitation-date" data-id="' . $row->id . '" ' . $disabled . '>
                                    Add Invitation Date
                                </button>';
                    }

                })


                ->rawColumns(['action', 'prospect_first_name_new', 'contact_number_1', 'reminder_status', 'confirmation_status', 'send_lhs', 'invitation_date']) // To render HTML in the actions column
                ->make(true);
        }
        $id = '';
        return view('leads.employeeclosedleads', compact('id', 'comapnyName', 'timeZone', 'sourceNames', 'closedon'));

    }

    // public function employeeclosedleads(Request $request)
    // {
    //     /* ================= EMPLOYEE IDS ================= */
    //     $employee_ids = User::where(function ($query) {
    //             $query->where('user_id', auth()->id())
    //                   ->whereIn('is_admin', ['1', '3']);
    //         })
    //         ->orWhereIn('user_id', function ($subquery) {
    //             $subquery->select('id')
    //                 ->from('users')
    //                 ->where('user_id', auth()->id())
    //                 ->where('is_admin', '3');
    //         })
    //         ->where('is_active', 1)
    //         ->pluck('id');

    //     /* ================= FILTER DATA ================= */
    //     $comapnyName = Lead::where('status', 3)
    //         ->whereIn('asign_to', $employee_ids)
    //         ->groupBy('company_name')
    //         ->orderBy('company_name')
    //         ->get();

    //     $sourceNames = Source::select('sources.source_name', 'sources.description')
    //         ->join('leads', 'leads.source_id', '=', 'sources.id')
    //         ->where('leads.status', 3)
    //         ->whereIn('leads.asign_to', $employee_ids)
    //         ->groupBy('sources.source_name')
    //         ->orderBy('sources.source_name')
    //         ->get();

    //     $timeZone = Lead::where('status', 3)
    //         ->whereIn('asign_to', $employee_ids)
    //         ->groupBy('timezone')
    //         ->orderBy('timezone')
    //         ->get();

    //     $closedon = Lead::join('lhs_report', 'lhs_report.lead_id', 'leads.id')
    //         ->where('leads.status', 3)
    //         ->whereIn('leads.asign_to', $employee_ids)
    //         ->selectRaw('DATE(lhs_report.created_at) as date')
    //         ->distinct()
    //         ->orderBy('date', 'DESC')
    //         ->get()
    //         ->toArray();

    //     /* ================= DATATABLE AJAX ================= */
    //     if ($request->ajax()) {

    //         $query = Lead::select(
    //                 'leads.*',
    //                 'sources.source_name',
    //                 'sources.description'
    //             )
    //             ->join('sources', 'sources.id', '=', 'leads.source_id')
    //             ->where('leads.status', 3)
    //             ->whereIn('leads.asign_to', $employee_ids);

    //         /* ===== CUSTOM FILTERS ===== */
    //         if ($request->filled('cName')) {
    //             $query->where('company_name', 'LIKE', '%' . $request->cName . '%');
    //         }

    //         if ($request->filled('timeZone')) {
    //             $query->where('timezone', 'LIKE', '%' . $request->timeZone . '%');
    //         }

    //         if ($request->filled('campaign_name')) {
    //             $query->where('sources.source_name', 'LIKE', '%' . $request->campaign_name . '%');
    //         }

    //         if ($request->filled('closedon')) {
    //             $query->join('lhs_report', 'lhs_report.lead_id', '=', 'leads.id')
    //                   ->whereDate('lhs_report.created_at', $request->closedon)
    //                   ->groupBy('leads.id');
    //         }

    //         /* ===== GLOBAL SEARCH (DataTables) ===== */
    //         $searchValue = $request->input('search.value');

    //         if (!empty($searchValue)) {
    //             $query->where(function ($q) use ($searchValue) {
    //                 $q->where('company_name', 'LIKE', "%{$searchValue}%")
    //                   ->orWhere('prospect_first_name', 'LIKE', "%{$searchValue}%")
    //                   ->orWhere('prospect_last_name', 'LIKE', "%{$searchValue}%")
    //                   ->orWhere(DB::raw('CONCAT(prospect_first_name," ",prospect_last_name)'), 'LIKE', "%{$searchValue}%")
    //                   ->orWhere('timezone', 'LIKE', "%{$searchValue}%")
    //                   ->orWhere('designation', 'LIKE', "%{$searchValue}%")
    //                   ->orWhere('contact_number_1', 'LIKE', "%{$searchValue}%");
    //             });
    //         }

    //         /* ===== ORDERING FIX ===== */
    //         $columnIndex = $request->input('order.0.column');
    //         $direction   = $request->input('order.0.dir', 'desc');

    //         $columns = [
    //             0 => 'linkedin_address',
    //             1 => 'employee_name',
    //             2 => 'company_name',
    //             3 => 'prospect_first_name',
    //             4 => 'designation',
    //             5 => 'leads.created_at',
    //             6 => 'sources.source_name',
    //         ];

    //         if (isset($columns[$columnIndex])) {
    //             $query->orderBy($columns[$columnIndex], $direction);
    //         } else {
    //             $query->orderByDesc('leads.updated_at');
    //         }

    //         /* ===== DATATABLE RESPONSE ===== */
    //         return datatables()->of($query)

    //             ->addColumn('updated_at_new', function ($row) {
    //                 $checklhs = LhsReport::where('lead_id', $row->id)->value('created_at');
    //                 return $checklhs
    //                     ? \Carbon\Carbon::parse($checklhs)->format('d/m/Y')
    //                     : date('d/m/Y', strtotime($row->updated_at));
    //             })

    //             ->editColumn('prospect_first_name_new', function ($row) {
    //                 $name = '<a href="' . url('/leads/' . $row->id) . '" target="_blank">'
    //                       . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';

    //                 if ($row->linkedin_address && str_contains($row->linkedin_address, 'linkedin')) {
    //                     $url = str_starts_with($row->linkedin_address, 'http')
    //                         ? $row->linkedin_address
    //                         : 'https://' . $row->linkedin_address;

    //                     $name .= ' <a href="' . $url . '" target="_blank">
    //                                 <i class="fa-brands fa-linkedin"></i>
    //                               </a>';
    //                 }

    //                 return $name;
    //             })

    //             ->addColumn('status', fn($row) => 'Closed')

    //             ->addColumn('closed_by', function ($row) {
    //                 $emp = User::find($row->asign_to);
    //                 return $emp ? $emp->first_name . ' ' . $emp->last_name : '';
    //             })

    //          ->addColumn('action', function ($row) { $notesButton = '<a onclick="shownoteslist(' . $row->id . ')" class="notes_id" data-toggle="modal" data-target="#largeModal"> <i class="fas fa-comment label-new" aria-hidden="true"></i> </a>'; $notesButton .= '<a href="' . url('leads', $row->id) . '" class="notes_id" data-toggle="modal" data-target="#largeModal"> <i class="fas fa-eye label-new" aria-hidden="true"></i> </a>'; if ($row->status == 3) { $notesButton .= '<a href="' . url('employee/export/' . $row->id . '/word_single_down') . '?employee_id=&campaign_id=&date_from=&date_to=" class="notes_id"> <i class="fa fa-arrow-down label-new" style="color:green"> </i> </a>'; } if (Auth::user()->is_admin == SUBMANAGER) { $permissions = SubmanagerPermissions::where('user_id', Auth::id())->first(); if ($permissions) { $userPermissions = json_decode($permissions->user_permissions); if (!empty($userPermissions->lead->edit) && $userPermissions->lead->edit == 1) { $notesButton .= '<a href="' . url('editlead', $row->id) . '" class="notes_id" data-toggle="modal" data-target="#largeModal"> <i class="fas fa-edit label-new" aria-hidden="true"></i> </a>'; } } } else { $notesButton .= '<a href="' . url('editlead', $row->id) . '" class="notes_id" data-toggle="modal" data-target="#largeModal"> <i class="fas fa-edit label-new" aria-hidden="true"></i> </a>'; } if (Auth::user()->is_admin == SUBMANAGER) { $permissions = SubmanagerPermissions::where('user_id', Auth::id())->first(); if ($permissions) { $userPermissions = json_decode($permissions->user_permissions); if (!empty($userPermissions->lead->delete) && $userPermissions->lead->delete == 1) { $notesButton .= '<a href="' . url('leads/delete', ['id' => $row->id]) . '" class="notes_id" data-toggle="tooltip" data-placement="top" title="Delete" style="color:red;font-size: 15px;" onclick="return confirm(\'Are you sure you want to delete this lead ?\')"> <i class="fa fa-trash" aria-hidden="true"></i> </a>'; } } } else { $notesButton .= '<a href="' . url('leads/delete', ['id' => $row->id]) . '" class="notes_id" data-toggle="tooltip" data-placement="top" title="Delete" style="color:red;font-size: 15px;" onclick="return confirm(\'Are you sure you want to delete this lead ?\')"> <i class="fa fa-trash" aria-hidden="true"></i> </a>'; } return $notesButton; })

    //             ->rawColumns(['action', 'prospect_first_name_new'])
    //             ->make(true);
    //     }

    //     $id = '';
    //     return view('leads.employeeclosedleads', compact(
    //         'id',
    //         'comapnyName',
    //         'timeZone',
    //         'sourceNames',
    //         'closedon'
    //     ));
    // }
    public function meeting_scheduled(Request $request)
    {
        if (auth::user()->is_admin == null) {
            $employee_ids = User::where(function ($query) {
                $query->whereIn('is_admin', ['1', '3']);
            })
                ->orWhereIn('user_id', function ($subquery) {
                    $subquery->select('id')
                        ->from('users')
                        ->where('user_id', auth()->id())
                        ->where('is_admin', '3');
                })
                ->where('is_active', 1)
                ->pluck('id');

            $comapnyName = Lead::with('source')
                ->whereNotNull('invitation_date')
                ->whereIn('asign_to', $employee_ids)
                ->groupBy('company_name')
                ->orderBy('company_name', 'asc')
                ->get();

            $sourceNames = Source::select('sources.source_name', 'sources.description')
                ->join('leads', 'leads.source_id', '=', 'sources.id')
                ->whereNotNull('leads.invitation_date')
                ->whereIn('leads.asign_to', $employee_ids)
                ->groupBy('sources.source_name')
                ->orderBy('sources.source_name')
                ->get();

        } else {

            $employee_ids = User::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->whereIn('is_admin', ['1', '3']);
            })
                ->orWhereIn('user_id', function ($subquery) {
                    $subquery->select('id')
                        ->from('users')
                        ->where('user_id', auth()->id())
                        ->where('is_admin', '3');
                })
                ->where('is_active', 1)
                ->pluck('id');

            $comapnyName = Lead::with('source')
                ->whereNotNull('invitation_date')
                ->whereIn('asign_to', $employee_ids)
                ->groupBy('company_name')
                ->orderBy('company_name', 'asc')
                ->get();

            $sourceNames = Source::select('sources.source_name', 'sources.description')
                ->join('leads', 'leads.source_id', '=', 'sources.id')
                ->whereNotNull('leads.invitation_date')
                ->whereIn('leads.asign_to', $employee_ids)
                ->groupBy('sources.source_name')
                ->orderBy('sources.source_name')
                ->get();

        }

        if (request()->ajax()) {
            $query = Lead::select('leads.*', 'sources.source_name', 'sources.description')
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->whereNotNull('invitation_date')
                ->whereIn('asign_to', $employee_ids);

            if (!empty(request('cName'))) {
                $query->where('company_name', 'LIKE', '%' . request('cName') . '%');
            }

            if (!empty(request('campaign_name'))) {
                $query->whereHas('source', function ($q) use ($request) {
                    $q->where('source_name', 'LIKE', '%' . request('campaign_name') . '%');
                });
            }

            if (!empty(request('invitation_from'))) {
                $query->whereDate('invitation_date', '>=', request('invitation_from'));
            }

            if (!empty(request('invitation_to'))) {
                $query->whereDate('invitation_date', '<=', request('invitation_to'));
            }

            if (request('meeting_status') == 'Failed') {
                $query->where(function ($q) {
                    $q->where('meeting_status', 'Failed')
                        ->orWhere('status', '2');
                });
            } elseif (request('meeting_status') == 'Done') {
                $query->where(function ($q) {
                    $q->where('meeting_status', 'Done')
                        ->orWhere(function ($sq) {
                            $sq->where('status', '5')->has('momReport');
                        });
                });
            } elseif (!empty(request('meeting_status'))) {
                $query->where('meeting_status', request('meeting_status'));
            } else {
                $query->where(function ($q) {
                    $q->whereNull('meeting_status')
                        ->orWhere('meeting_status', '')
                        ->orWhereIn('meeting_status', ['Pending', 'Rescheduled']);
                });
            }

            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('CONCAT(prospect_first_name, " ", prospect_last_name)'), 'LIKE', '%' . $search . '%')
                        ->orWhere('contact_number_1', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_email', 'LIKE', '%' . $search . '%');
                });
            }

            return datatables()->of($query)
                ->editColumn('prospect_first_name_new', function ($row) {
                    $leadName = '<a href="' . url('/leads', [$row->id]) . '" target="_blank">' . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';
                    $linkedinAddress = $row->linkedin_address ?? ''; // Ensure the variable exists
                    $linkedinIcon = '';

                    if (strpos($linkedinAddress, 'linkedin') === false) {
                        $linkedinIcon = '<a href="javascript:void(0)"><i style="color: #000" alt="LinkedIn" title="LinkedIn Address Not Valid" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    } else {
                        $linkedinUrl = strpos($linkedinAddress, 'http://') !== 0 && strpos($linkedinAddress, 'https://') !== 0
                            ? 'https://' . $linkedinAddress
                            : $linkedinAddress;
                        $linkedinIcon = '<a href="' . $linkedinUrl . '" target="_blank"><i alt="LinkedIn" title="LinkedIn" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    }

                    return $leadName . '  ' . $linkedinIcon;
                })
                ->editColumn('invitation_date', function ($row) {
                    return \Carbon\Carbon::parse($row->invitation_date)->format('d M, Y h:i A');
                })
                ->editColumn('meeting_status', function ($row) {
                    if (empty($row->meeting_status) || $row->meeting_status == 'Pending') {
                        if (auth::user()->is_admin == null) {
                            return '<span class="badge bg-light text-dark">N/A</span>';
                        } else {


                            return '<button class="btn btn-sm btn-primary" onclick="updateMeeting(' . $row->id . ')">Update Meeting</button>';
                        }
                    }
                    if ($row->meeting_status == 'Done')
                        return '<span class="badge bg-success">Meeting Happened</span>';
                    if ($row->meeting_status == 'Failed')
                        return '<span class="badge bg-danger">Meeting Not Happened</span>';
                    if ($row->meeting_status == 'Rescheduled')
                        return '<span class="badge bg-warning">Meeting Rescheduled</span>';

                    return '<button class="btn btn-sm btn-primary" onclick="updateMeeting(' . $row->id . ')">Update Meeting</button>';
                })
                ->addColumn('comments', function ($row) {
                    if (empty($row->meeting_status) || $row->meeting_status == 'Pending')
                        return 'N/A';
                    return $row->meeting_failed_reason ? htmlspecialchars($row->meeting_failed_reason) : 'N/A';
                })
                ->rawColumns(['prospect_first_name_new', 'meeting_status'])
                ->make(true);
        }
        $pendingLeads = [];
        if (!request()->ajax()) {
            $pendingLeads = Lead::whereIn('asign_to', $employee_ids)
                ->whereNotNull('invitation_date')
                ->where('invitation_date', '<=', now())
                ->where(function ($q) {
                    $q->whereNull('meeting_status')
                        ->orWhere('meeting_status', '')
                        ->orWhere('meeting_status', 'Pending');
                })
                ->get();
        }

        $id = '';
        return view('leads.meeting_scheduled', compact('id', 'comapnyName', 'sourceNames', 'pendingLeads'));
    }

    public function update_meeting_status(Request $request)
    {
        $lead = Lead::find($request->id);
        if (!$lead)
            return response()->json(['error' => 'Lead not found']);

        $lead->meeting_status = $request->status;
        if ($request->status == 'Failed') {
            $lead->meeting_failed_reason = $request->reason;
        } else if ($request->status == 'Rescheduled') {
            $datetime = $request->reschedule_date . ' ' . $request->reschedule_time;
            $previous_date = $lead->invitation_date;
            $lead->invitation_date = \Carbon\Carbon::parse($datetime)->format('Y-m-d H:i:s');
            $lead->meeting_failed_reason = 'Meeting is rescheduled. Previous invitation date time: ' . \Carbon\Carbon::parse($previous_date)->format('d M, Y h:i A');
        } else if ($request->status == 'Done') {
            $lead->meeting_failed_reason = 'N/A';
            $lead->status = LEAD_STATUS_COMPLETED;
        }

        $lead->save();

        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->type = 20;
        $logs->reference_id = $lead->id;
        $logs->source_id = $lead->source_id;
        $logs->description = 'Meeting status updated to ' . $request->status . ' for lead - ' . $lead->prospect_first_name . ' ' . $lead->prospect_last_name;
        $logs->save();

        return response()->json(['success' => 'Meeting status updated successfully']);
    }

    public function employeecompletedleads(Request $request)
    {
        if (auth()->user()->is_admin == null) {
            $employee_ids = User::where(function ($query) {
                $query->whereIn('is_admin', ['1', '3']);
            })
                ->where('is_active', 1)
                ->pluck('id');
        } else {
            $employee_ids = User::where(function ($query) {
                // 1️⃣ Tumhare direct employees aur submanagers
                $query->where('user_id', auth()->id())
                    ->whereIn('is_admin', ['1', '3']);
            })
                ->orWhereIn('user_id', function ($subquery) {
                    // 2️⃣ Submanagers ke under wale employees
                    $subquery->select('id')
                        ->from('users')
                        ->where('user_id', auth()->id())
                        ->where('is_admin', '3');
                })
                ->where('is_active', 1)
                ->pluck('id');
        }
        $comapnyName = Lead::select('company_name')
            ->where('status', 5)
            ->whereIn('asign_to', $employee_ids)
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->distinct()
            ->orderBy('company_name', 'asc')
            ->get();

        $sourceIds = Lead::where('status', 5)
            ->whereIn('asign_to', $employee_ids)
            ->whereNotNull('source_id')
            ->where('source_id', '!=', '')
            ->distinct()
            ->pluck('source_id')
            ->toArray();

        $sourceNames = Source::whereIn('id', $sourceIds)
            ->select('source_name')
            ->distinct()
            ->orderBy('source_name')
            ->get();

        $timeZone = Lead::select('timezone')->where(['status' => 5])
            ->whereIn('asign_to', $employee_ids)
            ->whereNotNull('timezone')
            ->where('timezone', '!=', '')
            ->distinct()
            ->orderBy('timezone', 'asc')
            ->get();

        $closedon = Lead::where(['status' => 5])
            ->whereIn('asign_to', $employee_ids)
            ->selectRaw('DATE(updated_at) as date')  // Extract the date part
            ->distinct()  // Ensure distinct dates
            ->orderBy('date', 'DESC')  // Order by the extracted date
            ->get()
            ->toArray();
        // Check if the request is an AJAX call from DataTable
        if (request()->ajax()) {
            $query = Lead::with(['user', 'momReport'])->select('leads.*', 'sources.source_name', 'sources.description')->join('sources', 'leads.source_id', '=', 'sources.id')
                ->where('status', 5)->whereIn('asign_to', $employee_ids);
            if (!empty(request('cName'))) {
                $query->where('company_name', '=', request('cName'));
            }

            if (!empty(request('timeZone'))) {
                $query->where('timezone', '=', request('timeZone'));
            }

            if (!empty(request('campaign_name'))) {
                $query->where('sources.source_name', '=', request('campaign_name'));
            }

            if (!empty(request('closedon'))) {
                $query->whereDate('leads.updated_at', request('closedon'));
            }

            if (!empty(request('date_from')) && !empty(request('date_to'))) {
                $query->where(function ($q) {
                    $q->whereBetween('leads.closed_on', [request('date_from') . ' 00:00:00', request('date_to') . ' 23:59:59'])
                        ->orWhere(function ($sub) {
                            $sub->whereNull('leads.closed_on')
                                ->whereBetween('leads.updated_at', [request('date_from') . ' 00:00:00', request('date_to') . ' 23:59:59']);
                        });
                });
            }
            $columnIndex = $request->input('order.0.column'); // this will be 9
            $direction = $request->input('order.0.dir');      // this will be 'desc'

            if (!is_null($columnIndex) && $columnIndex == 9 && $direction == 'desc') {
                $query->orderbyDesc('leads.closed_on')->orderByDesc('leads.updated_at');

            }

            if (!is_null($columnIndex) && $columnIndex == 9 && $direction == 'asc') {
                $query->orderBy('leads.closed_on')->orderBy('leads.updated_at');

            }






            // Apply search and filter conditions
            $search = $request->input('search.value');

            if (!empty($search)) {

                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('CONCAT(prospect_first_name, " ", prospect_last_name)'), 'LIKE', '%' . $search . '%')
                        ->orWhere('timezone', 'LIKE', '%' . $search . '%')
                        ->orWhere('designation', 'LIKE', '%' . $search . '%')
                        ->orWhere('contact_number_1', 'LIKE', '%' . $search . '%')
                        ->orWhere('sources.description', 'LIKE', '%' . $search . '%')
                        ->orWhere('sources.source_name', 'LIKE', '%' . $search . '%');
                });
            }



            $userPermissions = null;
            if (Auth::user()->is_admin == SUBMANAGER) {
                $permissions = SubmanagerPermissions::where('user_id', Auth::id())->first();
                if ($permissions) {
                    $userPermissions = json_decode($permissions->user_permissions);
                }
            }

            return datatables()->of($query)
                ->addColumn('updated_at_new', function ($row) {
                    if (!empty($row->closed_on)) {
                        return date('d/m/Y', strtotime($row->closed_on));
                    } else {
                        return date('d/m/Y', strtotime($row->updated_at));

                    }
                })
                ->addColumn('action', function ($row) use ($userPermissions) {

                    $notesButton = '';

                    // 📝 Notes
                    $notesButton .= '
                        <a onclick="shownoteslist(' . $row->id . ')" class="notes_id">
                            <i class="fas fa-comment label-new" data-tippy-content="Notes"></i>
                        </a>';

                    // 👁 View
                    $notesButton .= '
                        <a href="' . url('leads', $row->id) . '" class="notes_id">
                            <i class="fas fa-eye label-new" data-tippy-content="View Lead"></i>
                        </a>';

                    // ⬇ Download (only if status = 3)
                    if ($row->status == 3) {
                        $notesButton .= '
                            <a href="' . url('employee/export/' . $row->id . '/word_single_down') . '?employee_id=&campaign_id=&date_from=&date_to=" class="notes_id">
                                <i class="fa fa-arrow-down label-new" style="color:green" data-tippy-content="Download"></i>
                            </a>';
                    }



                    $momReport = MomReport::where('lead_id', $row->id)->orderby('id', 'desc')->first();

                    if (isset($momReport) && !empty($momReport)) {

                        // ⬇ Download MOM (only if status = 5 and MOM is generated)
                        $notesButton .= '
                            <a href="' . route('download-single-mom', [$row->id]) . '" class="downloadmom notes_id">
                                <i class="fa fa-download label-new" style="color:#55ce63" data-tippy-content="Download MOM"></i>
                            </a>';

                    }

                    // ✏ Edit permission
                    if (Auth::user()->is_admin == SUBMANAGER) {
                        if ($userPermissions && !empty($userPermissions->lead->edit) && $userPermissions->lead->edit == 1) {
                            $notesButton .= '
                                <a href="' . url('editlead', $row->id) . '" class="notes_id">
                                    <i class="fas fa-edit label-new" data-tippy-content="Edit Lead"></i>
                                </a>';
                        }
                    } else {
                        $notesButton .= '
                            <a href="' . url('editlead', $row->id) . '" class="notes_id">
                                <i class="fas fa-edit label-new" data-tippy-content="Edit Lead"></i>
                            </a>';
                    }

                    // 🗑 Delete permission
                    if (Auth::user()->is_admin == SUBMANAGER) {
                        if ($userPermissions && !empty($userPermissions->lead->delete) && $userPermissions->lead->delete == 1) {
                            $notesButton .= '
                            <a href="#"
                            class="notes_id"
                            onclick="deleteLead(' . $row->id . ')">
                            <i class="fa fa-trash label-new" style="color:red;font-size:15px"
                                data-tippy-content="Delete Lead"></i>
                         </a>';
                        }
                    } else {
                        $notesButton .= '
                        <a href="#"
                        class="notes_id"
                        onclick="deleteLead(' . $row->id . ')">
                        <i class="fa fa-trash label-new" style="color:red;font-size:15px"
                            data-tippy-content="Delete Lead"></i>
                     </a>';
                    }

                    return $notesButton;
                })
                ->editColumn('prospect_first_name_new', function ($row) {
                    $leadName = '<a href="' . url('/leads', [$row->id]) . '" target="_blank">' . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';
                    $linkedinAddress = $row->linkedin_address ?? ''; // Ensure the variable exists
                    $linkedinIcon = '';

                    if (strpos($linkedinAddress, 'linkedin') === false) {
                        $linkedinIcon = '<a href="javascript:void(0)"><i style="color: #000" alt="LinkedIn" title="LinkedIn Address Not Valid" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    } else {
                        $linkedinUrl = strpos($linkedinAddress, 'http://') !== 0 && strpos($linkedinAddress, 'https://') !== 0
                            ? 'https://' . $linkedinAddress
                            : $linkedinAddress;
                        $linkedinIcon = '<a href="' . $linkedinUrl . '" target="_blank"><i alt="LinkedIn" title="LinkedIn" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    }

                    return $leadName . '  ' . $linkedinIcon;
                })

                ->addColumn('update_note_date', function ($row) {
                    return 'N/A';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return 'Pending';
                    } elseif ($row->status == 2) {
                        return 'Failed';
                    } elseif ($row->status == 5) {
                        return 'Completed';
                    } else {
                        return 'Closed';
                    }
                })
                ->addColumn('completed_by', function ($row) {
                    $employeedetails = $row->user;
                    return $employeedetails ? ($employeedetails->first_name . ' ' . $employeedetails->last_name) : 'N/A';
                })
                ->editColumn('contact_number_1', function ($row) {
                    if (empty($row->contact_number_1)) {
                        return 'N/A';
                    }

                    // Split by '/-' if present, otherwise fall back to comma, semicolon, space
                    if (strpos($row->contact_number_1, '/-') !== false) {
                        $numbers = explode('/-', $row->contact_number_1);
                    } else {
                        $numbers = preg_split('/[,\s;]+/', $row->contact_number_1);
                    }
                    $numbers = array_map('trim', $numbers);
                    
                    // Remove all hyphens from each number
                    $numbers = array_map(function ($num) {
                        return str_replace('-', '', $num);
                    }, $numbers);

                    // Filter out any elements that do not contain a digit (e.g. only '-', '/' etc.)
                    $numbers = array_values(array_filter($numbers, function ($num) {
                        return preg_match('/\d/', $num);
                    }));

                    $count = count($numbers);

                    if ($count == 0) {
                        return 'N/A';
                    }

                    $firstNumber = $numbers[0];
                    if ($count <= 1) {
                        return $firstNumber;
                    }

                    $contact = json_encode(implode(', ', $numbers));
                    // Pass the rest of the numbers as a JSON array to the JS function
    
                    return "{$firstNumber} 
            <span class='badge' 
                  style='cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px;' 
                  onclick='showAllNumbers({$contact})'>
                  + show more
            </span>";
                })
                ->rawColumns(['action', 'prospect_first_name_new', 'contact_number_1']) // To render HTML in the actions column
                ->make(true);
        }
        $id = '';
        return view('leads.employeecompletedleads', compact('id', 'comapnyName', 'timeZone', 'sourceNames', 'closedon'));

    }


    public function employeefailedleads(Request $request)
    {
        if (auth()->user()->is_admin == null) {
            $employee_ids = User::where(function ($query) {
                $query->whereIn('is_admin', ['1', '3']);
            })
                ->where('is_active', 1)
                ->pluck('id');
        } else {
            $employee_ids = User::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->whereIn('is_admin', ['1', '3']);
            })
                ->orWhereIn('user_id', function ($subquery) {
                    $subquery->select('id')
                        ->from('users')
                        ->where('user_id', auth()->id())
                        ->where('is_admin', '3');
                })
                ->where('is_active', 1)
                ->pluck('id');
        }
        $comapnyName = Lead::select('company_name')
            ->where('status', 2)
            ->whereIn('asign_to', $employee_ids)
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->distinct()
            ->orderBy('company_name', 'asc')
            ->get();

        $sourceIds = Lead::where('status', 2)
            ->whereIn('asign_to', $employee_ids)
            ->whereNotNull('source_id')
            ->where('source_id', '!=', '')
            ->distinct()
            ->pluck('source_id')
            ->toArray();

        $sourceNames = Source::whereIn('id', $sourceIds)
            ->select('source_name')
            ->distinct()
            ->orderBy('source_name')
            ->get();

        $timeZone = Lead::select('timezone')->where(['status' => 2])
            ->whereIn('asign_to', $employee_ids)
            ->whereNotNull('timezone')
            ->where('timezone', '!=', '')
            ->distinct()
            ->orderBy('timezone', 'asc')
            ->get();

        $closedon = Lead::where(['status' => 2])
            ->whereIn('asign_to', $employee_ids)
            ->selectRaw('DATE(updated_at) as date')  // Extract the date part
            ->distinct()  // Ensure distinct dates
            ->orderBy('date', 'DESC')  // Order by the extracted date
            ->get()
            ->toArray();
        // Check if the request is an AJAX call from DataTable
        if (request()->ajax()) {
            $query = Lead::with(['user', 'momReport'])->select('leads.*', 'sources.source_name', 'sources.description')->join('sources', 'leads.source_id', '=', 'sources.id')
                ->where('status', 2)->whereIn('asign_to', $employee_ids);
            if (!empty(request('cName'))) {
                $query->where('company_name', '=', request('cName'));
            }

            if (!empty(request('timeZone'))) {
                $query->where('timezone', '=', request('timeZone'));
            }

            if (!empty(request('campaign_name'))) {
                $query->where('sources.source_name', '=', request('campaign_name'));
            }

            if (!empty(request('closedon'))) {
                $query->whereDate('leads.updated_at', request('closedon'));
            }

            if (!empty(request('date_from')) && !empty(request('date_to'))) {
                $query->where(function ($q) {
                    $q->whereBetween('leads.closed_on', [request('date_from') . ' 00:00:00', request('date_to') . ' 23:59:59'])
                        ->orWhere(function ($sub) {
                            $sub->whereNull('leads.closed_on')
                                ->whereBetween('leads.updated_at', [request('date_from') . ' 00:00:00', request('date_to') . ' 23:59:59']);
                        });
                });
            }
            $columnIndex = $request->input('order.0.column'); // this will be 9
            $direction = $request->input('order.0.dir');      // this will be 'desc'

            if (!is_null($columnIndex) && $columnIndex == 9 && $direction == 'desc') {
                $query->orderbyDesc('leads.closed_on')->orderByDesc('leads.updated_at');

            }

            if (!is_null($columnIndex) && $columnIndex == 9 && $direction == 'asc') {
                $query->orderBy('leads.closed_on')->orderBy('leads.updated_at');

            }

            // Apply search and filter conditions
            $search = $request->input('search.value');

            if (!empty($search)) {

                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('CONCAT(prospect_first_name, " ", prospect_last_name)'), 'LIKE', '%' . $search . '%')
                        ->orWhere('timezone', 'LIKE', '%' . $search . '%')
                        ->orWhere('designation', 'LIKE', '%' . $search . '%')
                        ->orWhere('contact_number_1', 'LIKE', '%' . $search . '%')
                        ->orWhere('sources.description', 'LIKE', '%' . $search . '%')
                        ->orWhere('sources.source_name', 'LIKE', '%' . $search . '%');
                });
            }

            $userPermissions = null;
            if (Auth::user()->is_admin == SUBMANAGER) {
                $permissions = SubmanagerPermissions::where('user_id', Auth::id())->first();
                if ($permissions) {
                    $userPermissions = json_decode($permissions->user_permissions);
                }
            }

            return datatables()->of($query)
                ->addColumn('updated_at_new', function ($row) {
                    if (!empty($row->closed_on)) {
                        return date('d/m/Y', strtotime($row->closed_on));
                    } else {
                        return date('d/m/Y', strtotime($row->updated_at));

                    }
                })
                ->addColumn('action', function ($row) use ($userPermissions) {

                    $notesButton = '';

                    // Notes
                    $notesButton .= '
                        <a onclick="shownoteslist(' . $row->id . ')" class="notes_id">
                            <i class="fas fa-comment label-new" data-tippy-content="Notes"></i>
                        </a>';

                    // View
                    $notesButton .= '
                        <a href="' . url('leads', $row->id) . '" class="notes_id">
                            <i class="fas fa-eye label-new" data-tippy-content="View Lead"></i>
                        </a>';

                    // Edit permission
                    if (Auth::user()->is_admin == SUBMANAGER) {
                        if ($userPermissions && !empty($userPermissions->lead->edit) && $userPermissions->lead->edit == 1) {
                            $notesButton .= '
                                <a href="' . url('editlead', $row->id) . '" class="notes_id">
                                    <i class="fas fa-edit label-new" data-tippy-content="Edit Lead"></i>
                                </a>';
                        }
                    } else {
                        $notesButton .= '
                            <a href="' . url('editlead', $row->id) . '" class="notes_id">
                                <i class="fas fa-edit label-new" data-tippy-content="Edit Lead"></i>
                            </a>';
                    }

                    // Delete permission
                    if (Auth::user()->is_admin == SUBMANAGER) {
                        if ($userPermissions && !empty($userPermissions->lead->delete) && $userPermissions->lead->delete == 1) {
                            $notesButton .= '
                            <a href="#"
                            class="notes_id"
                            onclick="deleteLead(' . $row->id . ')">
                            <i class="fa fa-trash label-new" style="color:red;font-size:15px"
                                data-tippy-content="Delete Lead"></i>
                         </a>';
                        }
                    } else {
                        $notesButton .= '
                        <a href="#"
                        class="notes_id"
                        onclick="deleteLead(' . $row->id . ')">
                        <i class="fa fa-trash label-new" style="color:red;font-size:15px"
                            data-tippy-content="Delete Lead"></i>
                     </a>';
                    }

                    return $notesButton;
                })
                ->editColumn('prospect_first_name_new', function ($row) {
                    $leadName = '<a href="' . url('/leads', [$row->id]) . '" target="_blank">' . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';
                    $linkedinAddress = $row->linkedin_address ?? ''; // Ensure the variable exists
                    $linkedinIcon = '';

                    if (strpos($linkedinAddress, 'linkedin') === false) {
                        $linkedinIcon = '<a href="javascript:void(0)"><i style="color: #000" alt="LinkedIn" title="LinkedIn Address Not Valid" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    } else {
                        $linkedinUrl = strpos($linkedinAddress, 'http://') !== 0 && strpos($linkedinAddress, 'https://') !== 0
                            ? 'https://' . $linkedinAddress
                            : $linkedinAddress;
                        $linkedinIcon = '<a href="' . $linkedinUrl . '" target="_blank"><i alt="LinkedIn" title="LinkedIn" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    }

                    return $leadName . '  ' . $linkedinIcon;
                })

                ->addColumn('update_note_date', function ($row) {
                    return 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return 'Failed';
                })
                ->addColumn('completed_by', function ($row) {
                    $employeedetails = $row->user;
                    return $employeedetails ? ($employeedetails->first_name . ' ' . $employeedetails->last_name) : 'N/A';
                })
                ->editColumn('contact_number_1', function ($row) {
                    if (empty($row->contact_number_1)) {
                        return 'N/A';
                    }

                    // Split by '/-' if present, otherwise fall back to comma, semicolon, space
                    if (strpos($row->contact_number_1, '/-') !== false) {
                        $numbers = explode('/-', $row->contact_number_1);
                    } else {
                        $numbers = preg_split('/[,\s;]+/', $row->contact_number_1);
                    }
                    $numbers = array_map('trim', $numbers);
                    
                    // Remove all hyphens from each number
                    $numbers = array_map(function ($num) {
                        return str_replace('-', '', $num);
                    }, $numbers);

                    // Filter out any elements that do not contain a digit (e.g. only '-', '/' etc.)
                    $numbers = array_values(array_filter($numbers, function ($num) {
                        return preg_match('/\d/', $num);
                    }));

                    $count = count($numbers);

                    if ($count == 0) {
                        return 'N/A';
                    }

                    $firstNumber = $numbers[0];
                    if ($count <= 1) {
                        return $firstNumber;
                    }

                    $contact = json_encode(implode(', ', $numbers));
                    // Pass the rest of the numbers as a JSON array to the JS function
    
                    return "{$firstNumber} 
            <span class='badge' 
                  style='cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px;' 
                  onclick='showAllNumbers({$contact})'>
                  + show more
            </span>";
                })
                ->rawColumns(['action', 'prospect_first_name_new', 'contact_number_1']) // To render HTML in the actions column
                ->make(true);
        }
        $id = '';
        return view('leads.employeefailedleads', compact('id', 'comapnyName', 'timeZone', 'sourceNames', 'closedon'));

    }


    public function leadslist(Request $request)
    {
        // Check if the request is an AJAX call from DataTable
        if (request()->ajax()) {
            if (isset($request['status']) && !empty($request['status'])) {
                $query = Lead::select('leads.*', 'sources.*')->with('source')
                    ->join('sources', 'leads.source_id', '=', 'sources.id') // Join with sources table
                    ->where('status', $request['status']);

            } else {
                $query = Lead::select('leads.*', 'sources.*')->with('source')
                    ->join('sources', 'leads.source_id', '=', 'sources.id');
                $comapnyName = Lead::with('source')->where(['status' => $request['status']])->orderBy('company_name', 'asc')->groupBy('company_name')->get();
                $timeZone = Lead::with('source')->where(['status' => $request['status']])->orderBy('timezone', 'asc')->groupBy('timezone')->get();
            }
            if (!empty(request('cName'))) {
                $query->where('company_name', 'LIKE', '%' . request('cName') . '%');
            }

            if (!empty(request('timeZone'))) {
                $query->where('timezone', 'LIKE', '%' . request('timeZone') . '%');
            }

            if (!empty(request('campaign_name'))) {
                $query->whereHas('source', function ($q) use ($request) {
                    $q->where('source_name', 'LIKE', '%' . request('campaign_name') . '%');
                });
            }

            if (!empty(request('closedon'))) {
                // Ensure 'closedon' is in a valid format (Y-m-d) and match only the date part of updated_at
                $query->whereRaw('DATE(updated_at) = ?', [request('closedon')]);
            }


            // Apply search and filter conditions
            if (!empty(request('search'))) {
                $search = trim(request('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('prospect_last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('CONCAT(prospect_first_name, " ", prospect_last_name)'), 'LIKE', '%' . $search . '%')
                        ->orWhere('timezone', 'LIKE', '%' . $search . '%')
                        ->orWhere('designation', 'LIKE', '%' . $search . '%')
                        ->orWhere('contact_number_1', 'LIKE', '%' . $search . '%');
                });
            }


            return datatables()->of($query)
                ->addColumn('source_name', function ($row) {
                    return $row->source->source_name ?? 'N/A'; // Example: Adjust 'name' as per your source model
                })
                ->editColumn('description', function ($row) {
                    return $row->source->description ?? 'N/A'; // Example: Adjust 'name' as per your source model
                })
                ->editColumn('updated_at', function ($row) {
                    if (!empty($row->closed_on)) {
                        return date('d/m/Y', strtotime($row->closed_on));
                    } else {
                        return date('d/m/Y', strtotime($row->updated_at));

                    }

                })
                ->addColumn('action', function ($row) {
                    $notesButton = '<a onclick="shownoteslist(' . $row->id . ')" class="notes_id" data-toggle="modal" data-target="#largeModal">
                                     <i class="fas fa-comment label-new" aria-hidden="true"></i>
                                 </a>';
                    $notesButton .= '<a href="' . url('leads', $row->id) . '" class="notes_id" data-toggle="modal" data-target="#largeModal">
                                 <i class="fas fa-eye label-new" aria-hidden="true"></i>
                             </a>';
                    $notesButton .= '<a href="' . url('editlead', $row->id) . '" class="notes_id" data-toggle="modal" data-target="#largeModal">
                                 <i class="fas fa-edit label-new" aria-hidden="true"></i>
                             </a>';
                    if ($row->status == 3) {
                        $notesButton .= '<a href="' . url('employee/export/' . $row->id . '/word_single_down') . '?employee_id=&campaign_id=&date_from=&date_to=" class="notes_id">
                             <i class="fa fa-arrow-down label-new" style="color:green"> </i>
                          </a>';
                    }
                    $notesButton .= '<a href="' . url('leads/delete', ['id' => $row->id]) . '" 
                             class="notes_id" 
                             data-toggle="tooltip" 
                             data-placement="top" 
                             title="Delete" 
                             style="color:red;font-size: 15px;" 
                             onclick="return confirm(\'Are you sure you want to delete this lead ?\')">
                             <i class="fa fa-trash" aria-hidden="true"></i>
                         </a>';

                    return $notesButton;


                })
                ->editColumn('prospect_first_name', function ($row) {
                    $leadName = '<a href="' . url('/leads', [$row->id]) . '" target="_blank">' . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';
                    $linkedinAddress = $row->linkedin_address ?? ''; // Ensure the variable exists
                    $linkedinIcon = '';

                    if (strpos($linkedinAddress, 'linkedin') === false) {
                        $linkedinIcon = '<a href="javascript:void(0)"><i style="color: #000" alt="LinkedIn" title="LinkedIn Address Not Valid" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    } else {
                        $linkedinUrl = strpos($linkedinAddress, 'http://') !== 0 && strpos($linkedinAddress, 'https://') !== 0
                            ? 'https://' . $linkedinAddress
                            : $linkedinAddress;
                        $linkedinIcon = '<a href="' . $linkedinUrl . '" target="_blank"><i alt="LinkedIn" title="LinkedIn" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
                    }

                    return $leadName . '  ' . $linkedinIcon;
                })

                ->addColumn('update_note_date', function ($row) {
                    return 'N/A';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return 'Pending';
                    } elseif ($row->status == 2) {
                        return 'Failed';
                    } else {
                        return 'Closed';
                    }
                })
                ->rawColumns(['action', 'prospect_first_name']) // To render HTML in the actions column
                ->make(true);
        }


        $comapnyName = Lead::with('source')
            ->where('status', $request['status'])
            ->orderBy('company_name', 'asc')
            ->distinct()
            ->get(['company_name', 'source_id']);

        $sourceNames = Source::select('sources.source_name', 'sources.description')
            ->join('leads', 'leads.source_id', '=', 'sources.id')
            ->distinct() // Ensures unique source names
            ->get();
        $timeZone = Lead::with('source')->where(['status' => $request['status']])->orderBy('timezone', 'asc')->groupBy('timezone')->get();

        if (!empty($request['status'] == 3)) {
            $closedon = Lead::where(['status' => $request['status']])
                ->selectRaw('DATE(updated_at) as date')  // Extract the date part
                ->distinct()  // Ensure distinct dates
                ->orderBy('date', 'DESC')  // Order by the extracted date
                ->get()
                ->toArray();

        } else {
            $closedon = Lead::selectRaw('DATE(updated_at) as date')  // Extract the date part
                ->distinct()  // Ensure distinct dates
                ->orderBy('date', 'DESC')  // Order by the extracted date
                ->get()
                ->toArray();
        }
        $id = '';
        return view('leads.adminhomepageleads', compact('id', 'comapnyName', 'timeZone', 'sourceNames', 'closedon'));

    }

    public function getMangerSource()
    {
        $managers = User::where('is_admin', 2)->where('deleted_at', NULL)->where(function ($query) {
            $query->where('manager_type', 1)
                ->orWhereNull('manager_type');
        })->get();
        // dd($data);
        $externalManagers = DB::table('users')->where('is_admin', 2)->where('deleted_at', NULL)->where('manager_type', 2)->get()->toArray();
        $permissions = SubmanagerPermissions::where('user_id', Auth::id())->first();

        $active = Source::where(function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->orWhere('assign_to_manager', auth()->user()->id);
        })
            ->where('is_active', 1)->count();
        $inactive = Source::where(function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->orWhere('assign_to_manager', auth()->user()->id);
        })
            ->where('is_active', 2)
            ->count();
        $totalCampaigns = Source::where(function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->orWhere('assign_to_manager', auth()->user()->id);
        })
            ->count();


        return view('sources.list_new')->with(['managers' => $managers, 'externalManagers' => $externalManagers, 'permissions' => $permissions, 'active' => $active, 'inactive' => $inactive, 'totalCampaigns' => $totalCampaigns]);
    }

    public function leadscount(Request $request)
    {
        // Fetch assigned data grouped by `asign_to`
        $assingData = Lead::where('source_id', $request->source_id)->select(
            'asign_to',
            DB::raw('COUNT(*) as totalasign_to'),
            DB::raw("SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) as failed_leads"),
            DB::raw("SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as pending_leads"),
            DB::raw("SUM(CASE WHEN status = '3' THEN 1 ELSE 0 END) as closed_leads")
        )
            ->groupBy('asign_to')
            ->get();
        if ($assingData->isEmpty()) {
            $html = '<div class="tooltip1">
                    <span class="tooltiptext1">No Result</span>
                </div>';
            return response()->json(['html' => $html, 'source_id' => $request->source_id]);
        }

        // Get all assigned user names in a single query
        $userIds = $assingData->pluck('asign_to')->toArray();
        $users = User::whereIn('id', $userIds)->pluck('name', 'id'); // Fetch names indexed by ID

        // Build tooltip HTML
        $tooltipContent = '';
        foreach ($assingData as $assign) {
            if (empty($assign->asign_to)) {
                $userName = 'Unassigned';
            } else {
                $userName = $users[$assign->asign_to] ?? 'Unknown'; // Handle missing users
            }
            $tooltipContent .= " <tr style='#c9d1e3:1px solid black'>
            <td style='border:1px solid #c9d1e3;text-align:center'> $userName</td>
            <td style='border:1px solid #c9d1e3;text-align:center'>$assign->totalasign_to</td>
            <td style='border:1px solid #c9d1e3;text-align:center'>$assign->pending_leads</td>
            <td style='border:1px solid #c9d1e3;text-align:center'>$assign->closed_leads</td>
            <td style='border:1px solid #c9d1e3;text-align:center'>$assign->failed_leads</td>
        </tr>";
        }

        // Return HTML as a response
        $html = '
            <table border="1" cellpadding="4" cellspacing="0" style="width:100%;border:1px solid black">
                <thead style="border:1px solid #c9d1e3;color:black;font-size:16px">
                    <tr>
                        <th style="border:1px solid #c9d1e3;text-align:center">Employee Name</th>
                        <th style="border:1px solid #c9d1e3;text-align:center">Total Leads</th>
                        <th style="border:1px solid #c9d1e3;text-align:center">Pending Leads</th>
                        <th style="border:1px solid #c9d1e3;text-align:center">Closed Leads</th>
                        <th style="border:1px solid #c9d1e3;text-align:center">Failed Leads</th>
                    </tr>
                </thead>
                <tbody style="border:1px solid #c9d1e3;color:black;font-size:14px">
                ' . $tooltipContent . '
                </tbody>
            </table>';

        return response()->json(['html' => $html, 'source_id' => $request->source_id]);
    }


    public function import_leads(Request $request)
    {
        $source_id = $request->source_name;
        $import_duplicate = $request->import_duplicate ?? 0;
        $source = $request->source;

        if ($request->hasFile('file') && $request->file('file')->isValid()) {

            $file = $request->file('file');

            $importService = new CampaignImport($source_id);

            $importService->importLeads(
                $file,
                $import_duplicate,
                $source
            );
        }

        return redirect('leads/assign_lead_emp/' . $source_id)
            ->with('success', 'Lead Imported Successfully.');
    }

    public function usersByManager(Request $request): string
    {
        $users = User::where(['user_id' => auth()->user()->id, 'is_admin' => 1])->get(['id', 'first_name', 'last_name']);
        $html = '<div class="table-responsive m-t-40" id="table_data">
                    <table id="sources" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0"
                        width="100%">
                        <thead class="heading-custom">
                            <tr>
                                <th width="100" style="text-align:center">Employee Name</th>                             
                                <th width="50" style="text-align:center">Action</th>
                            </tr>
                        </thead>
                        <tbody>';
        foreach ($users as $user) {
            $html .= '<tr>          
                <td style="white-space: pre-wrap" width="200">' . $user->first_name . $user->last_name . '</td>';
            $html .= '<td><a href="javascript:void(0);" data-user ="' . $user->id . '" onclick="assignLeadsToUser(this)" on><span class="label btn-success">Assign</span></a>                   
                </td>              
            </tr>';
        }
        $html .= '</tbody>
            </table>
        </div>';
        return $html;
    }

    public function downloadmomreport($id)
    {
        $source = Source::where('id', $id)->first();
        if (!$source) {
            return back()->with('error', 'File not found');
        }
        $folderName = 'public/mom/' . $source->source_name . '-' . $source->id; // Folder to be zipped (inside storage/app/)
        $zipFileName = $source->source_name . '_mom_' . time() . '.zip'; // ZIP file name
        $zipPath = storage_path('app/' . $zipFileName); // Path to store ZIP

        // Ensure folder exists
        if (!Storage::exists($folderName)) {
            return back()->with('error', 'File not found');
        }

        // Create a new ZIP Archive
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = Storage::files($folderName); // Get all files in folder
            if (empty($files)) {
                return back()->with('error', 'File not found');
            }
            foreach ($files as $file) {
                $filePath = storage_path('app/' . $file);
                $zip->addFile($filePath, basename($file)); // Add file to ZIP
            }

            $zip->close();
        } else {
            return back()->with('error', 'File not found');
        }

        // Download ZIP & delete after sending
        return response()->download($zipPath)->deleteFileAfterSend(true);

    }


    public function employeecampaign(Request $request)
    {
        $userId = Auth::id();
        $totalCampaign = Source::whereHas('leads', function ($q) use ($userId) {
            $q->where('asign_to', $userId)
                ->where('status', '!=', '2');
        })->count();

        $activeCampaign = Source::where('is_active', 1)
            ->whereHas('leads', function ($q) use ($userId) {
                $q->where('asign_to', $userId)
                    ->where('status', '!=', '2');
            })->count();

        $inactiveCampaign = Source::where('is_active', operator: 2)
            ->whereHas('leads', function ($q) use ($userId) {
                $q->where('asign_to', $userId)
                    ->where('status', '!=', '2');
            })->count();


        return view('employeecampaigns', compact('totalCampaign', 'activeCampaign', 'inactiveCampaign'));
    }

    public function checkCampaignExists(Request $request)
    {
        $campaignName = strtolower($request->source_name);
        $subCampaign = strtolower($request->description);

        $query = Source::whereRaw('LOWER(source_name) = ?', [$campaignName]);

        if (!empty($subCampaign)) {
            $query->whereRaw('LOWER(description) = ?', [$subCampaign]);
        }

        $id = $request->source_id ?: $request->id;
        if (!empty($id)) {
            $query->where('id', '!=', $id);
        }

        $checkSourceName = $query->first();

        if ($checkSourceName) {
            return response()->json([
                'status' => 400,
                'message' => 'Campaign already exists.'
            ]);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Source Available'
            ]);
        }
    }

    public function exportClosedLeadsCsv(Request $request)
    {
        if (auth()->user()->is_admin == null) {
            $employee_ids = User::where(function ($query) {
                $query->whereIn('is_admin', ['1', '3']);
            })
                ->where('is_active', 1)
                ->pluck('id');
        } else {
            $employee_ids = User::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->whereIn('is_admin', ['1', '3']);
            })
                ->orWhereIn('user_id', function ($subquery) {
                    $subquery->select('id')
                        ->from('users')
                        ->where('user_id', auth()->id())
                        ->where('is_admin', '3');
                })
                ->where('is_active', 1)
                ->pluck('id');
        }

        $query = Lead::select('leads.*', 'sources.source_name', 'sources.description')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->where('status', 3)
            ->whereIn('asign_to', $employee_ids);

        if (!empty($request->get('cName'))) {
            $query->where('company_name', 'LIKE', '%' . $request->get('cName') . '%');
        }

        if (!empty($request->get('timeZone'))) {
            $query->where('timezone', 'LIKE', '%' . $request->get('timeZone') . '%');
        }

        if (!empty($request->get('campaign_name'))) {
            $query->whereHas('source', function ($q) use ($request) {
                $q->where('source_name', 'LIKE', '%' . $request->get('campaign_name') . '%');
            });
        }

        if (!empty($request->get('closedon'))) {
            $query->join('lhs_report', 'lhs_report.lead_id', '=', 'leads.id')
                ->whereDate('lhs_report.created_at', $request->get('closedon'))
                ->groupBy('leads.id');
        }

        if (!empty($request->get('invitation_date'))) {
            $query->whereDate('invitation_date', $request->get('invitation_date'));
        }

        if (!empty($request->get('confirmation_status'))) {
            if ($request->get('confirmation_status') == 'sent') {
                $query->whereNotNull('invitation_date');
            } elseif ($request->get('confirmation_status') == 'waiting') {
                $query->whereNull('invitation_date');
            }
        }

        if (!empty($request->get('date_from')) && !empty($request->get('date_to'))) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('leads.closed_on', [$request->get('date_from') . ' 00:00:00', $request->get('date_to') . ' 23:59:59'])
                    ->orWhere(function ($sub) use ($request) {
                        $sub->whereNull('leads.closed_on')
                            ->whereBetween('leads.updated_at', [$request->get('date_from') . ' 00:00:00', $request->get('date_to') . ' 23:59:59']);
                    });
            });
        }

        if (!empty($request->get('search'))) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('prospect_first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('prospect_last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere(DB::raw('CONCAT(prospect_first_name, " ", prospect_last_name)'), 'LIKE', '%' . $search . '%')
                    ->orWhere('timezone', 'LIKE', '%' . $search . '%')
                    ->orWhere('designation', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact_number_1', 'LIKE', '%' . $search . '%')
                    ->orWhere('sources.description', 'LIKE', '%' . $search . '%')
                    ->orWhere('sources.source_name', 'LIKE', '%' . $search . '%');
            });
        }

        $leads = $query->with(['lhsreport', 'user'])->get();

        $filename = 'Closed_Leads_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header row
            fputcsv($file, [
                'Campaign Name',
                'Sub Campaign Name',
                'Company Name',
                'Closed By',
                'Prospect Name',
                'Time Zone',
                'Designation',
                'Email Id',
                'Phone Number',
                'Closed On',
                'Invitation Date',
                'Confirmation Status',
                'Reminder Status'
            ]);

            foreach ($leads as $lead) {
                // Determine Closed On Date
                $checklhs = $lead->lhsreport ? $lead->lhsreport->created_at : null;
                $closedOnDate = $checklhs ? \Carbon\Carbon::parse($checklhs)->format('d/m/Y') : date('d/m/Y', strtotime($lead->updated_at));

                // Closed By User Name
                $closedByUser = $lead->user;
                $closedByName = $closedByUser ? ($closedByUser->first_name . ' ' . $closedByUser->last_name) : 'N/A';

                // Confirmation Status
                $confStatus = $lead->invitation_date ? 'Confirmation Sent' : 'Waiting for Confirmation';

                // Reminder Status
                $reminderStatus = $lead->reminder_status == 1 ? 'Sent' : 'Pending';

                fputcsv($file, [
                    $lead->source_name,
                    $lead->description,
                    $lead->company_name,
                    $closedByName,
                    $lead->prospect_first_name . ' ' . $lead->prospect_last_name,
                    $lead->timezone,
                    $lead->designation,
                    $lead->prospect_email,
                    $lead->contact_number_1,
                    $closedOnDate,
                    $lead->invitation_date ? date('d/m/Y h:i A', strtotime($lead->invitation_date)) : 'N/A',
                    $confStatus,
                    $reminderStatus
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCompletedLeadsCsv(Request $request)
    {
        if (auth()->user()->is_admin == null) {
            $employee_ids = User::where(function ($query) {
                $query->whereIn('is_admin', ['1', '3']);
            })
                ->where('is_active', 1)
                ->pluck('id');
        } else {
            $employee_ids = User::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->whereIn('is_admin', ['1', '3']);
            })
                ->orWhereIn('user_id', function ($subquery) {
                    $subquery->select('id')
                        ->from('users')
                        ->where('user_id', auth()->id())
                        ->where('is_admin', '3');
                })
                ->where('is_active', 1)
                ->pluck('id');
        }

        $query = Lead::select('leads.*', 'sources.source_name', 'sources.description')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->where('status', 5)
            ->whereIn('asign_to', $employee_ids);

        if (!empty($request->get('cName'))) {
            $query->where('company_name', 'LIKE', '%' . $request->get('cName') . '%');
        }

        if (!empty($request->get('timeZone'))) {
            $query->where('timezone', 'LIKE', '%' . $request->get('timeZone') . '%');
        }

        if (!empty($request->get('campaign_name'))) {
            $query->whereHas('source', function ($q) use ($request) {
                $q->where('source_name', 'LIKE', '%' . $request->get('campaign_name') . '%');
            });
        }

        if (!empty($request->get('closedon'))) {
            $query->whereRaw('DATE(leads.updated_at) = ?', [$request->get('closedon')]);
        }

        if (!empty($request->get('date_from')) && !empty($request->get('date_to'))) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('leads.closed_on', [$request->get('date_from') . ' 00:00:00', $request->get('date_to') . ' 23:59:59'])
                    ->orWhere(function ($sub) use ($request) {
                        $sub->whereNull('leads.closed_on')
                            ->whereBetween('leads.updated_at', [$request->get('date_from') . ' 00:00:00', $request->get('date_to') . ' 23:59:59']);
                    });
            });
        }

        if (!empty($request->get('search'))) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('prospect_first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('prospect_last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere(DB::raw('CONCAT(prospect_first_name, " ", prospect_last_name)'), 'LIKE', '%' . $search . '%')
                    ->orWhere('timezone', 'LIKE', '%' . $search . '%')
                    ->orWhere('designation', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact_number_1', 'LIKE', '%' . $search . '%')
                    ->orWhere('sources.description', 'LIKE', '%' . $search . '%')
                    ->orWhere('sources.source_name', 'LIKE', '%' . $search . '%');
            });
        }

        $leads = $query->with('user')->get();

        $filename = 'Completed_Leads_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header row
            fputcsv($file, [
                'Campaign Name',
                'Sub Campaign Name',
                'Company Name',
                'Completed By',
                'Prospect Name',
                'Time Zone',
                'Designation',
                'Email Id',
                'Phone Number',
                'Completed On'
            ]);

            foreach ($leads as $lead) {
                // Completed On Date
                $completedOnDate = !empty($lead->closed_on) ? date('d/m/Y', strtotime($lead->closed_on)) : date('d/m/Y', strtotime($lead->updated_at));

                // Completed By User Name
                $completedByUser = $lead->user;
                $completedByName = $completedByUser ? ($completedByUser->first_name . ' ' . $completedByUser->last_name) : 'N/A';

                fputcsv($file, [
                    $lead->source_name,
                    $lead->description,
                    $lead->company_name,
                    $completedByName,
                    $lead->prospect_first_name . ' ' . $lead->prospect_last_name,
                    $lead->timezone,
                    $lead->designation,
                    $lead->prospect_email,
                    $lead->contact_number_1,
                    $completedOnDate
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportFailedLeadsCsv(Request $request)
    {
        if (auth()->user()->is_admin == null) {
            $employee_ids = User::where(function ($query) {
                $query->whereIn('is_admin', ['1', '3']);
            })
                ->where('is_active', 1)
                ->pluck('id');
        } else {
            $employee_ids = User::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->whereIn('is_admin', ['1', '3']);
            })
                ->orWhereIn('user_id', function ($subquery) {
                    $subquery->select('id')
                        ->from('users')
                        ->where('user_id', auth()->id())
                        ->where('is_admin', '3');
                })
                ->where('is_active', 1)
                ->pluck('id');
        }

        $query = Lead::select('leads.*', 'sources.source_name', 'sources.description')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->where('status', 2)
            ->whereIn('asign_to', $employee_ids);

        if (!empty($request->get('cName'))) {
            $query->where('company_name', 'LIKE', '%' . $request->get('cName') . '%');
        }

        if (!empty($request->get('timeZone'))) {
            $query->where('timezone', 'LIKE', '%' . $request->get('timeZone') . '%');
        }

        if (!empty($request->get('campaign_name'))) {
            $query->whereHas('source', function ($q) use ($request) {
                $q->where('source_name', 'LIKE', '%' . $request->get('campaign_name') . '%');
            });
        }

        if (!empty($request->get('closedon'))) {
            $query->whereRaw('DATE(leads.updated_at) = ?', [$request->get('closedon')]);
        }

        if (!empty($request->get('date_from')) && !empty($request->get('date_to'))) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('leads.closed_on', [$request->get('date_from') . ' 00:00:00', $request->get('date_to') . ' 23:59:59'])
                    ->orWhere(function ($sub) use ($request) {
                        $sub->whereNull('leads.closed_on')
                            ->whereBetween('leads.updated_at', [$request->get('date_from') . ' 00:00:00', $request->get('date_to') . ' 23:59:59']);
                    });
            });
        }

        if (!empty($request->get('search'))) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('prospect_first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('prospect_last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere(DB::raw('CONCAT(prospect_first_name, " ", prospect_last_name)'), 'LIKE', '%' . $search . '%')
                    ->orWhere('timezone', 'LIKE', '%' . $search . '%')
                    ->orWhere('designation', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact_number_1', 'LIKE', '%' . $search . '%')
                    ->orWhere('sources.description', 'LIKE', '%' . $search . '%')
                    ->orWhere('sources.source_name', 'LIKE', '%' . $search . '%');
            });
        }

        $leads = $query->with('user')->get();

        $filename = 'Failed_Leads_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header row
            fputcsv($file, [
                'Campaign Name',
                'Sub Campaign Name',
                'Company Name',
                'Failed By',
                'Prospect Name',
                'Time Zone',
                'Designation',
                'Email Id',
                'Phone Number',
                'Failed On'
            ]);

            foreach ($leads as $lead) {
                // Failed On Date
                $failedOnDate = !empty($lead->closed_on) ? date('d/m/Y', strtotime($lead->closed_on)) : date('d/m/Y', strtotime($lead->updated_at));

                // Failed By User Name
                $failedByUser = $lead->user;
                $failedByName = $failedByUser ? ($failedByUser->first_name . ' ' . $failedByUser->last_name) : 'N/A';

                fputcsv($file, [
                    $lead->source_name,
                    $lead->description,
                    $lead->company_name,
                    $failedByName,
                    $lead->prospect_first_name . ' ' . $lead->prospect_last_name,
                    $lead->timezone,
                    $lead->designation,
                    $lead->prospect_email,
                    $lead->contact_number_1,
                    $failedOnDate
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

