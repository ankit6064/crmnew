<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Source;
use App\Models\MomReport;
use App\Models\Money;
use App\Models\User;
use App\Models\Lead;
use App\Models\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;
use DataTables;
use App\Imports\CampaignImport;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SourcesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
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
        return view('sources.list_new')->with(['datas' => $data, 'managers' => $managers, 'externalManagers' => $externalManagers]);
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
        /*$data = array(
            'source_name'=>$request->source_name
        );
        
        Source::create($data);
        
        return redirect('sources')->with('success', 'Source Added Successfully.');*/

        $validator = Validator::make($request->all(), [
            'source_name' => 'required',
            'description' => 'required',
        ]);


        if ($validator->passes()) {


            $data = array(
                'user_id' => auth()->user()->id,
                'source_name' => $request->source_name,
                'description' => $request->description
            );
            //dd($data);
            $source_id = Source::create($data);
            $file = request()->file('source_file');
            if ($request->hasFile('source_file') && $request->file('source_file')->isValid()) {
    
    
                $importService = new CampaignImport($source_id->id);
                $importService->importLeads($file);
            }

            return redirect('sources')->with('success', 'Source Added Successfully.');
        }
        return response()->json(['error' => $validator->errors()->all()]);
    }


    public function show($id)
    {
        //
    }


    public function camp_assign(Request $request, $id)
    {
        $data = Source::where(['id' => $id])->first();
        if (isset($request->external_manager) && $request->external_manager == true) {
            $data->assign_to_external_manager = $request->assignedTo;
            $data->accessible_fields = serialize(json_decode($_GET['selected_fields']));
            $all_leads_data = Lead::where('source_id', $id)->get();
            foreach ($all_leads_data as $leadid) {
                Lead::where('id', $leadid->id)->update(['assign_to_external_manager' => $request->assignedTo]);
            }
        } else {
            if (empty($data->assign_to_manager)) {
                $data->assign_to_manager = $request->assignedTo;
                $all_leads_data = Lead::where('source_id', $id)->get();
                foreach ($all_leads_data as $leadid) {
                    Lead::where('id', $leadid->id)->update(['asign_to_manager' => $request->assignedTo]);
                }
            }
        }
        $data->save();
        return redirect('sources')->with('success', 'Campaign Assigned Successfully');
    }

    public function assignManager(Request $request)
    {
        $data = Source::where(['id' => $request->campaign_id])->first();
      
            if (empty($data->assign_to_manager)) {
                $data->assign_to_manager = $request->manager_id;
                $all_leads_data = Lead::where('source_id', $request->campaign_id)->get();
                foreach ($all_leads_data as $leadid) {
                    Lead::where('id', $leadid->id)->update(['asign_to_manager' => $request->assignedTo]);
                }
            }
        
        $data->save();
        echo json_encode(['status'=>200,'message'=>'Manager Assigned']);
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
        if ($request->ajax()) {
            if(Auth::user()->is_admin == null){
                $sources = Source::select(
                    "id",
                    "source_name",
                    "description",
                    "created_at",
                    "updated_at",
                    "assign_to_manager",
                    "is_active",
                    DB::raw("'N/A' as company_distribution"),
                    DB::raw("COALESCE(leads_count.totalLeads, 0) as total_leads"), // Optimized lead count
                )
                ->leftJoinSub(
                    Lead::select('source_id', DB::raw('COUNT(*) as totalLeads'))
                        ->groupBy('source_id'),
                    'leads_count',
                    'leads_count.source_id',
                    'sources.id'
                )
                ->with(['closed_leads', 'leadNotImported'])
                ->orderBy('source_name', 'asc')
                ->get();
            }else{

            // Build the query with necessary conditions
     $sources = Source::where(function ($query) {
        $query->where('user_id', auth()->user()->id)
              ->orWhere('assign_to_manager', auth()->user()->id);
    })
    ->where('is_active', 1) // Applied outside for better query optimization
    ->select(
        "id",
        "source_name",
        "description",
        "created_at",
        "updated_at",
        "assign_to_manager",
        "is_active",
        DB::raw("'N/A' as company_distribution"),
        DB::raw("COALESCE(leads_count.totalLeads, 0) as total_leads"), // Optimized lead count
        DB::raw("(SELECT SUM(amount) FROM money WHERE money.source_id = sources.id) as amount")
    )
    ->leftJoinSub(
        Lead::select('source_id', DB::raw('COUNT(*) as totalLeads'))
            ->groupBy('source_id'),
        'leads_count',
        'leads_count.source_id',
        'sources.id'
    )
    ->with(['closed_leads', 'leadNotImported'])
    ->orderBy('source_name', 'asc')
    ->get();
    }


            return DataTables::of($sources)
                ->editColumn('total_leads', function ($data) {
                    // if(Auth::user()->is_admin == 2){
                    // $totalLeads = '<a href="javascript:void(0)" data-sid="' . $data->id . '" onclick="clickmodal(' . $data->id . ');">' . $data->total_leads . '</a>';
                    // }else{
                    //     $totalLeads = '<p>' . $data->total_leads . '</p>';

                    // }
                    $totalLeads = '<a href="javascript:void(0)" data-sid="' . $data->id . '" onclick="clickmodal(' . $data->id . ');">' . $data->total_leads . '</a>';

                    return $totalLeads;

                })
                ->editColumn('source_name', function ($data) {
                    return '<div class="tooltip1 source-item source-item-'.$data->id.'" data-source-id="' . $data->id . '">' . $data->source_name . '</div>';

                })
                
                ->editColumn('created_at', function ($data) {
                    $created_at = \Carbon\Carbon::parse($data->created_at)->format('d-m-Y');
                    return $created_at;

                })
                ->editColumn('updated_at', function ($data) {
                    $updated_at = \Carbon\Carbon::parse($data->updated_at)->format('d-m-Y');
                    return $updated_at;

                })
                ->addColumn('manager_name',function($row){
                    $assigned_manager = $row->assign_to_manager;
                    $manager_data = User::where(['id'=>$assigned_manager,'is_admin'=>'2'])->first();
                    if(isset($manager_data->name) && !empty($manager_data->name)){
                        $manager_name = $manager_data->name;
                    }else{
                         $manager_name = 'N/A';
                    }
                    return $manager_name;
                })
                ->addColumn('status',function($row){
                    $checked = $row->is_active == 1 ? 'checked' : '';
                    $status = '<input data-sid = "' . $row->id . '" class="switchery" type="checkbox" ' . $checked . ' onchange="updatestatus(' . $row->id . ');">';
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $checkMomReport = MomReport::join('leads', 'mom_report.lead_id', '=', 'leads.id')
                    ->where('leads.source_id', $row->id)
                    ->whereNotNull('mom_report.mom_file_path')
                    ->first();
                        $html =
                        '<a href="' . url('/sources/' . $row->id . '/leadview') . '" target="_blank">
                        <span class="label" data-toggle="tooltip" data-placement="top" title="View Leads" 
                            style="color:#000;font-size: 15px;">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </span>
                    </a> 
                    
                    <a href="' . url('/lead/exportCsv/' . $row->id . '/report_down') . '">
                        <span class="label" data-toggle="tooltip" data-placement="top" title="Download Excel Report" 
                            style="color:#000;font-size: 15px;">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        </span>
                    </a>
                    <a href="' . url('/lead/export/' . $row->id . '/pdf_down') . '">
                        <span class="label" data-toggle="tooltip" data-placement="top" title="Word Download" 
                            style="color:#55ce63;font-size: 15px;">
                            <i class="ti-download"></i>
                        </span>
                    </a>';

                    if(!empty($checkMomReport)){
                        $html .=
                            '<a href="' . url('download-mom-report', ['source_id' => $row->id]) . '">
                            <span class="label" data-toggle="tooltip" data-placement="top" title="Dowload Mom Report" 
                                style="color:blue;font-size: 15px;">
                                <i class="ti-download"></i>
                            </span>
                        </a>';
                    }
                    
                  

                    if(Auth::user()->is_admin == 2){
                        $html .= ' <a href="' . url('/add_leads/' . $row->id) . '" target="_blank">
                        <span class="label" data-toggle="tooltip" data-placement="top" title="Import Leads" 
                            style="color:#000;font-size: 15px;">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </span>
                    </a> ';
                    }

                    // Append an additional link if `leadNotImported` exists
                    if (!empty($row->leadNotImported)) {
                        $html .=
                            '<a href="' . url('download.csv', ['filename' => $row->leadNotImported->file_name]) . '">
                            <span class="label" data-toggle="tooltip" data-placement="top" title="Download leads not imported" 
                                style="color:#ac2609;font-size: 15px;">
                                <i class="ti-download"></i>
                            </span>
                        </a>';
                    }
                    if(Auth::user()->is_admin == null){
                        if(!empty($row->assign_to_manager)){
                        $html .='<a href="javascript:void(0);"><span class="label label-warning">Assigned</span></a>';
                        }
                        else{
                        $html .='<a href="#" class="assignToManagerBtn" id="campaign_id_new" onclick="assignmanager('.$row->id.');"><span  class="label label-warning">Assign to Manager</span></a>';
                        }
                    $html .=
                    '<a href="' . url('sources/delete', ['id' => $row->id]) . '" 
                        onclick="return confirm(\'Are you sure you want to delete this item?\')">
                        <span class="label" data-toggle="tooltip" data-placement="top" title="Delete" 
                            style="color:#dc3545;font-size: 15px;">
                            <i class="ti-trash"></i>
                        </span>
                    </a>';
                    $html .=
                    '<a href="' . url('sources/source-edit',$row->id) . '">
                        <span class="label" data-toggle="tooltip" data-placement="top" title="Edit" 
                            style="color:#000;font-size: 15px;">
                            <i class="ti-pencil"></i>
                        </span>
                    </a>';

                }

                    return $html;
                })
                ->rawColumns(['total_leads', 'action', 'status','source_name'])
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

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
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
        } else {
            $campaign->is_active = 1;
        }
        $campaign->save();
       echo json_encode(['status'=>200,'message'=>'Status Updated']);
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
            ->selectRaw(' SUM( CASE WHEN status = 1 THEN 1 ELSE 0 END) AS pending_leads ')
            ->selectRaw(' SUM( CASE WHEN status = 2 THEN 1 ELSE 0 END) AS failed_leads ')
            ->selectRaw(' SUM( CASE WHEN status = 4 THEN 1 ELSE 0 END) AS inprogress_leads ')
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
        foreach ($leads as $lead) {
            $html .= '<tr>          
                <td style="white-space: pre-wrap" width="200">' . $lead->company_name . '</td>';
            $html .= '<td>' . $lead->pending_leads . ' <fieldset>
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" value="1" name="' . $lead->company_name . '[]" required class="custom-control-input">
                                <span class="custom-control-label"></span>
                            </label>
                        </fieldset></td>';
            $html .= '<td>' . $lead->failed_leads . '<fieldset>
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" value="2" name="' . $lead->company_name . '[]" required class="custom-control-input">
                                <span class="custom-control-label"></span>
                            </label>
                        </fieldset></td>';
            $html .= '<td>' . $lead->inprogress_leads . '<fieldset>
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" value="4" name="' . $lead->company_name . '[]" required class="custom-control-input">
                                <span class="custom-control-label"></span>
                            </label>
                        </fieldset></td>';
            $html .= '<td><a href="javascript:void(0);" data-company="' . $lead->company_name . '" onclick="getAlredayAssignedUsers(this)" ><span class="label btn-success">Users</span></a>                   
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
            // Assign request user to leads
            $result = Lead::whereIn('id', $leadsToAssign)->update(['asign_to' => $userId]);
            if ($result) {
                return [
                    'status' => true,
                    'message' => 'Leads assigned successfully.'
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong. Please try after sometime.'
                ];
            }
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
            ['status', "<>", config('constants.LEADS_STATUS.CLOSED')],
        ])
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


    
    public function camp_assign_emp()
    {
        return view('employeemodule.compaignlist');
    }

