<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\MomReport;
use App\Models\User;
use App\Models\Source;
use App\Models\Relation;
use App\Models\Note;
use App\Models\LhsReport;
use App\Models\conversationType;
use App\Models\LhsFiles;
use Illuminate\Http\Request;
use App\Http\Requests\AssignLeadRequest;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Auth;
class LeadsController extends Controller
{

    public function index()
    {
        return view('leads.index');
    }

    /**
     * Handle the AJAX request for DataTables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSourceLeads(Request $request)
    {
        if ($request->ajax()) {
            // Retrieve leads by source_id, assuming 'source_id' is a foreign key in the leads table
            $leads = Lead::with('source', 'user', 'managerAssigned', 'momReport')
                ->when($request->has('source_id'), function ($query) use ($request) {
                    $query->where('source_id', $request->source_id);  // Apply filter if source_id exists
                })
                ->get();
            return DataTables::of($leads)
                ->editColumn('linkedin_address', function ($data) {
                    $linkedin = $data->linkedin_address;
                    // Check if the LinkedIn URL contains 'linkedin' and is valid
                    $isValidLinkedIn = strpos($linkedin, 'linkedin.com') !== false;
                    if (!$isValidLinkedIn) {
                        $linkedinAddress = '<a href="javascript:void(0)" class="text-danger"><i alt="LinkedIn" class="fa-brands fa-linkedin invalidLinkedin" aria-hidden="true"></i></a>';
                        return $linkedinAddress;
                    }
                    $linkedinurl = filter_var($linkedin, FILTER_VALIDATE_URL) ? $linkedin : 'https://' . $linkedin;
                    $linkedinAddress = '<a href="' . $linkedinurl . '" target="_blank"><i style="color: #000" alt="LinkedIn" class="fa-brands fa-linkedin validLinkedin" aria-hidden="true"></i></a>';
                    return $linkedinAddress;
                })
                ->editColumn('asign_to', function ($data) {
                    $asignTo = $data->user ? $data->user->name : '';
                    return $asignTo;
                })
                ->editColumn('asign_to_manager', function ($data) {
                    $asignTo = $data->managerAssigned ? $data->managerAssigned->name : '';
                    return $asignTo;
                })
                ->editColumn('created_on', function ($data) {
                    if ($data->status == LEAD_STATUS_CLOSED) {
                        $data->created_on = date('d-m-Y', strtotime($data->updated_at));
                    } else {
                        $data->created_on = date('d-m-Y', strtotime($data->created_at));
                    }
                    return $data->created_on;
                })
                ->editColumn('status', function ($data) {
                    switch ($data->status) {
                        case LEAD_STATUS_PENDING:
                            $data->class_status_label = 'pending';
                            $data->status_image = asset('img/common/pending.png');
                            break;
                        case LEAD_STATUS_FAILED:
                            $data->class_status_label = 'failed';
                            $data->status_image = asset('img/common/failed.png');
                            break;
                        case LEAD_STATUS_INPROGRESS:
                            $data->class_status_label = 'in-progress';
                            $data->status_image = asset('img/common/in-progress.png');
                            break;
                        default:
                            $data->class_status_label = 'closed';
                            $data->status_image = asset('img/common/completed.png');
                            break;
                    }
                    $status = '<span class="label ' . $data->class_status_label . '" data-toggle="tooltip" data-placement="top"  style="color:#000;font-size: 15px;"><img style="width: 20px" src="' . $data->status_image . '" alt="completed"><span class="lead_status_sapn">3</span></span>';
                    return $status;
                })
                ->addColumn('actions', function ($data) {
                    // Construct the URLs based on the data's attributes
                    // $data->view_notes_url = route('notes.view', ['id' => $data->id]);
                    $data->view_url = url('/leads', [$data->id]);
                    $data->export_url = $data->status == LEAD_STATUS_CLOSED ? url('/employee/export/' . $data->id . '/word_single_down?employee_id=&campaign_id=&date_from=&date_to=') : 'javascript:void(0)';
                    $data->mom_report_url = isset($data->mom_report['mom_file_path']) ? asset('storage/app/public/mom/' . $data->mom_report['mom_file_path']) : null;
                    $data->edit_url = url('/leads/' . $data->id . '/edit');
                    $downlodMOM = " ";
                    // Set labels for different conditions
                    if ($data->status == LEAD_STATUS_CLOSED) {
                        $data->classDownload = 'downloadLHS';
                        $data->classColor = 'text-success';
                        $data->download_report_icon = 'file_save';
                        if (isset($data->mom_report['mom_file_path'])) {
                            $downlodMOM = '<a href="' . $data->mom_report_url . '">
                                <span class="material-symbols-outlined text-warning downloadMOM">' . $data->download_report_icon . '</span>
                            </a>';
                        }
                    } else {
                        $data->classDownload = 'noLHS';
                        $data->classColor = 'text-danger';
                        $data->download_report_icon = 'file_download_off';
                    }

                    $viewNotes = ' <a href="' . $data->mom_report_url . '">
                        <span class="material-symbols-outlined viewNotes">format_list_bulleted</span>
                    </a>';

                    $viewLead = '<a href="' . route('manager.employees', ['manager_id' => $data->id]) . '">
                        <span class="material-symbols-outlined text-secondary viewLead">visibility</span>
                    </a>';

                    $downlodLHS = '<a href="' . $data->export_url . '">
                        <span class="material-symbols-outlined text-secondary ' . $data->classDownload . ' ' . $data->classColor . '">' . $data->download_report_icon . '</span>
                    </a>';

                    $deleteLink = '<a href="javascript:void(0);" class="delete-manager" data-id="' . $data->id . '">
                        <span class="material-symbols-outlined text-danger deleteLead">delete</span>
                    </a>';

                    return $viewNotes . ' ' . $viewLead . ' ' . $downlodLHS . ' ' . $downlodMOM . ' ' . $deleteLink;
                })
                ->rawColumns(['linkedin_address', 'status', 'actions'])
                ->toJson();
        }
        return response()->json(['error' => 'Invalid request'], 400);
    }


    public function create()
    {
        if (Auth::user()->is_admin == 1) {
            $sourcesData = Lead::where(['asign_to' => auth()->user()->id])
                // ->whereHas('source', function ($query) {
                //     $query->where('is_active', '1');
                // })
                ->with('source')->groupBy('source_id')->get()->toArray();
            $sources = [];

            foreach ($sourcesData as $element) {
                // Check if the element has a "source" key
                if (isset($element['source']) && is_array($element['source'])) {
                    // Add the "source" data to the result array
                    $sources[] = $element['source'];
                }
            }
        } else {
            $sources = Source::select('id', 'source_name', 'description') // Select only necessary columns
                ->where('assign_to_manager', auth()->id()) // Use `auth()->id()` for brevity
                ->where('is_active', 1)
                ->orderBy('source_name')
                ->get();
        }

        return view("leads.create", compact("sources"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $assign = Source::where('id', $request->source_id)->first();

        $approval_status = null;
        if (Auth::user()->is_admin == 1) {
            $approval_status = '2';
        }
        $data = [
            'user_id' => auth()->user()->id,
            'source_id' => $request->source_id,
            'company_name' => $request->company_name,
            'prospect_first_name' => $request->prospect_first_name,
            'prospect_last_name' => $request->prospect_last_name,
            'company_industry' => $request->company_industry,
            'designation' => $request->designation,
            'designation_level' => $request->designation_level,
            'contact_number_1' => $request->contact_number_1,
            'contact_number_2' => $request->contact_number_2,
            'prospect_email' => $request->prospect_email,
            'linkedin_address' => $request->linkedin_address,
            'bussiness_function' => $request->bussiness_function,
            'location' => $request->location,
            'timezone' => $request->timezone,
            'asign_to_manager' => $assign['assign_to_manager'],
            'date_shared' => $request->date_shared,
            'approval_status' => $approval_status,
        ];

        Lead::create($data);
        // Get and print the last executed query

        if (Auth::user()->is_admin == 1) {
            return redirect('leads/unapprovedLeadsemp')->with('success', 'Lead Added Successfully.');
        } else {
            return redirect('leads/assign_lead_emp/' . $request->source_id)->with('success', 'Lead Added Successfully.');
        }
    }
    public function assign_lead_emp($id = null)
    {
        $employees = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1', 'is_active' => 1])->orderBy('name')->get()->toArray();
        $sources = Source::where(function ($query) {
            $query->where(['user_id' => auth()->user()->id])
                ->orWhere(['assign_to_manager' => auth()->user()->id]);
        })
            ->where('is_active', 1) // Condition for is_active = 1
            ->orderBy('source_name')
            ->get()
            ->toArray();
        return view('leads.assign_lead')->with(['employees' => $employees, 'sources' => $sources, 'selectedSource' => $id]);
    }
    public function campname(Request $request)
    {
        // dd('hello');
        // dd($request->camp_id);
        $subLaws = "";
        $table = "";
        $sources = Source::where(['id' => $request->camp_id])->first();
        $User_info = User::where(['id' => $sources->assign_to_manager])->first();
        $src_Descrition = $sources->description;
        $source_name = $sources->source_name;
        if (!empty($sources)) {
            $all_leads = Lead::with('source')->where(['source_id' => $sources->id])->count();
            $total_leads = Lead::with('source')->where(['source_id' => $sources->id])->whereNotNull('asign_to')->count();
            $assign_to_leads = Lead::with('source')->where(['source_id' => $sources->id])->whereNull('asign_to')->count();
        }
        $subLaws = '<div class="append_row col-md-12 ">
           <table class="table">
           <tr>
           <td>Campaign Name</td>
           <td>' . $source_name . ' (' . $sources->description . ')</td>
           <td>Campaign Manager</td>
           <td>' . $User_info->name . '</td>
           </tr>
           <tr>
           <td>Campaign Total Leads</td>
           <td class="getleadTotalcount">' . $all_leads . '</td>
           <td>Campaign Start Date</td>
           <td>' . $sources->start_date . '</td>
           </tr>
           <tr>
           <td>Campaign End Date</td>
           <td>' . $sources->end_date . '</td>
           <td>Campaign Assigned Leads</td>
           <td class="Change_lead_count">' . $total_leads . '</td>
           </tr>
           </table>
       </div>
       <div class="col-sm-6 col-md-6">
       <div class="form-group change_lead">
           <label class="control-label"> Enter Assign Leads Count</label>
           <input type="text" id="start_assign_id" name="start_assign_id" class="form-control"  value=' . $assign_to_leads . ' readonly="">
           <div class="total_lead">
           <input class="switch-input all_leads" id="all_leads" name="edit" type="checkbox" value="all_leads">
            <label for="all_leads">Edit</label>
        </div>
        <span class="error_msg"></span>
       </div>
   </div>';

        $get_table = DB::table('leads')
            ->select('*', DB::raw('COUNT(asign_to) as totalLeads'))
            ->where('source_id', $request->camp_id)
            ->whereNotNull('asign_to')
            ->groupBy('asign_to')
            ->get();
        $table = '<table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
        <tr>
        <th>Campaign Name</th>
        <th>Total Assigned Lead</th>
        <th>Employee Name</th>
        <!--th>Campaign Start Date</th-->
        <!--th>Campaign End Date</th -->
        <th>Action</th>
        </tr>
        </thead>
        <tbody>';

        if (!empty($get_table)) {
            foreach ($get_table as $key => $table_data) {
                $User_info = User::where(['id' => $table_data->asign_to])->first();

                if (isset($User_info->name) && !empty($User_info->name)) {
                    $n = $User_info->name;
                } else {
                    $n = "";
                }

                $table .= '<tr><td class="wraping"> ' . $sources->source_name . ' (' . $sources->description . ') </td>
                <td class="wraping"> ' . $table_data->totalLeads . ' </td>
                <td class="wraping"> ' . $n . ' </td>
                <!--td class="wraping"> ' . $sources->start_date . ' </td-->
                <!--td class="wraping"> ' . $sources->end_date . ' </td-->
                <td class="wraping"><div class="reassigned"><a class="unassigned" href="javascript:void(0);" data-camp="' . $table_data->source_id . '" data-asign="' . $table_data->asign_to . '"><span class="label label-warning">Withdraw</span></a><a href="javascript:void(0);" onclick="reassign(' . $table_data->source_id . ',' . $table_data->totalLeads . ',' . $table_data->asign_to . ');" data-id="' . $table_data->source_id . '"  data-total="' . $table_data->totalLeads . '" data-asign="' . $table_data->asign_to . '"><span class="label label-warning">Reassign</span></a></div></td>
                </tr>';
            }
        } else {
            $table .= '<tr><td class="wraping">  </td>
            <td class="wraping">  </td>
            <td class="wraping"> Data Not Found </td>
            <td class="wraping">  </td>
            <td class="wraping">  </td><td class="wraping">  </td></tr>';
        }
        $table .= '</tbody> </table>';

        $sampleArray = array(
            'data' => $subLaws,
            'table' => $table
        );
        // echo"<pre>";
        // print_r($sampleArray);
        // die();
        return json_encode($sampleArray);
    }
    public function Unassigned(Request $request)
    {
        // Fetch the parameters
        $employee_id = $request->user_id;
        $campaign_id = $request->camp_id;

        // Optional: Validate the parameters (if needed)
        if (empty($employee_id) || empty($campaign_id)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid parameters provided.'], 400);
        }

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Perform the update operation for the leads
            $updateResult = Lead::where('source_id', $campaign_id)
                ->where('asign_to', $employee_id)
                ->update(['asign_to' => null]);

            // If any rows were updated, delete the related relation
            if ($updateResult > 0) {
                Relation::where('assign_to_cam', $campaign_id)
                    ->where('assign_to_employee', $employee_id)
                    ->delete();
            }

            // Commit the transaction if everything is successful
            DB::commit();

            // Return a success response
            return response()->json(['status' => 'success', 'message' => 'Employee unassigned successfully.'], 200);

        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error for debugging (optional but recommended)
            \Log::error('Error unassigning employee: ' . $e->getMessage());

            // Return an error response
            return response()->json(['status' => 'error', 'message' => 'An error occurred while unassigning the employee.'], 500);
        }
    }
    public function Reassigned(Request $request)
    {
        $prev_id = $request->user_id;
        $campaign_id = $request->camp_id;
        $assign_leads = $request->leads;
        $employee_id = $request->new_id;

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Delete previous relation and unassign the leads
            Relation::where('assign_to_cam', $campaign_id)
                ->where('assign_to_employee', $prev_id)
                ->delete();

            Lead::where('source_id', $campaign_id)
                ->where('asign_to', $prev_id)
                ->update(['asign_to' => null]);

            // Prepare data for the new relation
            $data = [
                'assign_to_cam' => $campaign_id,
                'assign_to_employee' => $employee_id,
                'assign_to_manager' => auth()->user()->id,
                'lead_assigned' => $assign_leads,
            ];

            // Check if the relation already exists, and update if necessary
            $relation = Relation::firstOrNew([
                'assign_to_employee' => $employee_id,
                'assign_to_cam' => $campaign_id,
            ]);

            if ($relation->exists) {
                // If relation exists, increment the lead_assigned count
                $relation->increment('lead_assigned', $assign_leads);
            } else {
                // If no relation exists, create a new one
                $relation->fill($data)->save();
            }

            // Get unassigned leads and assign to the new employee
            $get_assign_records = Lead::where('source_id', $campaign_id)
                ->whereNull('asign_to')
                ->take($assign_leads)
                ->get();

            // Bulk update to assign the leads to the new employee
            $leadIds = $get_assign_records->pluck('id');
            Lead::whereIn('id', $leadIds)->update(['asign_to' => $employee_id]);

            // Commit the transaction
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Leads reassigned successfully.']);

        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            // Log the exception (optional but recommended)
            \Log::error('Error during reassigning leads: ' . $e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'An error occurred while reassigning the leads.'], 500);
        }
    }
    public function assingParticalurleads(Request $request)
    {
        $employee_id = $request->emp_id;
        $assign_leads = $request->assign_leads;
        $campaign_id = $request->cmp_id;

        // Prepare the relation data for assignment
        $data = [
            'assign_to_cam' => $campaign_id,
            'assign_to_employee' => $employee_id,
            'assign_to_manager' => auth()->user()->id,
            'lead_assigned' => $assign_leads,
        ];

        // Use firstOrCreate or update the relation directly
        $relation = Relation::updateOrCreate(
            ['assign_to_employee' => $employee_id, 'assign_to_cam' => $campaign_id],
            ['lead_assigned' => \DB::raw('lead_assigned + ' . $assign_leads)]
        );

        // Get the source data (source_name, description)
        $sources_data = Source::find($campaign_id);

        // Get the unassigned leads and assign them to the employee in bulk
        $unassignedLeads = Lead::where('source_id', $campaign_id)
            ->whereNull('asign_to')
            ->take($assign_leads)
            ->pluck('id');

        // Bulk update the leads to assign to the employee
        Lead::whereIn('id', $unassignedLeads)->update(['asign_to' => $employee_id]);

        // Get the updated leads count
        $update_leads_count = Lead::where('source_id', $campaign_id)->whereNull('asign_to')->count();

        // Get the table data in one query to avoid multiple queries in the loop
        $assignedLeadsData = DB::table('leads')
            ->select('source_id', 'asign_to', DB::raw('COUNT(asign_to) as totalLeads'))
            ->where('source_id', $campaign_id)
            ->whereNotNull('asign_to')
            ->groupBy('asign_to')
            ->get();

        // Get the names of the users assigned to the campaign
        $userNames = User::whereIn('id', $assignedLeadsData->pluck('asign_to'))->pluck('name', 'id');

        // Generate the table HTML
        $table = '<table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Campaign Name</th>
                    <th>Total Assigned Lead</th>
                    <th>Employee Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($assignedLeadsData as $table_data) {
            $employee_name = $userNames[$table_data->asign_to] ?? ''; // Get the employee name or default to empty

            $table .= '<tr>
                <td class="wraping"> ' . $sources_data->source_name . ' (' . $sources_data->description . ') </td>
                <td class="wraping"> ' . $table_data->totalLeads . ' </td>
                <td class="wraping"> ' . $employee_name . ' </td>
                <td class="wraping">
                    <div class="reassigned">
                        <a class="unassigned" href="javascript:void(0);" data-camp="' . $table_data->source_id . '" data-asign="' . $table_data->asign_to . '">
                            <span class="label label-warning">Withdraw</span>
                        </a>
                        <a data-toggle="modal" data-target="#RevertModel" class="RevertModel" href="javascript:void(0);" data-id="' . $table_data->source_id . '" data-total="' . $table_data->totalLeads . '" data-asign="' . $table_data->asign_to . '">
                            <span class="label label-warning">Reassign</span>
                        </a>
                    </div>
                </td>
            </tr>';
        }

        $table .= '</tbody></table>';

        return response()->json([
            'success' => true,
            'data' => $update_leads_count,
            'table' => $table,
        ]);
    }

    // This fuction is used to get the list of unapproved leads for default listing.....
    public function unapprovedLeads()
    {
        // Change '10' to the number of items per page you prefer
        return view('leads.unapproved_lead');
    }

    public function unapprovedLeadsemp()
    {
        // Change '10' to the number of items per page you prefer
        return view('leads.anapproveemployee_lead');
    }

    // This fuction is used to get the list of unapproved leads for pagination content listing.....
    public function unapprovedManagerLeadsajaxPagination(Request $request)
    {
        if ($request->ajax()) {
            ## Read value
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length"); // Rows display per page

            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');

            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
            $columnSortColumn = $order_arr[0]['column']; // asc or desc
            $searchValue = $search_arr['value']; // Search value


            $orderByColumn = '';
            if ($columnSortColumn == '2') {
                $orderByColumn = 'company_name';
            } else if ($columnSortColumn == "3") {
                $orderByColumn = 'prospect_first_name';
            } else if ($columnSortColumn == "4") {
                $orderByColumn = 'designation';
            } else if ($columnSortColumn == "5") {
                $orderByColumn = 'created_at';
            }

            if (Auth::user()->is_admin == 1) {
                $totalRecords = Lead::where('user_id', auth()->user()->id)->where('approval_status', '2')->count();

                $baseQuery = Lead::with('source')->where('user_id', auth()->user()->id)->where('approval_status', '2');
            } else {
                $totalRecords = Lead::where('asign_to_manager', auth()->user()->id)->where('approval_status', '2')->count();

                $baseQuery = Lead::with('source')->where('asign_to_manager', auth()->user()->id)->where('approval_status', '2');
            }

            if (isset($orderByColumn) && !empty($orderByColumn)) {
                $leadsData = $baseQuery->orderBy($orderByColumn, $columnSortOrder);
            } else {
                $leadsData = $baseQuery->orderBy('created_at', 'desc');
            }

            $leadsData = $baseQuery->skip($start)->take($rowperpage)->get()->toArray();

            if ($columnSortColumn == "6") {
                usort($leadsData, function ($a, $b) use ($columnSortOrder) {
                    if ($columnSortOrder === 'asc') {
                        return strcmp($a['source']['source_name'], $b['source']['source_name']);
                    } else {
                        return strcmp($b['source']['source_name'], $a['source']['source_name']);
                    }
                });
            }

            $sources = Source::orderBy('source_name')->get()->toArray();
            $campaignsOptionsHtml = '<option value="">Select a source</option>';
            foreach ($sources as $source) {
                $campaignsOptionsHtml .= '<option value="' . $source["id"] . '">' . $source["source_name"] . ' ' . $source["description"] . '</option>';
            }
            // Transform the data to match the expected structure
            $formattedData = [];
            foreach ($leadsData as $lead) {
                $campaignsHtml = '<span id="icons_' . $lead["id"] . '" class="group_actions"><i class="fa fa-check green-color onchange_element_approve" style="color: #006400;" data-id="' . $lead["id"] . '"  data-emp-id="' . $lead["user_id"] . '" ></i><i class="fa fa-times red-color onchange_element_cross" style="color: red;"  data-id="' . $lead["id"] . '"></i></span>';
                $campaignsHtml .= '<select class="unapproved_lead" name="source_id" id="' . $lead["id"] . '" style="width:140px" data-id="' . $lead["source_id"] . '">';
                $campaignsHtml .= $campaignsOptionsHtml;
                $campaignsHtml .= '</select>';
                $userDetails = User::find($lead["user_id"]);
                $employeeName = '';
                if (isset($userDetails) && !empty($userDetails)) {
                    $employeeName = $userDetails['name'];
                }
                $var = $lead["linkedin_address"];
                if (strpos($var, 'linkedin') == -1) {
                    $linkdin = '<td><a href="javascript:void(0)" ><i style="color: #000" alt="LinkedIn" title="LinkedIn Address Not Valid" class="fa-brands fa-linkedin" aria-hidden="true"></i></a></td>';
                } else {
                    $linkdin = '<td><a href="" target="_blank" ><i  alt="LinkedIn" title="LinkedIn" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
                       </td>';
                }
                $formattedData[] = [
                    'action' => $campaignsHtml,
                    'employee_name' => trim($employeeName),
                    'company_name' => $lead["company_name"],
                    'prospect_full_name' => $lead["prospect_first_name"] . ' ' . $lead["prospect_last_name"],
                    'designation' => $lead["designation"],
                    'created_at' => date('d M, Y', strtotime($lead["created_at"])),
                    'source_name' => $lead['source']["source_name"] . ' ' . $lead['source']["description"],
                    'LinkedIn' => $linkdin
                ];
            }

            // Return the formatted data as JSON
            return response()->json([
                'data' => $formattedData,
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords, // Required by DataTables
                'recordsFiltered' => $totalRecords,
            ]);
        }
    }

    public function unapproved_emp_leads_list_pagination(Request $request)
    {
        if ($request->ajax()) {
            ## Read value
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length"); // Rows display per page

            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');

            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
            $columnSortColumn = $order_arr[0]['column']; // asc or desc
            $searchValue = $search_arr['value']; // Search value


            $orderByColumn = '';
            if ($columnSortColumn == '2') {
                $orderByColumn = 'company_name';
            } else if ($columnSortColumn == "3") {
                $orderByColumn = 'prospect_first_name';
            } else if ($columnSortColumn == "4") {
                $orderByColumn = 'designation';
            } else if ($columnSortColumn == "5") {
                $orderByColumn = 'created_at';
            }

            if (Auth::user()->is_admin == 1) {
                $totalRecords = Lead::where('user_id', auth()->user()->id)->where('approval_status', '2')->count();

                $baseQuery = Lead::with('source')->where('user_id', auth()->user()->id)->where('approval_status', '2');
            }

            if (isset($orderByColumn) && !empty($orderByColumn)) {
                $leadsData = $baseQuery->orderBy($orderByColumn, $columnSortOrder);
            } else {
                $leadsData = $baseQuery->orderBy('created_at', 'desc');
            }

            $leadsData = $baseQuery->skip($start)->take($rowperpage)->get()->toArray();

            if ($columnSortColumn == "6") {
                usort($leadsData, function ($a, $b) use ($columnSortOrder) {
                    if ($columnSortOrder === 'asc') {
                        return strcmp($a['source']['source_name'], $b['source']['source_name']);
                    } else {
                        return strcmp($b['source']['source_name'], $a['source']['source_name']);
                    }
                });
            }

            $sources = Source::orderBy('source_name')->get()->toArray();
            $campaignsOptionsHtml = '<option value="">Select a source</option>';
            foreach ($sources as $source) {
                $campaignsOptionsHtml .= '<option value="' . $source["id"] . '">' . $source["source_name"] . ' ' . $source["description"] . '</option>';
            }
            // Transform the data to match the expected structure
            $formattedData = [];
            foreach ($leadsData as $lead) {
                $campaignsHtml = '<span id="icons_' . $lead["id"] . '" class="group_actions"><i class="fa fa-check green-color onchange_element_approve" style="color: #006400;" data-id="' . $lead["id"] . '"  data-emp-id="' . $lead["user_id"] . '" ></i><i class="fa fa-times red-color onchange_element_cross" style="color: red;"  data-id="' . $lead["id"] . '"></i></span>';
                $campaignsHtml .= '<select class="unapproved_lead" name="source_id" id="' . $lead["id"] . '" style="width:140px" data-id="' . $lead["source_id"] . '">';
                $campaignsHtml .= $campaignsOptionsHtml;
                $campaignsHtml .= '</select>';
                $userDetails = User::find($lead["user_id"]);
                $employeeName = '';
                if (isset($userDetails) && !empty($userDetails)) {
                    $employeeName = $userDetails['name'];
                }
                $var = $lead["linkedin_address"];
                if (strpos($var, 'linkedin') == -1) {
                    $linkdin = '<td><a href="javascript:void(0)" ><i style="color: #000" alt="LinkedIn" title="LinkedIn Address Not Valid" class="fa-brands fa-linkedin" aria-hidden="true"></i></a></td>';
                } else {
                    $linkdin = '<td><a href="" target="_blank" ><i  alt="LinkedIn" title="LinkedIn" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
                       </td>';
                }
                $formattedData[] = [
                    'company_name' => $lead["company_name"],
                    'prospect_full_name' => $lead["prospect_first_name"] . ' ' . $lead["prospect_last_name"],
                    'designation' => $lead["designation"],
                    'created_at' => date('d M, Y', strtotime($lead["created_at"])),
                    'source_name' => $lead['source']["source_name"] . ' ' . $lead['source']["description"],
                    'LinkedIn' => $linkdin
                ];
            }

            // Return the formatted data as JSON
            return response()->json([
                'data' => $formattedData,
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords, // Required by DataTables
                'recordsFiltered' => $totalRecords,
            ]);
        }
    }
    public function updateApprovalStatus(Request $request)
    {
        if ($request->ajax()) {
            $leadId = $_POST['leadId'];
            $sourceId = $_POST['sourceId'];
            $status = $_POST['status'];
            $user_id = $_POST['user_id'];
            try {
                if (isset($leadId) && !empty($leadId)) {
                    if (isset($sourceId) && !empty($sourceId)) {
                        $data1 = array(
                            'source_id' => $sourceId,
                            'approval_status' => 1,
                            'asign_to' => $user_id
                        );
                        if ($status == 'approved') {
                            Lead::where('id', $leadId)->update($data1);
                            return response()->json(['success' => true, 'message' => 'Lead Has Been Approved Successfully!']);
                        } else {
                            Lead::where('id', $leadId)->update($data1);
                            Lead::where('id', $leadId)->delete();
                            return response()->json(['success' => true, 'message' => 'Lead Request Has Been Cancelled Successfully!']);
                        }
                    } else {
                        return response()->json(['success' => false, 'message' => 'Campaign ID is not provided or empty!']);
                    }

                } else {
                    return response()->json(['success' => false, 'message' => 'Lead ID is not provided or empty!']);
                }
            } catch (\Exception $e) {
                // Log the exception or handle it as required
                return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
            }
        }
    }

    public function leadview($id)
    {
        $data = Lead::where(['source_id' => $id])->with('source')->with('feedback')->get()->toArray();
        $sources = Source::where(['user_id' => auth()->user()->id])->orWhere(['assign_to_manager' => auth()->user()->id])->select('id', 'source_name', 'description')->orderBy('source_name')->get()->toArray();
        $source_ids = $id;
        return view('leads.leadview')->with(['data' => $data, 'sources' => $sources, 'source_ids' => $source_ids]);
    }

    public function getLeadsData(Request $request, $id = null)
    {
    
        $id = $request->source_id;
        if ($request->ajax()) {
            // Fetch leads with relationships and filters
            $data = Lead::join('sources', 'sources.id', 'leads.source_id')->where(['source_id' => $id])->with('source')->with('feedback')
                ->select([
                    'leads.id',
                    'source_id',
                    'company_name',
                    'prospect_first_name',
                    'prospect_last_name',
                    'linkedin_address',
                    'timezone',
                    'designation',
                    'contact_number_1',
                    'leads.created_at',
                    'status',
                ]);

            return DataTables::of($data)
                ->addColumn('campaign_name', function ($row) {
                    return $row->source ? $row->source->source_name : '';
                })
                ->addColumn('prospect_name', function ($row) {
                    $name = $row->prospect_first_name . ' ' . $row->prospect_last_name;
                    $linkedin = $row->linkedin_address;
                    $linkedinLink = $linkedin && (strpos($linkedin, 'http://') === 0 || strpos($linkedin, 'https://') === 0)
                        ? $linkedin
                        : 'https://' . $linkedin;

                    return "<a href='/leads/{$row->id}' target='_blank'>{$name}</a> " .
                        (strpos($linkedin, 'linkedin') !== false
                            ? "<a href='{$linkedinLink}' target='_blank'><i class='fa-brands fa-linkedin'></i></a>"
                            : "<i class='fa-brands fa-linkedin' title='LinkedIn Address Not Valid'></i>");
                })
                ->addColumn('status', function ($row) {
                    $statuses = [
                        1 => ['title' => 'Pending', 'icon' => 'pending.png'],
                        2 => ['title' => 'Failed', 'icon' => 'failed.png'],
                        3 => ['title' => 'Closed', 'icon' => 'completed.png'],
                        4 => ['title' => 'In Progress', 'icon' => 'in-progress.png'],
                    ];
                    $status = $statuses[$row->status] ?? null;
                    return $status
                        ? "<span data-toggle='tooltip' title='{$status['title']}'><img style='width: 20px' src='/admin/assets/images/{$status['icon']}' alt='{$status['title']}'></span>"
                        : '';
                })
                ->addColumn('action', function ($row) {
                    return "<a href='/leads/{$row->id}' target='_blank'><i class='fa fa-eye' style='color:black'></i></a>";
                })
                ->rawColumns(['prospect_name', 'status', 'action'])
                ->make(true);
        }


    }


    public function closed(Request $request)
    {
        if ($request->ajax()) {
            $query = Lead::with([
                'source',
                'momReport',
                'notes' => function ($query) {
                    $query->latest()->limit(1); // Eager load only the latest note to optimize query
                }
            ])
                ->where(['asign_to' => auth()->user()->id])
                ->where(['status' => '3'])
                ->whereHas('source', function ($q) {
                    $q->where('is_active', 1);
                })
                ->orderBy('id', 'DESC');

            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    $notesButton = '<a onclick="shownoteslist(' . $data->id . ')" class="notes_id" data-toggle="modal" data-target="#largeModal"><i class="fa fa-eye label-new" aria-hidden="true"></i></a>';
                    $quickNoteButton = '<a onclick="showaddmodal(' . $data->id . ')" data-toggle="modal" ><i class="fa fa-comment label-new" aria-hidden="true"></i></a>';
                    return $notesButton . ' ' . $quickNoteButton;
                })
                ->editColumn('updated_at', function ($data) {
                    return $data->updated_at->format('d M, Y H:i:s');
                })
                ->addColumn('last_updated_note', function ($data) {
                    // Get the latest note
                    $latestNote = $data->notes->first();
                    return $latestNote && strlen($latestNote->feedback) > 20
                        ? substr($latestNote->feedback, 0, 20) . '...'
                        : $latestNote->feedback ?? '';
                })
                ->addColumn('options', function ($data) {
                    // Avoid querying LhsReport and MomReport multiple times for the same lead
                    $getLhsReport = $data->lhsReport; // Already eager loaded
    
                    $actionHtml = '';

                    if ($getLhsReport) {
                        $actionHtml .= '
                        <a href="' . url('/lhs_report/view_lhs', [$data->id]) . '">
                            <span class="label" data-toggle="tooltip" data-placement="top" title="View LHS Report" style="color:#000;font-size: 15px;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </a>
                        <a href="' . url('/lhs_report/edit', [$data->id]) . '">
                            <span class="label" data-toggle="tooltip" data-placement="top" title="Edit LHS Report" style="color:#000;font-size: 15px;">
                                <i class="fa fa-pencil"></i>
                            </span>
                        </a>
                    ';

                        // Check for MOM report and file path
                        $momreport = $data->momReport; // Already eager loaded
                        if (empty($momreport['mom_file_path'])) {
                            $actionHtml .= '
                            <a href="' . route('employee.show_mom', [$data->id]) . '">
                                <span class="label" data-toggle="tooltip" data-placement="top" title="Create MOM Report" style="color:#000;font-size: 15px;">
                                    <i class="fa fa-file-text-o"></i>
                                </span>
                            </a>';
                        } else {
                            if (isset($momreport['mom_file_path'])) {
                                $actionHtml .= '
                                <a href="' . asset('storage/' . $momreport['mom_file_path']) . '">
                                    <span class="label" data-toggle="tooltip" data-placement="top" title="MOM Download" style="color:#55ce63;font-size: 15px;">
                                        <i class="ti-download"></i>
                                    </span>
                                </a>';
                            }
                        }
                    } else {
                        $actionHtml .= '
                        <a href="' . url('/employee/lhs_report', [$data->id]) . '">
                            <i class="fa fa-plus" title="Add LHS Report"></i>
                        </a>';
                    }

                    return $actionHtml;
                })
                ->rawColumns(['action', 'last_updated_note', 'options'])
                ->make(true);
        }

        return view('leads.closed');
    }

    public function add_note(Request $request)
    {
        $status = Lead::where('id', $request->lead_id)->first();
        $data = array(
            'user_id' => auth()->user()->id,
            'lead_id' => $request->lead_id,
            'source_id' => $request->source_id,
            'status' => $status['status'],
            'reminder_time' => $request->reminder_time,
            'reminder_date' => $request->reminder_date,
            'reminder_for' => $request->reminder_for,
            'feedback' => $request->feedback,
            'phone_number' => $request->phone_number,
        );
        Note::create($data);
        // dd(config('app.timezone'));
        $date = date('Y-m-d H:i:s');
        $notecreatedat = \Carbon\Carbon::parse($date)
            ->addHours(1)
            ->addMinutes(3)
            ->format('Y-m-d H:i:s');
        Lead::where('id', $request->lead_id)->update(array('note_created_date' => $notecreatedat));
        return response()->json(['success' => 'Note Added Successfully']);
        // }
    }


    public function notes_view(Request $request)
    {
        $notes_data = Lead::where('id', $request->lead_id)
            ->with('source')
            ->with([
                'notes' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])
            ->first();

        $table = '
        <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
        <tr>
        <th  width="400">Note</th>
        
        <th>Comment Added Date </th>
        <th>Converstation Type</th>
        <th>Reminder Date</th>  
    </tr>
        </thead>
        <tbody>';
        if (!empty($notes_data)) {
            foreach ($notes_data['notes'] as $key => $table_data) {
                if (isset($table_data->reminder_date) && !empty($table_data->reminder_date)) {
                    $dateData = date("d-m-Y", strtotime($table_data->reminder_date));
                } else {
                    $dateData = "N/A";
                }
                $date = \Carbon\Carbon::parse($table_data->created_at);
                $table .= '<tr>
                <td class="wraping notes_comment" ><p style="white-space: initial; max-height: 100px; overflow-y : auto;";> ' . $table_data->feedback . '</p> </td>
                <td class="wraping"> ' . $date->format('Y-m-d H:i') . ' </td>
                <td class="wraping"> ' . $table_data->reminder_for . ' </td>
                <td class="wraping"> ' . $dateData . ' </td>
                </tr>';
            }
        } else {
            $table .= '<tr>
                <td class="wraping">  </td>
                <td class="wraping">  </td>
                <td class="wraping"> Data Not Found </td>
                <td class="wraping">  </td>
            </tr>';
        }
        return response()->json(['notes_data' => $notes_data, 'table' => $table]);
        // return view('notes.view')->with(['data'=>$data]);
    }


    public function failed(Request $request)
    {
        if ($request->ajax()) {
            // Eager load 'source' and 'notes' with latest feedback
            $query = Lead::with([
                'source',
                'notes' => function ($query) {
                    $query->latest()->limit(1); // Load only the latest note to improve performance
                }
            ])
                ->where('asign_to', auth()->user()->id)
                ->where('status', '2')
                ->whereHas('source', function ($q) {
                    $q->where('is_active', 1);
                })
                ->orderBy('id', 'DESC');

            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    // Buttons for notes interaction
                    $notesButton = '<a onclick="shownoteslist(' . $data->id . ')" class="notes_id" data-toggle="modal" data-target="#largeModal"><i class="fa fa-eye label-new" aria-hidden="true"></i></a>';
                    $quickNoteButton = '<a onclick="showaddmodal(' . $data->id . ')" data-toggle="modal" ><i class="fa fa-comment label-new" aria-hidden="true"></i></a>';
                    return $notesButton . ' ' . $quickNoteButton;
                })
                ->editColumn('updated_at', function ($data) {
                    return $data->updated_at->format('d M, Y H:i:s');
                })
                ->addColumn('last_updated_note', function ($data) {
                    // Retrieve the latest note feedback from the eager-loaded relationship
                    $latestNote = $data->notes->first();
                    return $latestNote && strlen($latestNote->feedback) > 20
                        ? substr($latestNote->feedback, 0, 20) . '...'
                        : $latestNote->feedback ?? '';
                })
                ->addColumn('options', function ($data) {
                    // Eager load LhsReport for performance and avoid querying per row
                    $getLhsReport = LhsReport::where('lead_id', $data->id)->first();

                    $actionHtml = '<span class="label label-info" onclick="showstatusmodal(' . $data->id . ')" data-toggle="modal" data-target="#status-modal">Change Status</span>';

                    return $actionHtml;
                })
                ->rawColumns(['action', 'last_updated_note', 'options'])
                ->make(true);
        }

        return view('leads.failed');
    }

    public function in_progress(Request $request)
    {
        if ($request->ajax()) {
            // Eager load the relationships we need (source and notes)
            $query = Lead::with([
                'source',
                'notes' => function ($query) {
                    $query->latest()->limit(1); // Eager load the latest note only
                }
            ])
                ->where('asign_to', auth()->user()->id)
                ->where('status', '4')
                ->whereHas('source', function ($q) {
                    $q->where('is_active', 1);
                })
                ->orderBy('id', 'DESC');

            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    // Buttons for notes interaction
                    $notesButton = '<a onclick="shownoteslist(' . $data->id . ')" class="notes_id" data-toggle="modal" data-target="#largeModal"><i class="fa fa-eye label-new" aria-hidden="true"></i></a>';
                    $quickNoteButton = '<a onclick="showaddmodal(' . $data->id . ')" data-toggle="modal" ><i class="fa fa-comment label-new" aria-hidden="true"></i></a>';
                    return $notesButton . ' ' . $quickNoteButton;
                })
                ->editColumn('updated_at', function ($data) {
                    return $data->updated_at->format('d M, Y H:i:s');
                })
                ->addColumn('last_updated_note', function ($data) {
                    // Retrieve the latest note feedback
                    $latestNote = $data->notes->first(); // Already eager loaded
                    return $latestNote && strlen($latestNote->feedback) > 20
                        ? substr($latestNote->feedback, 0, 20) . '...'
                        : $latestNote->feedback ?? '';
                })
                ->addColumn('options', function ($data) {
                    // Eager load LhsReport and avoid querying inside the column
                    $actionHtml = '
                    <span class="label label-info" onclick="showstatusmodal(' . $data->id . ')" data-toggle="modal" data-target="#status-modal">Change Status</span>';
                    return $actionHtml;
                })
                ->rawColumns(['action', 'last_updated_note', 'options'])
                ->make(true);
        }

        return view('leads.in_progress');
    }


    public function changeStatus(Request $request)
    {
        $notesCountObj = Note::where(['lead_id' => $request->lead_id]);
        $notesCount = $notesCountObj->count();
        $total_Lhsreport_count = LhsReport::where(['lead_id' => $request->lead_id])->count();
        if ($notesCount == 0) {
            $html = '<li class="error_list"><span class="tab">Please add a notes first. If you want to create LHS, conversation type should be "meeting set-up"</span></li>';
            return response()->json(['error' => 'Please add a note first.', 'lhs_link' => $html]);
        } else if ($total_Lhsreport_count == 0 && $request->status == 3) {
            if ($notesCount == 0) {
                return response()->json(['error' => 'Please add a note first with conversation type meeting set-up to make LHS.']);
            } else {
                $conversationType = $notesCountObj->first('reminder_for')->reminder_for;
                $html = '';
                // $hostname = Config::get('app.url');
                $hostnameNew = "http://203.190.154.132";//Config::get('app.url');
                $Current_url = $hostnameNew . "/employee/lhs_report/" . $request->lead_id . "?status=" . $request->status;
                $html = '<li class="error_list"><span class="tab">Please add  LHS Report first.</span><a href="' . $Current_url . '" ><span class="tab">Click here to add Lhs Report</span></a></li>';
                return response()->json(['error' => 'Please add LHS Report first.', 'lhs_link' => $html]);
            }

        } else {
            $create_note = Note::where('lead_id', $request->lead_id)->orderBy('updated_at', 'desc')
                ->first();
            $data = array(
                'user_id' => $create_note['user_id'],
                'lead_id' => $create_note['lead_id'],
                'source_id' => $create_note['source_id'],
                'status' => $request->status,
                'reminder_time' => $create_note['reminder_time'],
                'reminder_date' => $create_note['reminder_date'],
                'reminder_for' => $create_note['reminder_for'],
                'feedback' => $create_note['feedback'],
                'phone_number' => $create_note['phone_number'],
            );
            Note::create($data);
            if($request->status == 3){
                Lead::where('id', $request->lead_id)->update(['status' => $request->status, 'is_notify' => 1, 'is_read' => 1,'closed_on'=>Carbon::now()]);

            }else{
                Lead::where('id', $request->lead_id)->update(['status' => $request->status, 'is_notify' => 1, 'is_read' => 1]);

            }
            $notification_count = Lead::where('is_notify', '!=', 0)->count();
            if ($request->status == 2) {
                $status = 'failed';
            } elseif ($request->status == 3) {
                $status = 'close';
            } else {
                $status = 'in_progress';
            }
            return response()->json(['success' => 'Updated Successfully.', 'notification_count' => $notification_count, 'status' => $status]);
        }
    }

    public function showlead($id)
    {
        $data = Lead::where(['id' => $id])->with('source')->with('notes')->with('user')->first()->toArray();
        $record = Lead::where(['id' => $id])->with('notes')->orderBy('updated_at', 'DESC')->first();
        $lead_ID = $id;
        //dd( $record);

        $conversationTypes = conversationType::orderBy('type')->get()->toArray();


        $accessibleFields = '';
        $accessibleFieldsData = '';
        if (auth()->user()->manager_type == 2) {
            $accessibleFields = 'consider';
            if (isset($data['source']['accessible_fields']) && !empty($data['source']['accessible_fields'])) {
                $accessibleFieldsData = unserialize($data['source']['accessible_fields']);
            }
        }

        $fiedsArray = array();
        $fiedsArray['phoneAccessible'] = true;
        $fiedsArray['emailAccessible'] = true;
        $fiedsArray['linkdinAccessible'] = true;

        if ($accessibleFields == 'consider') {
            if (isset($accessibleFieldsData) && !empty($accessibleFieldsData)) {
                if (in_array("email", $accessibleFieldsData)) {
                    $fiedsArray['emailAccessible'] = true;
                } else {
                    $fiedsArray['emailAccessible'] = false;
                }

                if (in_array("phone_no", $accessibleFieldsData)) {
                    $fiedsArray['phoneAccessible'] = true;
                } else {
                    $fiedsArray['phoneAccessible'] = false;
                }

                if (in_array("linkedIn", $accessibleFieldsData)) {
                    $fiedsArray['linkdinAccessible'] = true;
                } else {
                    $fiedsArray['linkdinAccessible'] = false;
                }

            } else {
                $fiedsArray['phoneAccessible'] = false;
                $fiedsArray['emailAccessible'] = false;
                $fiedsArray['linkdinAccessible'] = false;
            }
        } else {
            $fiedsArray['phoneAccessible'] = true;
            $fiedsArray['emailAccessible'] = true;
            $fiedsArray['linkdinAccessible'] = true;
        }

        $lhsFiles = LhsFiles::where('lead_id', $id)->first();
        return view('leads.show')->with([
            'data' => $data,
            'record' => $record,
            'lead_ID' => $id,
            'fiedsArray' => $fiedsArray,
            'conversationTypes' => $conversationTypes,
            'lhsFiles' => $lhsFiles,
        ]);
    }


    public function edit($id)
    {

        $sources = Source::select('id', 'source_name')->orderBy('source_name')->get()->toArray();
        $data = Lead::where(['id' => $id])->first();
        return view('leads.edit')->with(['data' => $data, 'sources' => $sources]);
    }


    public function update(Request $request, $id)
    {
        $input = $request->all();
        $data = Lead::find($id);
        $data->designation_level = $input['designation_level'];
        $data->designation = $input['designation'];
        $data->company_name = $input['company_name'];
        $data->contact_number_2 = $input['contact_number_2'];
        $data->company_industry = $input['company_industry'];
        $data->bussiness_function = $input['bussiness_function'];
        $data->prospect_name = $input['prospect_name'];
        $data->linkedin_address = $input['linkedin_address'];
        $data->prospect_first_name = $input['prospect_first_name'];
        $data->prospect_last_name = $input['prospect_last_name'];
        $data->prospect_email = $input['prospect_email'];
        $data->contact_number_1 = $input['contact_number_1'];
        $data->timezone = $input['timezone'];
        $data->save();
        return redirect()->back()->with('success', 'Lead Updated Successfully.');

    }

    public function delete($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();
        return redirect()->back()->with('success', 'Lead Deleted Successfully.');
    }

    public function assign()
    {

        $admin = User::where(['is_admin' => Null, 'id' => auth()->user()->id])->first();
        if (!empty($admin)) {

            $employees = User::where(['user_id' => auth()->user()->id, 'is_admin' => '2'])->orderBy('name')->get()->toArray();
            $assing_checkemployees = User::where(['is_admin' => '1'])->orderBy('name')->get()->toArray();
            $data = Lead::with('source')->where(['user_id' => auth()->user()->id, 'status' => '1', 'asign_to' => NULL])->whereNull('asign_to_manager')->get()->toArray();
            //dd($employees);
        } else {
            //$employees = User::where(['user_id'=>auth()->user()->id,'is_admin'=>'1'])->get()->toArray();
            $employees = User::where(['is_admin' => '1'])->orderBy('name')->get()->toArray();
            $assing_checkemployees = User::where(['is_admin' => '1'])->orderBy('name')->get()->toArray();
            $data = Lead::with('source')->where(['asign_to_manager' => auth()->user()->id, 'status' => '1', 'asign_to' => NULL])->orWhere(['user_id' => auth()->user()->id])->get()->toArray();
        }
        //dd( $assing_checkemployees);
        // $data = Lead::with('source')->where(['user_id'=>auth()->user()->id,'status'=>'1','asign_to'=>NULL])->get()->toArray();
        //  $employees = User::where(['user_id'=>auth()->user()->id,'is_admin'=>'1'])->get()->toArray();
        return view('leads.assign')->with(['employees' => $employees, 'data' => $data, 'assign_employe' => $assing_checkemployees]);
    }


    public function assignLeadsManager(AssignLeadRequest $request)
    {

        $data = array(
            'employee_id' => $request->employee_id,
            'lead_id' => $request->lead_id
        );
        foreach ($request->lead_id as $leadid) {
            Lead::where('id', $leadid)->update(['asign_to_manager' => $request->employee_id]);
        }

        return redirect('leads/assign')->with('success', 'Leads Assigned Successfully.');

    }


}