public function camp_assign_list(Request $request){
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
        $query = Lead::with('source')
            ->where('status', '1')
            ->where('asign_to', auth()->user()->id)
            ->where('source_id', $id);

            if(!empty(request('cName'))){
                    $query->where('company_name', 'LIKE', '%' . request('cName') . '%');
            }

            if(!empty(request('timeZone'))){
                    $query->where('timezone', 'LIKE', '%' . request('timeZone') . '%');
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
                return $row->source->name ?? 'N/A'; // Example: Adjust 'name' as per your source model
            })
            ->addColumn('action', function ($row) {
                $notesButton = '<a onclick="shownoteslist(' . $row->id . ')" class="notes_id" data-toggle="modal" data-target="#largeModal">
                                     <i class="fas fa-eye label-new" aria-hidden="true"></i>
                                 </a>';
                $quickNoteButton = '<a onclick="showaddmodal(' . $row->id . ')" data-toggle="modal">
                                     <i class="fas fa-comment label-new" aria-hidden="true"></i>
                                 </a>';
                $status = '<a label-info" onclick="showstatusmodal('.$row->id.')" data-toggle="modal" data-target="#status-modal"><i class="fa fa-refresh label-new"></i></a>';
                return $notesButton . ' ' . $quickNoteButton . ' ' .$status;


            })
            ->editColumn('prospect_first_name', function ($row) {
                $leadName =  '<a href="'.url('/leads', [$row->id]).'" target="_blank">' . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';
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
            ->rawColumns(['action','prospect_first_name']) // To render HTML in the actions column
            ->make(true);
    }

     $comapnyName = Lead::with('source')->where(['status'=>'1','asign_to'=>auth()->user()->id,'source_id'=>$id])->orderBy('company_name', 'asc')->groupBy('company_name')->get();
      $timeZone = Lead::with('source')->where(['status'=>'1','asign_to'=>auth()->user()->id,'source_id'=>$id])->orderBy('timezone', 'asc')->groupBy('timezone')->get();

    return view('leads.campaignlisting',compact('id','comapnyName','timeZone'));

}
public function employeeclosedleads(Request $request)
{
    $employee_ids = User::where('user_id',Auth::id())->pluck('id');

    $comapnyName = Lead::with('source')
    ->where('status', 3)
    ->whereIn('asign_to',$employee_ids)
    ->orderBy('company_name', 'asc')
    ->distinct()
    ->get(['company_name', 'source_id']); 

    $sourceNames = Source::select('sources.source_name','sources.description')
     ->join('leads', 'leads.source_id', '=', 'sources.id')
     ->where('leads.status',3)
     ->whereIn('leads.asign_to',$employee_ids)
     ->distinct() // Ensures unique source names
     ->get();    
    $timeZone = Lead::with('source')->where(['status'=>3])
    ->whereIn('asign_to',$employee_ids)
    ->orderBy('timezone', 'asc')
    ->groupBy('timezone')
    ->get();

    $closedon = Lead::where(['status' => 3])
    ->whereIn('asign_to',$employee_ids)
    ->selectRaw('DATE(updated_at) as date')  // Extract the date part
    ->distinct()  // Ensure distinct dates
    ->orderBy('date', 'DESC')  // Order by the extracted date
    ->get()
    ->toArray();
    // Check if the request is an AJAX call from DataTable
    if (request()->ajax()) {
        $query = Lead::with('source')
            ->where('status', 3)->whereIn('asign_to',$employee_ids);
            
        
            if(!empty(request('cName'))){
                    $query->where('company_name', 'LIKE', '%' . request('cName') . '%');
            }

            if(!empty(request('timeZone'))){
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
            ->addColumn('source_description', function ($row) {
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
                             if($row->status == 3){
                             $notesButton .= '<a href="' . url('employee/export/'.$row->id.'/word_single_down') . '?employee_id=&campaign_id=&date_from=&date_to=" class="notes_id">
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
                $leadName =  '<a href="'.url('/leads', [$row->id]).'" target="_blank">' . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';
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
                if($row->status == 1){
                return 'Pending';
                }elseif($row->status == 2){
                return 'Failed';
                }
                else{
                return 'Closed';
                }
            })
            ->rawColumns(['action','prospect_first_name']) // To render HTML in the actions column
            ->make(true);
    }
    $id = '';
    return view('leads.employeeclosedleads',compact('id','comapnyName','timeZone','sourceNames','closedon'));

}


public function leadslist(Request $request)
{
    // Check if the request is an AJAX call from DataTable
    if (request()->ajax()) {
        if(isset($request['status']) && !empty($request['status'])){
        $query = Lead::with('source')
            ->where('status', $request['status']);
            
        }else{
            $query = Lead::with('source');

     $comapnyName = Lead::with('source')->where(['status'=>$request['status']])->orderBy('company_name', 'asc')->groupBy('company_name')->get();
     $timeZone = Lead::with('source')->where(['status'=>$request['status']])->orderBy('timezone', 'asc')->groupBy('timezone')->get();
        }
            if(!empty(request('cName'))){
                    $query->where('company_name', 'LIKE', '%' . request('cName') . '%');
            }

            if(!empty(request('timeZone'))){
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
            ->addColumn('source_description', function ($row) {
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
                             if($row->status == 3){
                             $notesButton .= '<a href="' . url('employee/export/'.$row->id.'/word_single_down') . '?employee_id=&campaign_id=&date_from=&date_to=" class="notes_id">
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
                $leadName =  '<a href="'.url('/leads', [$row->id]).'" target="_blank">' . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';
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
                if($row->status == 1){
                return 'Pending';
                }elseif($row->status == 2){
                return 'Failed';
                }
                else{
                return 'Closed';
                }
            })
            ->rawColumns(['action','prospect_first_name']) // To render HTML in the actions column
            ->make(true);
    }
    

    $comapnyName = Lead::with('source')
    ->where('status', $request['status'])
    ->orderBy('company_name', 'asc')
    ->distinct()
    ->get(['company_name', 'source_id']); 

         $sourceNames = Source::select('sources.source_name','sources.description')
     ->join('leads', 'leads.source_id', '=', 'sources.id')
     ->distinct() // Ensures unique source names
     ->get();    
    $timeZone = Lead::with('source')->where(['status'=>$request['status']])->orderBy('timezone', 'asc')->groupBy('timezone')->get();

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
    return view('leads.adminhomepageleads',compact('id','comapnyName','timeZone','sourceNames','closedon'));

}

    public function getMangerSource()
    {
        $managers = User::where('is_admin', 2)->where('deleted_at', NULL)->where(function ($query) {
            $query->where('manager_type', 1)
                ->orWhereNull('manager_type');
        })->get();
        // dd($data);
        $externalManagers = DB::table('users')->where('is_admin', 2)->where('deleted_at', NULL)->where('manager_type', 2)->get()->toArray();
        return view('sources.list_new')->with(['managers' => $managers, 'externalManagers' => $externalManagers]);
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
            return response()->json(['html' => $html,'source_id'=>$request->source_id]);
        }
    
        // Get all assigned user names in a single query
        $userIds = $assingData->pluck('asign_to')->toArray();
        $users = User::whereIn('id', $userIds)->pluck('name', 'id'); // Fetch names indexed by ID
    
        // Build tooltip HTML
        $tooltipContent = '';
        foreach ($assingData as $assign) {
            $userName = $users[$assign->asign_to] ?? 'Unknown'; // Handle missing users
            $tooltipContent.=" <tr style='#c9d1e3:1px solid black'>
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
                '.$tooltipContent.'
                </tbody>
            </table>';
    
        return response()->json(['html' => $html,'source_id'=>$request->source_id]);
    }
    

        public function import_leads(Request $request)
    {
        // dd($request->source_name);
        $source_id = $request->source_name;
        //$data = Source::where('id')->first();
        $file = request()->file('file');
        if ($request->hasFile('file') && $request->file('file')->isValid()) {


            $importService = new CampaignImport($source_id);
            $importService->importLeads($file);
        }
        return redirect('leads/assign_lead_emp/' . $source_id)->with('success', 'Lead Imported Successfully.');
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

    public function downloadmomreport($id){
    $source = Source::where('id',$id)->first();
    $folderName = 'public/mom/'.$source->source_name.'-'.$source->id; // Folder to be zipped (inside storage/app/)
    $zipFileName = $source->source_name.'_mom_' . time() . '.zip'; // ZIP file name
    $zipPath = storage_path('app/' . $zipFileName); // Path to store ZIP

    // Ensure folder exists
    if (!Storage::exists($folderName)) {
        return back()->with('error', 'Folder not found.');
    }

    // Create a new ZIP Archive
    $zip = new ZipArchive;
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        $files = Storage::files($folderName); // Get all files in folder
        foreach ($files as $file) {
            $filePath = storage_path('app/' . $file);
            $zip->addFile($filePath, basename($file)); // Add file to ZIP
        }

        $zip->close();
    } else {
        return back()->with('error', 'Could not create ZIP file.');
    }

    // Download ZIP & delete after sending
    return response()->download($zipPath)->deleteFileAfterSend(true);

    }

}
