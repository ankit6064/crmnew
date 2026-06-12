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
use App\Models\CallbackLeads;
use App\Models\Checkcallback;
use App\Models\Logs;
use Illuminate\Http\Request;
use App\Http\Requests\AssignLeadRequest;
use Illuminate\Support\Carbon;
use Log;
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

        $lead = Lead::create($data);
        // Get and print the last executed query

        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->type = 5;
        $logs->description = 'Lead is added';
        $logs->reference_id = $lead->id;
        $logs->source_id = $request->source_id;
        $logs->save();

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
        $camp_id = $request->camp_id;

        $source = Source::find($camp_id);
        if (!$source) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        // ===================== MANAGER =====================
        $manager = User::find($source->assign_to_manager);
        $manager_name = $manager->name ?? 'N/A';

        // ===================== TOTAL STATUS COUNTS =====================
        $leadStatusCounts = Lead::where('source_id', $camp_id)
            ->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = '" . LEAD_STATUS_PENDING . "' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status = '" . LEAD_STATUS_FAILED . "' THEN 1 ELSE 0 END) AS failed,
            SUM(CASE WHEN status = '" . LEAD_STATUS_CLOSED . "' THEN 1 ELSE 0 END) AS closed,
            SUM(CASE WHEN status = '" . LEAD_STATUS_INPROGRESS . "' THEN 1 ELSE 0 END) AS inprogress,
            SUM(CASE WHEN status = '" . LEAD_STATUS_COMPLETED . "' THEN 1 ELSE 0 END) AS completed
        ")
            ->first();


        $total_leads = $leadStatusCounts->total ?? 0;
        $pending = $leadStatusCounts->pending ?? 0;
        $failed = $leadStatusCounts->failed ?? 0;
        $closed = $leadStatusCounts->closed ?? 0;
        $inprogress = $leadStatusCounts->inprogress ?? 0;
        $completed = $leadStatusCounts->completed ?? 0;

        // ===================== ASSIGNMENT COUNTS =====================
        $assigned_leads = Lead::where('source_id', $camp_id)
            ->whereNotNull('asign_to')
            ->count();

        $unassigned_leads = $total_leads - $assigned_leads;

        // ===================== HEADER TABLE =====================
        $headerTable = '
        <table>
            <thead class="thead-main">
                <tr>
                    <th>Campaign Name</th>
                    <th>Total</th>
                    <th>Fresh</th>
                    <th>In Progress</th>
                    <th>Closed</th>
                    <th>Completed</th>
                    <th>Failed</th>
                    <th>Assigned</th>
                    <th>Unassigned</th>
                 
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>' . $source->source_name . ' (' . $source->description . ')</td>
                    <td>' . $total_leads . '</td>
                    <td>' . $pending . '</td>
                    <td>' . $inprogress . '</td>
                    <td>' . $closed . '</td>
                    <td>' . $completed . '</td>
                    <td>' . $failed . '</td>
                    <td>' . $assigned_leads . '</td>
                    <td>' . $unassigned_leads . '</td>
                
                </tr>
            </tbody>
        </table>';

        // ===================== ASSIGN BLOCK =====================
        $assignBlock = '
        <div class="form-group">
            <label>Enter Assign Leads Count</label>
            <div class="input-box">
                <input type="text" id="assign_count" value="' . $unassigned_leads . '" readonly>
                <i class="fa-solid fa-pen-to-square edit-icon"></i>
            </div>
        </div>

        <div class="form-group">
            <label>Select Employee</label>
            <select id="employee_id" class="form-control">
                <option value="">Select Employee</option>';

        $employees = User::where('is_admin', '!=', 1)->get();
        foreach ($employees as $emp) {
            $assignBlock .= '<option value="' . $emp->id . '">' . $emp->name . '</option>';
        }

        $assignBlock .= '</select>
        <div class="error_msg" style="color:red;margin-top:5px;"></div>
        </div>

        <div class="btn-group">
            <button type="button" id="assignLeadBtn" class="btn btn-save">Assign Leads</button>
        </div>';

        // ===================== EMPLOYEE WISE DATA =====================
        $assigned_rows = DB::table('leads')


            ->selectRaw(" asign_to,
            COUNT(*) as total,
            SUM(CASE WHEN status = '" . LEAD_STATUS_PENDING . "' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status = '" . LEAD_STATUS_FAILED . "' THEN 1 ELSE 0 END) AS failed,
            SUM(CASE WHEN status = '" . LEAD_STATUS_CLOSED . "' THEN 1 ELSE 0 END) AS closed,
            SUM(CASE WHEN status = '" . LEAD_STATUS_INPROGRESS . "' THEN 1 ELSE 0 END) AS inprogress,
            SUM(CASE WHEN status = '" . LEAD_STATUS_COMPLETED . "' THEN 1 ELSE 0 END) AS completed
        ")
            ->where('source_id', $camp_id)
            ->whereNotNull('asign_to')
            ->groupBy('asign_to')
            ->get();

        // ===================== ASSIGNED TABLE =====================
        $assignedTable = '
        <table>
            <thead class="thead-main">
                <tr>
                    <th>Campaign</th>
                    <th>Total</th>
                    <th>Fresh</th>
                    <th>In Progress</th>
                    <th>Closed</th>
                    <th>Completed</th>
                    <th>Failed</th>
                    <th>Employee</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';

        if ($assigned_rows->count()) {

            // optimize users
            $userIds = $assigned_rows->pluck('asign_to')->toArray();
            $users = User::whereIn('id', $userIds)->pluck('name', 'id');

            foreach ($assigned_rows as $row) {

                $emp_name = $users[$row->asign_to] ?? 'Unknown';

                $assignedTable .= '
                <tr>
                    <td>' . $source->source_name . ' (' . $source->description . ')</td>
                    <td>' . $row->total . '</td>
                    <td>' . $row->pending . '</td>
                    <td>' . $row->inprogress . '</td>
                    <td>' . $row->closed . '</td>
                    <td>' . $row->completed . '</td>
                    <td>' . $row->failed . '</td>
                    <td>' . $emp_name . '</td>
                    <td>
                        <button class="btn-action Withdraw" 
                            data-camp="' . $camp_id . '" 
                            data-emp="' . $row->asign_to . '">Withdraw</button>

                        <button class="btn-action Reassign"
                            data-camp="' . $camp_id . '" 
                            data-assign="' . $row->asign_to . '" 
                            data-count="' . $row->total . '">Reassign</button>
                    </td>
                </tr>';
            }

        } else {
            $assignedTable .= '
            <tr>
                <td colspan="9" style="text-align:center;">No Assigned Leads Found</td>
            </tr>';
        }

        $assignedTable .= '</tbody></table>';

        return response()->json([
            'headerTable' => $headerTable,
            'assignBlock' => $assignBlock,
            'assignedTable' => $assignedTable
        ]);
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
            ## Read values
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");

            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $search_arr = $request->get('search');

            $columnIndex = $columnIndex_arr[0]['column'];
            $columnSortOrder = $columnIndex_arr[0]['dir'];
            $searchValue = $search_arr['value'];

            // Map column index to DB columns for ordering
            $columnsMap = [
                '2' => 'company_name',
                '3' => 'prospect_first_name',
                '4' => 'designation',
                '5' => 'created_at'
            ];
            $orderByColumn = $columnsMap[$columnIndex] ?? 'created_at';

            // 1. Initialize Base Query
            $baseQuery = Lead::with('source');
            // 2. Apply Role-based filters
            if (Auth::user()->is_admin == 1) {
                $baseQuery->where('user_id', auth()->user()->id);
            } else {
                $baseQuery->where('asign_to_manager', auth()->user()->id);
            }

            $baseQuery->where('approval_status', '2');

            // 3. Apply the Search Filter (for campaign, employee name, prospect, and company)
            if (!empty($searchValue)) {
                $baseQuery->where(function ($query) use ($searchValue) {
                    $query->where('company_name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('prospect_first_name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('prospect_last_name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('designation', 'LIKE', "%{$searchValue}%")
                        // Search in Source Relationship
                        ->orWhereHas('source', function ($q) use ($searchValue) {
                            $q->where('source_name', 'LIKE', "%{$searchValue}%");
                        })
                        // Search by Employee Name (using subquery since relationship isn't used for fetching)
                        ->orWhereIn('user_id', function ($sub) use ($searchValue) {
                            $sub->select('id')->from('users')->where('name', 'LIKE', "%{$searchValue}%");
                        });
                });
            }

            // Calculate counts for DataTables
            $totalRecords = (Auth::user()->is_admin == 1)
                ? Lead::where('user_id', auth()->user()->id)->where('approval_status', '2')->count()
                : Lead::where('asign_to_manager', auth()->user()->id)->where('approval_status', '2')->count();

            $recordsFiltered = $baseQuery->count();

            // 4. Handle Sorting & Pagination
            if ($columnIndex == "6") {
                $leadsDataRaw = $baseQuery->get();
                $sorted = $leadsDataRaw->sortBy(function ($lead) {
                    return $lead->source->source_name ?? '';
                }, SORT_REGULAR, ($columnSortOrder === 'desc'));
                $leadsData = $sorted->slice($start, $rowperpage)->toArray();
            } else {
                $leadsData = $baseQuery->orderBy($orderByColumn, $columnSortOrder)
                    ->skip($start)
                    ->take($rowperpage)
                    ->get()
                    ->toArray();
            }

            $sources = Source::orderBy('source_name')->get()->toArray();
            $formattedData = [];

            foreach ($leadsData as $lead) {
                // Build Source Options
                $optionsHtml = '';
                foreach ($sources as $source) {
                    $selected = ($source["id"] == $lead["source_id"]) ? ' selected' : '';
                    $optionsHtml .= '<option value="' . $source["id"] . '"' . $selected . '>'
                        . $source["source_name"] . ' ' . $source["description"] . '</option>';
                }

                // Action HTML
                $campaignsHtml = '
                <span id="icons_' . $lead["id"] . '" class="group_actions" style="display:flex; gap:10px; align-items:center; cursor:pointer;">
                    <i class="fa-solid fa-xmark onchange_element_cross" data-id="' . $lead["id"] . '" data-emp-id="' . $lead["user_id"] . '" style="font-size:18px; margin:0 5px; padding:5px; border-radius:5px; color:#fff; background:red; cursor:pointer;"></i>
                    <i class="fa-solid fa-check onchange_element_approve" data-id="' . $lead["id"] . '" data-emp-id="' . $lead["user_id"] . '" style="font-size:18px; margin:0 5px; padding:5px; border-radius:5px; color:#fff; background:#5fbc01; cursor:pointer;"></i>
                </span>';
                $campaignsHtml .= '<select class="unapproved_lead" name="source_id" id="' . $lead["id"] . '" style="width:140px" data-id="' . $lead["source_id"] . '">';
                $campaignsHtml .= '<option value="">Select a source</option>' . $optionsHtml . '</select>';

                // Employee Name (Using User::find exactly as before)
                $userDetails = User::find($lead["user_id"]);
                $employeeName = (isset($userDetails) && !empty($userDetails)) ? $userDetails['name'] : 'N/A';

                // LinkedIn Logic
                $var = $lead["linkedin_address"];
                if (strpos($var, 'linkedin') === false) {
                    $linkdin = '<td><a href="javascript:void(0)"><i style="color: #000" class="fa-brands fa-linkedin" title="LinkedIn Address Not Valid"></i></a></td>';
                } else {
                    $cleanUrl = $var;
                    if (!preg_match('/^https?:\/\//i', $cleanUrl)) {
                        $cleanUrl = 'https://' . ltrim($cleanUrl, '/');
                    }
                    $linkdin = '<td><div style="display:flex; align-items:center; gap:8px;"><a href="' . $cleanUrl . '" target="_blank"><i class="fa-brands fa-linkedin"></i></a><i class="fa-solid fa-pen-to-square" onclick="editmodule(' . $lead["id"] . ', \'' . $cleanUrl . '\')" style="cursor:pointer;"></i></div></td>';
                }

                $formattedData[] = [
                    'action' => $campaignsHtml,
                    'employee_name' => trim($employeeName),
                    'company_name' => $lead["company_name"],
                    'prospect_full_name' => $lead["prospect_first_name"] . ' ' . $lead["prospect_last_name"],
                    'designation' => $lead["designation"],
                    'created_at' => date('d M, Y', strtotime($lead["created_at"])),
                    'source_name' => $lead['source']["source_name"] . ' ' . $lead['source']["description"],
                    'LinkedIn' => $linkdin,
                    'Lead_id' => $lead['id']
                ];
            }

            return response()->json([
                'data' => $formattedData,
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $recordsFiltered,
            ]);
        }
    }
    public function updatelinkedin(Request $request)
    {
        $check = Lead::where('id', $request->leadid)->first();
        if (!empty($check)) {
            $check->linkedin_address = $request->linkedinurl;
            $check->save();
            echo json_encode(['status' => 200, 'message' => 'Linkedin Updated']);
            exit;
        } else {
            echo json_encode(['status' => 400, 'message' => 'Something went wrong']);
            exit;

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
                        $leadDetails = Lead::where('id', $leadId)->first();
                        if ($status == 'approved') {

                            Lead::where('id', $leadId)->update($data1);

                            $logs = new Logs();
                            $logs->user_id = Auth::id();
                            $logs->description = $leadDetails->company_name . ' lead is approved';
                            $logs->type = 15;
                            $logs->source_id = $sourceId;
                            $logs->reference_id = $leadId;
                            $logs->save();
                            return response()->json(['success' => true, 'message' => 'Lead Has Been Approved Successfully!']);
                        } else {
                            Lead::where('id', $leadId)->update($data1);
                            Lead::where('id', $leadId)->delete();
                            $logs = new Logs();
                            $logs->user_id = Auth::id();
                            $logs->description = $leadDetails->company_name . ' lead is disapproved';
                            $logs->type = 15;
                            $logs->source_id = $sourceId;
                            $logs->reference_id = $leadId;
                            $logs->save();
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
            // Base Query
            $data = Lead::join('sources', 'sources.id', '=', 'leads.source_id')
                ->where('leads.source_id', $id)
                ->with(['source', 'feedback'])
                ->select([
                    'leads.id',
                    'leads.source_id',
                    'leads.company_name',
                    'leads.prospect_first_name',
                    'leads.prospect_last_name',
                    'leads.linkedin_address',
                    'leads.timezone',
                    'leads.designation',
                    'leads.contact_number_1',
                    'leads.created_at',
                    'leads.status',
                ]);

            return DataTables::of($data)
                // --- Custom Search Filter ---
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->search['value'])) {
                        $searchValue = strtolower($request->search['value']);

                        $query->where(function ($q) use ($searchValue) {
                            $q->whereRaw('LOWER(leads.company_name) LIKE ?', ["%{$searchValue}%"])
                                ->orWhereRaw('LOWER(leads.designation) LIKE ?', ["%{$searchValue}%"])
                                ->orWhereRaw('LOWER(leads.prospect_first_name) LIKE ?', ["%{$searchValue}%"])
                                ->orWhereRaw('LOWER(leads.prospect_last_name) LIKE ?', ["%{$searchValue}%"])
                                // Optional: Search full name combined
                                ->orWhereRaw("LOWER(CONCAT(leads.prospect_first_name, ' ', leads.prospect_last_name)) LIKE ?", ["%{$searchValue}%"]);
                        });
                    }
                })
                // ----------------------------
                ->addColumn('campaign_name', function ($row) {
                    return $row->source ? $row->source->source_name : '';
                })
                ->addColumn('prospect_name', function ($row) {
                    $name = $row->prospect_first_name . ' ' . $row->prospect_last_name;
                    $linkedin = $row->linkedin_address;
                    $linkedinLink = $linkedin && (str_starts_with($linkedin, 'http://') || str_starts_with($linkedin, 'https://'))
                        ? $linkedin
                        : 'https://' . $linkedin;

                    $icon = (str_contains($linkedin, 'linkedin'))
                        ? "<a href='{$linkedinLink}' target='_blank' style='color:#0077b5'><i class='fa-brands fa-linkedin'></i></a>"
                        : "<i class='fa-brands fa-linkedin' style='color:#ccc' title='LinkedIn Address Not Valid'></i>";

                    return "<a href='/leads/{$row->id}' target='_blank' style='color:black; font-weight:500;'>{$name}</a> " . $icon;
                })
                ->editColumn('contact_number_1', function ($row) {
                    if (empty($row->contact_number_1)) {
                        return 'N/A';
                    }

                    // Split by comma, semicolon, or space
                    $numbers = preg_split('/[,\s;]+/', $row->contact_number_1);
                    $numbers = array_filter($numbers); // Remove empty strings
                    $count = count($numbers);

                    if ($count <= 1) {
                        return $row->contact_number_1;
                    }

                    $firstNumber = $numbers[0];
                    $contact = json_encode($row->contact_number_1);
                    // Pass the rest of the numbers as a JSON array to the JS function
    
                    return "{$firstNumber} 
            <span class='badge' 
                  style='cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px;' 
                  onclick='showAllNumbers({$contact})'>
                  + show more
            </span>";
                })
                // Ensure 'contact_number_1' is in rawColumns
                ->rawColumns(['prospect_name', 'status', 'action', 'contact_number_1'])
                ->addColumn('status', function ($row) {
                    $statuses = [
                        0 => '<p class="status-label pending">Pending</p>',
                        1 => '<p class="status-label pending">Pending</p>',
                        2 => '<p class="status-label failed">Failed</p>',
                        3 => '<p class="status-label completed">Completed</p>',
                        4 => '<p class="status-label in-progress">In Progress</p>',
                    ];
                    return $statuses[$row->status] ?? '<p class="status-label pending">Pending</p>';
                })
                ->addColumn('action', function ($row) {
                    return "<a href='/leads/{$row->id}' target='_blank'><i class='fa fa-eye' style='color:black; cursor:pointer;'></i></a>";
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at
                        ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i')
                        : '';
                })
                ->rawColumns(['prospect_name', 'status', 'action', 'contact_number_1'])
                ->make(true);
        }
    }


    public function allleadview()
    {

        return view('leads.allleadview');
    }

    public function allgetLeadsData(Request $request, $id = null)
    {
        if ($request->ajax()) {
            if (Auth::user()->is_admin == null) {
                $data = Lead::join('sources', 'sources.id', 'leads.source_id')
                    ->with('source', 'feedback')
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
                        'leads.status',
                    ]);
            } else {
                $data = Lead::join('sources', 'sources.id', 'leads.source_id')
                    ->where('asign_to_manager', Auth::id())
                    ->with('source', 'feedback')
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
            }

            return DataTables::of($data)

                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->search['value'])) {
                        $searchValue = strtolower($request->search['value']);

                        $query->where(function ($q) use ($searchValue) {
                            $q->whereRaw('LOWER(leads.company_name) LIKE ?', ["%{$searchValue}%"])
                                ->orWhereRaw('LOWER(leads.designation) LIKE ?', ["%{$searchValue}%"])
                                ->orWhereRaw('LOWER(leads.prospect_first_name) LIKE ?', ["%{$searchValue}%"])
                                ->orWhereRaw('LOWER(leads.prospect_last_name) LIKE ?', ["%{$searchValue}%"])
                                // Optional: Search full name combined
                                ->orWhereRaw("LOWER(CONCAT(leads.prospect_first_name, ' ', leads.prospect_last_name)) LIKE ?", ["%{$searchValue}%"]);
                        });
                    }
                })
                ->order(function ($query) {
                    $query->orderBy('leads.created_at', 'DESC');
                })

                ->addColumn('campaign_name', function ($row) {
                    return $row->source ? $row->source->source_name : '';
                })

                ->addColumn('prospect_name', function ($row) {
                    $name = $row->prospect_first_name . ' ' . $row->prospect_last_name;
                    $linkedin = $row->linkedin_address;

                    $linkedinLink = $linkedin && (str_starts_with($linkedin, 'http://') || str_starts_with($linkedin, 'https://'))
                        ? $linkedin
                        : 'https://' . $linkedin;

                    return "<a href='/leads/{$row->id}' target='_blank'>{$name}</a> " .
                        (strpos($linkedin, 'linkedin') !== false
                            ? "<a href='{$linkedinLink}' target='_blank'><i class='fa-brands fa-linkedin'></i></a>"
                            : "<i class='fa-brands fa-linkedin' title='LinkedIn Address Not Valid'></i>");
                })

                ->editColumn('status', function ($row) {
                    $statuses = [
                        0 => '<p class="pending">Pending</p>',
                        1 => '<p class="pending">Pending</p>',
                        2 => '<p class="failed">Failed</p>',
                        3 => '<p class="completed">Closed</p>',
                        4 => '<p class="in-progress">In Progress</p>',
                        5 => '<p class="in-progress">Completed</p>',
                    ];

                    return $statuses[$row->status] ?? '<p class="pending">Pending</p>';
                })

                ->addColumn('action', function ($row) {
                    $url = url('leads/' . $row->id);
                    return "<a href='{$url}' target='_blank'><i class='fa fa-eye' style='color:black'></i></a>";
                })


                ->editColumn('created_at', function ($row) {
                    return $row->created_at
                        ? Carbon::parse($row->created_at)->format('d-m-Y H:i')
                        : '';
                })
                ->editColumn('contact_number_1', function ($row) {
                    if (empty($row->contact_number_1)) {
                        return 'N/A';
                    }

                    // Split by comma, semicolon, or space
                    $numbers = preg_split('/[,\s;]+/', $row->contact_number_1);
                    $numbers = array_filter($numbers); // Remove empty strings
                    $count = count($numbers);

                    if ($count <= 1) {
                        return $row->contact_number_1;
                    }

                    $firstNumber = $numbers[0];
                    $contact = json_encode($row->contact_number_1);
                    // Pass the rest of the numbers as a JSON array to the JS function
    
                    return "{$firstNumber} 
            <span class='badge' 
                  style='cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px;' 
                  onclick='showAllNumbers({$contact})'>
                  + show more
            </span>";
                })

                ->rawColumns(['prospect_name', 'status', 'action', 'contact_number_1'])

                ->make(true);
        }
    }



    public function closed(Request $request)
    {
        if ($request->ajax()) {
            $query = Lead::select([
                'leads.*',
                'sources.source_name as source_name',
                'sources.description as description'
            ])
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->with([
                    'momReport',
                    'lhsReport',
                    'notes' => function ($query) {
                        $query->latest()->limit(1);
                    }
                ])
                ->where('leads.asign_to', auth()->user()->id)
                ->where('leads.status', '3')
                ->where('sources.is_active', 1);


            if (
                !$request->has('order') ||
                ($request->input('order.0.column') == '0' && $request->input('order.0.dir') === 'asc')
            ) {
                $query->orderBy('closed_on', 'DESC')
                    ->orderBy('leads.updated_at', 'DESC');
            }



            return DataTables::of($query)
                // Fix sorting & searching on joined columns




                ->editColumn('updated_at', function ($data) {
                    if (!empty($data->closed_on)) {
                        return \Carbon\Carbon::parse($data->closed_on)->format('d M, Y H:i:s');
                    } else {
                        return $data->updated_at->format('d M, Y H:i:s');
                    }
                })

                ->addColumn('last_updated_note', function ($data) {
                    $latestNote = $data->notes->first();
                    return $latestNote && strlen($latestNote->feedback) > 20
                        ? substr($latestNote->feedback, 0, 20) . '...'
                        : $latestNote->feedback ?? '';
                })

                ->addColumn('options', function ($data) {
                    $actionHtml = '';

                    $lhsReport = $data->lhsReport;
                    $actionHtml = '';

                    if ($lhsReport) {

                        // View LHS Report
                        $actionHtml .= '
                        <a class = "viewlhs" href="' . url('/lhs_report/view_lhs', [$data->id]) . '" title="View LHS Report">
                            <i class="fa fa-search" style="margin-right:8px;"></i>
                        </a>';

                        // Edit LHS Report
                        $actionHtml .= '
                        <a class="editlhs" href="' . url('/lhs_report/edit', [$data->id]) . '" title="Edit LHS Report">
                            <i class="fa fa-edit" style="margin-right:8px;"></i>
                        </a>';

                        $momReport = $data->momReport;

                        if (empty($momReport['mom_file_path'])) {

                            // Create MOM
                            $actionHtml .= '
                            <a class="createmom" href="' . route('employee.show_mom', [$data->id]) . '" title="Create MOM Report">
                                <i class="fa fa-file" style="margin-right:8px;"></i>
                            </a>';
                        }

                    } else {

                        $checknote = Note::where('lead_id', $data->id)->orderByDesc('id')->first();

                        if (isset($checknote) && !empty($checknote) && $checknote->reminder_for == 'Meeting Set-up') {

                            // Add LHS
                            $actionHtml .= '
                            <a class="addlhs" href="' . url('/employee/lhs_report', [$data->id]) . '" title="Add LHS Report">
                                <i class="fa fa-file-medical" style="margin-right:8px;"></i>
                            </a>';
                        }
                    }

                    // KEEP VIEW NOTES ICON SAME
    
                    $notedetails = Note::where('lead_id', $data->id)->orderByDesc('id')->first();


                    if ($notedetails) {

                        $createdAt = \Carbon\Carbon::parse($notedetails->created_at);

                        // check if note created within last 24 hours
                        if ($createdAt->diffInHours(now()) <= 24) {

                            $actionHtml .= '<a class="viewnotes shake-note" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye label-new" style="color:#8B8000 !important"></i>
                            </a>';

                        } else {

                            $actionHtml .= '<a class="viewnotes" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye" style="color:black"></i>
                            </a>';

                        }
                    }

                    // KEEP ADD NOTE ICON SAME
                    $actionHtml .= '
                    <a class="addnotes" onclick="showaddmodal(' . $data->id . ')" data-toggle="modal">
                        <i class="fa fa-comment label-new" aria-hidden="true"></i>
                    </a>';

                    return $actionHtml;
                })

                ->editColumn('contact_number_1', function ($row) {
                    if (empty($row->contact_number_1)) {
                        return 'N/A';
                    }

                    // Split by comma, semicolon, or space
                    $numbers = preg_split('/[,\s;]+/', $row->contact_number_1);
                    $numbers = array_filter($numbers); // Remove empty strings
                    $count = count($numbers);

                    if ($count <= 1) {
                        return $row->contact_number_1;
                    }

                    $firstNumber = $numbers[0];
                    $contact = json_encode($row->contact_number_1);
                    // Pass the rest of the numbers as a JSON array to the JS function
    
                    return "{$firstNumber} 
            <span class='badge' 
                  style='cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px;' 
                  onclick='showAllNumbers({$contact})'>
                  + show more
            </span>";
                })
                ->addColumn('pending_for_approvalnew', function ($data) {


                    if ($data->pending_for_approval == 0) {

                        $meeting1 = $data->lhsReport->meeting_date1 ?? null;
                        $meeting2 = $data->lhsReport->meeting_date2 ?? null;

                        $isOld = false;

                        if ($meeting2) {
                            // If meeting_date2 exists, check it
                            if (\Carbon\Carbon::parse($meeting2)->lt(now()->subDays(30))) {
                                $isOld = true;
                            }
                        } elseif ($meeting1) {
                            // Else, check meeting_date1
                            if (\Carbon\Carbon::parse($meeting1)->lt(now()->subDays(30))) {
                                $isOld = true;
                            }
                        }

                        if ($isOld) {
                            return '<span class="label label-danger">Dropped</span>';
                        } else {
                            $actionHtml = '<span class="label label-info" onclick="showstatusmodal(' . $data->id . ')" data-toggle="modal" data-target="#status-modal">Pending for Confirmation</span>';
                            return $actionHtml;
                        }
                    } elseif ($data->pending_for_approval == 1) {
                        return '<span class="label label-success">Completed</span>';
                    } else {
                        return '<span class="label label-danger">Dropped</span>';
                    }
                })

                ->editColumn('decline_note', function ($data) {
                    if ($data->pending_for_approval == 0) {

                        $meeting1 = $data->lhsReport->meeting_date1 ?? null;
                        $meeting2 = $data->lhsReport->meeting_date2 ?? null;

                        $isOld = false;

                        if ($meeting2) {
                            // If meeting_date2 exists, check it
                            if (\Carbon\Carbon::parse($meeting2)->lt(now()->subDays(30))) {
                                $isOld = true;
                            }
                        } elseif ($meeting1) {
                            // Else, check meeting_date1
                            if (\Carbon\Carbon::parse($meeting1)->lt(now()->subDays(30))) {
                                $isOld = true;
                            }
                        }

                        if ($isOld) {
                            return "Dropped (meeting date exceeded 30 days)";
                        } else {
                            return 'N/A';
                        }
                    } else {

                        if (empty($data->decline_note)) {
                            return "N/A";
                        } else {
                            return $data->decline_note;
                        }
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

                ->addColumn('invitation_date', function ($row) {

                    // Case 2: Reminder sent but date already exists → disabled with date
                    if (!empty($row->invitation_date)) {
                        return '<span class="label label-success">' . \Carbon\Carbon::parse($row->invitation_date)->format('d M, Y h:i A') . '</span>';
                    } else {
                        return 'N/A';
                    }


                })

                ->rawColumns(['action', 'last_updated_note', 'options', 'pending_for_approvalnew', 'decline_note', 'contact_number_1', 'reminder_status', 'invitation_date'])
                ->make(true);
        }

        return view('leads.closed');
    }

    public function update_invitation_date(Request $request)
    {
        $leadDetails = Lead::where('id', $request->id)->first();
        $leadDetails->confirmation_status = 1;
        $leadDetails->invitation_date = $request->invitation_date . ' ' . $request->invitation_time;
        $leadDetails->save();


        // Create log (single query)
        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->type = 19;
        $logs->reference_id = $request->id;
        $logs->source_id = $leadDetails->source_id;
        $logs->description = 'Lead Confirmed and Invitation Date added for lead -' . $leadDetails->prospect_first_name . ' ' . $leadDetails->prospect_last_name;
        $logs->save();

        return response()->json(['success' => 'Invitation Date added Successfully']);
    }

    public function send_lhs(Request $request)
    {
        $leadId = $request->id;
        $leadDetails = Lead::where('id', $leadId)->first();
        $leadDetails->lhs_sent_at = now();
        $leadDetails->save();

        // Create log
        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->type = 17;
        $logs->reference_id = $leadId;
        $logs->source_id = $leadDetails->source_id;
        $logs->description = 'LHS sent for lead -' . $leadDetails->prospect_first_name . ' ' . $leadDetails->prospect_last_name;
        $logs->save();

        return response()->json(['success' => 'LHS Sent Successfully']);
    }

    public function update_lhs_reminder_status(Request $request)
    {
        $leadId = $request->id;
        $leadDetails = Lead::where('id', $leadId)->first();
        $leadDetails->lhs_reminder_sent_at = now();
        $leadDetails->save();

        // Create log
        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->type = 18;
        $logs->reference_id = $leadId;
        $logs->source_id = $leadDetails->source_id;
        $logs->description = 'LHS Reminder sent for lead -' . $leadDetails->prospect_first_name . ' ' . $leadDetails->prospect_last_name;
        $logs->save();

        return response()->json(['success' => 'LHS Reminder Sent Successfully']);
    }


    public function approval_status(Request $request)
    {
        Lead::where('id', $request->lead_id)->update(['pending_for_approval' => $request->status, 'meeting_date' => $request->meeting_datetime, 'decline_note' => $request->decline_note]);
        echo json_encode(['status' => 200, 'message' => 'Updated']);
        exit;
    }

    public function completed(Request $request)
    {
        if ($request->ajax()) {
            $query = Lead::select([
                'leads.*',
                'sources.source_name as source_name',
                'sources.description as description'
            ])
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->with([
                    'momReport',
                    'lhsReport',
                    'notes' => function ($query) {
                        $query->latest()->limit(1);
                    }
                ])
                ->where('leads.asign_to', auth()->user()->id)
                ->where('leads.status', '5')
                ->where('sources.is_active', 1);


            if (
                !$request->has('order') ||
                ($request->input('order.0.column') == '0' && $request->input('order.0.dir') === 'asc')
            ) {
                $query->orderBy('closed_on', 'DESC')
                    ->orderBy('leads.updated_at', 'DESC');
            }




            return DataTables::of($query)
                // Fix sorting & searching on joined columns


                ->editColumn('updated_at', function ($data) {
                    if (!empty($data->closed_on)) {
                        return \Carbon\Carbon::parse($data->closed_on)->format('d M, Y H:i:s');
                    } else {
                        return $data->updated_at->format('d M, Y H:i:s');
                    }
                })

                ->addColumn('last_updated_note', function ($data) {
                    $latestNote = $data->notes->first();
                    return $latestNote && strlen($latestNote->feedback) > 20
                        ? substr($latestNote->feedback, 0, 20) . '...'
                        : $latestNote->feedback ?? '';
                })

                ->addColumn('options', function ($data) {

                    $actionHtml = '';
                    $momReport = $data->momReport;

                    // Create MOM
                    if (empty($momReport['mom_file_path'])) {

                        $actionHtml .= '
                        <a class="showmom" href="' . route('employee.show_mom', [$data->id]) . '" title="Create MOM Report">
                            <i class="fa fa-file-alt" style="margin-right:8px;font-size:15px;"></i>
                        </a>';

                    }
                    // Download MOM
                    elseif (!empty($momReport['mom_file_path'])) {

                        $actionHtml .= '
                        <a  class="downloadmom" href="' . asset('storage/' . $momReport['mom_file_path']) . '" title="Download MOM">
                            <i class="fa fa-download" style="margin-right:8px;color:#55ce63;font-size:15px;"></i>
                        </a>';
                    }

                    // KEEP VIEW NOTES ICON SAME
                    $notedetails = Note::where('lead_id', $data->id)->orderByDesc('id')->first();

                    $actionHtml = '';

                    if ($notedetails) {

                        $createdAt = \Carbon\Carbon::parse($notedetails->created_at);

                        // check if note created within last 24 hours
                        if ($createdAt->diffInHours(now()) <= 24) {

                            $actionHtml .= '<a class="shownotes shake-note" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye label-new" style="color:#8B8000 !important"></i>
                            </a>';

                        } else {

                            $actionHtml .= '<a class="shownotes" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye" style="color:black"></i>
                            </a>';

                        }
                    }

                    // KEEP ADD NOTE ICON SAME
                    $actionHtml .= '
                    <a class="addnotes"  onclick="showaddmodal(' . $data->id . ')" data-toggle="modal">
                        <i class="fa fa-comment label-new" aria-hidden="true"></i>
                    </a>';

                    return $actionHtml;
                })

                ->rawColumns(['action', 'last_updated_note', 'options'])
                ->make(true);
        }

        return view('leads.completed');
    }

    public function add_note(Request $request)
    {
        $leadId = $request->lead_id;
        $userId = auth()->id();

        // Get last note timestamp only (fast)
        $lastCreatedAt = Note::where('user_id', $userId)
            ->latest('created_at')
            ->value('created_at');

        if ($lastCreatedAt && now()->diffInSeconds($lastCreatedAt) < 15) {
            return response()->json([
                'error' => 'Please wait 30 seconds before adding another note.'
            ], 400);
        }

        // Get lead status only (not full model)
        $status = Lead::where('id', $leadId)->value('status');

        // Create note
        $note = Note::create([
            'user_id' => $userId,
            'lead_id' => $leadId,
            'source_id' => $request->source_id,
            'status' => $status,
            'reminder_time' => $request->reminder_time,
            'reminder_date' => $request->reminder_date,
            'reminder_for' => $request->reminder_for,
            'feedback' => $request->feedback,
            'phone_number' => $request->phone_number,
        ]);

        // Create log (single query)
        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->type = 1;
        $logs->reference_id = $request->lead_id;
        $logs->source_id = Lead::where('id', $request->lead_id)->value('source_id');
        $logs->note_id = $note->id;
        $logs->description = 'New note is added on lead -' . $request->reminder_for;
        $logs->save();

        // Callback logic
        if ($request->reminder_for === 'Callback') {
            CallbackLeads::create([
                'note_id' => $note->id,
                'employee_id' => $userId,
                'lead_id' => $leadId,
                'callback_date' => $request->callback_date,
                'callback_time' => $request->callback_time,
            ]);
        }

        if (Auth::user()->is_admin == 1) {
            $leaddetails = Lead::where('id', $leadId)->first();
            if (!empty($leaddetails) && $leaddetails->status == 1) {
                Lead::where('id', $leadId)->update([
                    'note_created_date' => now()->addHour()->addMinutes(3),
                    'status' => 4
                ]);
            }

        } else {
            // Update lead timestamp (no parsing)
            Lead::where('id', $leadId)->update([
                'note_created_date' => now()->addHour()->addMinutes(3),
            ]);
        }

        return response()->json(['success' => 'Note Added Successfully']);
    }


    public function update_reminder_status(Request $request)
    {
        $leadId = $request->lead_id;
        $leadDetails = Lead::where('id', $leadId)->first();
        $leadDetails->reminder_status = 1;
        $leadDetails->reminder_note = $request->reminder_note;
        $leadDetails->save();

        // Create log (single query)
        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->type = 6;
        $logs->reference_id = $leadId;
        $logs->source_id = $leadDetails->source_id;
        $logs->description = 'Reminder sent for lead -' . $leadDetails->prospect_first_name . ' ' . $leadDetails->prospect_last_name;
        $logs->save();

        return response()->json(['success' => 'Reminder Sent Successfully']);
    }

    public function callbackleads(request $request)
    {
        $currentDate = now()->format('Y-m-d'); // 2025-05-14
        if ($request->ajax()) {
            $callbackleads = CallbackLeads::where('employee_id', auth()->user()->id)->whereDate('callback_date', $currentDate)->orderBy('callback_time')->get();
            if (isset($callbackleads) && !empty($callbackleads)) {
                for ($i = 0; $i < count($callbackleads); $i++) {
                    $leadDetails = Lead::join('sources', 'sources.id', 'leads.source_id')->where('leads.id', $callbackleads[$i]->lead_id)->first();
                    if (!empty($leadDetails)) {
                        $callbackleads[$i]->lead_name = $leadDetails->prospect_first_name . ' ' . $leadDetails->prospect_last_name;
                        $callbackleads[$i]->source_name = $leadDetails->source_name;
                        $callbackleads[$i]->description = $leadDetails->description;

                    }
                }
            }
            return DataTables::of($callbackleads)
                ->addIndexColumn()
                ->editColumn('status', function ($callbackleads) {
                    if ($callbackleads->status == 1) {
                        return '<span style="color:green">Complete</span>';
                    } elseif ($callbackleads->status == 2) {
                        return '<span style="color:red">Uncomplete</span>';
                    } else {
                        return '<button class="btn btn-success btn-sm" onclick="changecallbackstatus(' . $callbackleads->id . ',' . $callbackleads->lead_id . ',1)">Complete</button> 
                    <button class="btn btn-danger btn-sm" onclick="changecallbackstatus(' . $callbackleads->id . ',' . $callbackleads->lead_id . ',2)">Uncomplete</button>';

                    }
                })
                ->editColumn('note', function ($callbackleads) {
                    if (empty($callbackleads->note)) {
                        return 'N/A';
                    } else {
                        return $callbackleads->note;
                    }
                })
                ->editColumn('callback_time', function ($callbackleads) {
                    return Carbon::parse($callbackleads->callback_time)->format('g:i A');

                })
                ->rawColumns(['status'])
                ->make(true);
        }
        return view('leads.callbackleadsemployee');

    }

    public function managercallbackleads(request $request)
    {
        $currentDate = now()->format('Y-m-d'); // 2025-05-14
        $currentTime = now()->format('H:i'); // e.g., 14:27 (use 'H:i' for 24-hour format to avoid AM/PM issues)
        if ($request->ajax()) {
            $passedleads = @$request->passed_status;
            if (isset($request->employee_id) && !empty($request->employee_id)) {
                $employeeids = [$request->employee_id];
            } else {
                $employeeids = User::where('user_id', auth()->user()->id)->pluck('id');

            }

            if (isset($request->callback_status)) {
                $callback_status = [$request->callback_status];
            } else {
                $callback_status = ["0", "1", "2"];
            }
            // if ($passedleads == 'passed') {

            //     $callbackleads = CallbackLeads::whereIn('employee_id', $employeeids)
            //         ->whereDate('callback_date', $currentDate)
            //         ->whereTime('callback_time', '<', $currentTime) // 🔍 Only future callbacks
            //         ->where('status', 0)
            //         ->orderBy('callback_time')
            //         ->get();
            // } else {
            //     $callbackleads = CallbackLeads::whereIn('employee_id', $employeeids)
            //         ->whereDate('callback_date', $currentDate)
            //         ->orderBy('callback_time')
            //         ->whereIn('status', $callback_status)
            //         ->get();
            // }
            // if (isset($callbackleads) && !empty($callbackleads)) {
            //     for ($i = 0; $i < count($callbackleads); $i++) {
            //         $leadDetails = Lead::join('sources', 'sources.id', 'leads.source_id')->where('leads.id', $callbackleads[$i]->lead_id)->first();
            //         if (!empty($leadDetails)) {
            //             $callbackleads[$i]->lead_name = $leadDetails->prospect_first_name . ' ' . $leadDetails->prospect_last_name;
            //             $callbackleads[$i]->source_name = $leadDetails->source_name;
            //             $callbackleads[$i]->description = $leadDetails->description;

            //         }
            //         $employee_details = User::where('id', $callbackleads[$i]->employee_id)->first();
            //         if (!empty($employee_details)) {
            //             $callbackleads[$i]->employee_name = $employee_details->first_name . ' ' . $employee_details->last_name;
            //         } else {
            //             $callbackleads[$i]->employee_name = 'N/A';
            //         }
            //     }
            // }
            $callbackleads = CallbackLeads::query()
                ->join('leads', 'leads.id', '=', 'callback_leads.lead_id')
                ->join('sources', 'sources.id', '=', 'leads.source_id')
                ->join('users', 'users.id', '=', 'callback_leads.employee_id')
                ->select(
                    'callback_leads.*',
                    DB::raw("CONCAT(leads.prospect_first_name, ' ', leads.prospect_last_name) as lead_name"),
                    'sources.source_name',
                    'sources.description',
                    DB::raw("CONCAT(users.first_name, ' ', users.last_name) as employee_name")
                )
                ->whereIn('callback_leads.employee_id', $employeeids)
                ->whereDate('callback_leads.callback_date', $currentDate);

            // Apply conditionally based on $passedleads
            if ($passedleads === 'passed') {
                $callbackleads->whereTime('callback_leads.callback_time', '<', $currentTime)
                    ->where('callback_leads.status', 0);
            } else {
                $callbackleads->whereIn('callback_leads.status', $callback_status);
            }
            return DataTables::of($callbackleads)
                ->addIndexColumn()
                ->editColumn('status', function ($callbackleads) {
                    if ($callbackleads->status == 1) {
                        return '<span style="color:green">Complete</span>';
                    } elseif ($callbackleads->status == 2) {
                        return '<span style="color:red">Uncomplete</span>';
                    } else {
                        return '<span style="color:orange">Pending</span>';

                    }
                })
                ->editColumn('note', function ($callbackleads) {
                    if (empty($callbackleads->note)) {
                        return 'N/A';
                    } else {
                        return $callbackleads->note;
                    }
                })
                ->editColumn('callback_time', function ($callbackleads) {
                    return Carbon::parse($callbackleads->callback_time)->format('g:i A');

                })
                ->rawColumns(['status'])
                ->make(true);
        }
        $employee_list = User::select('id', 'first_name', 'last_name')->where('user_id', auth()->user()->id)->orderby('first_name')->get();

        return view('leads.managercallbackleads', compact('employee_list'));

    }

    public function changecallbackstatus(Request $request)
    {
        if ($request->ajax()) {
            CallbackLeads::where('id', $request->callbackid)->update(['id' => $request->callbackid, 'status' => $request->status, 'note' => $request->note]);
            echo json_encode(['status' => 200, 'message' => 'Status Upated']);
            exit;
        }
    }


    public function checkpendingcallback(Request $request)
    {
        $currentDate = now()->format('Y-m-d'); // e.g., 2025-05-17
        $currentTime = now()->format('H:i'); // e.g., 14:27 (use 'H:i' for 24-hour format to avoid AM/PM issues)
        $check = CallbackLeads::where('employee_id', $request->id)
            ->whereDate('callback_date', $currentDate)
            ->where('status', 0)
            ->count();

        if ($check > 0) {
            $checklastcallback = Checkcallback::where('employee_id', $request->id)
                ->whereDate('today_date', $currentDate)
                ->first();
            if (empty($checklastcallback)) {
                $checkcallbacktable = new Checkcallback();
                $checkcallbacktable->employee_id = $request->id;
                $checkcallbacktable->today_date = $currentDate;
                $checkcallbacktable->last_modal_time = $currentTime;
                $checkcallbacktable->save();

                echo json_encode([
                    'status' => 200,
                    'message' => 'You have pending callback',
                    'data' => $check
                ]);
                exit;
            } else {
                $lastModalDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $checklastcallback->updated_at);
                // dd($lastModalDateTime);
                $currentDateTime = Carbon::now();

                $diffInMinutes = $lastModalDateTime->diffInMinutes($currentDateTime);
                if ($diffInMinutes >= 60) {
                    Checkcallback::where('employee_id', $request->id)->update(['today_date' => $currentDate, 'last_modal_time' => $currentTime]);
                    echo json_encode([
                        'status' => 200,
                        'message' => 'You have pending callback',
                        'data' => $check
                    ]);
                    exit;
                }
            }
        } else {
            echo json_encode([
                'status' => 400,
                'message' => 'No pending leads',
                'data' => $check
            ]);
            exit;
        }
    }

    public function checkpendingcallbackmanager(Request $request)
    {
        $currentDate = now()->format('Y-m-d'); // e.g., 2025-05-17
        $currentTime = now()->format('H:i'); // e.g., 14:27 (use 'H:i' for 24-hour format to avoid AM/PM issues)
        $employeeids = User::where('user_id', auth()->user()->id)->pluck('id');

        $check = CallbackLeads::whereIn('employee_id', $employeeids)
            ->whereDate('callback_date', $currentDate)
            ->whereTime('callback_time', '<', $currentTime) // 🔍 Only future callbacks
            ->where('status', 0)
            ->count();
        // dd($check);

        if ($check > 0) {
            $checklastcallback = Checkcallback::where('employee_id', $request->id)
                ->whereDate('today_date', $currentDate)
                ->first();
            if (empty($checklastcallback)) {
                // $checkcallbacktable = new Checkcallback();
                // $checkcallbacktable->employee_id = $request->id;
                // $checkcallbacktable->today_date = $currentDate;
                // $checkcallbacktable->last_modal_time = $currentTime;
                // $checkcallbacktable->save();

                echo json_encode([
                    'status' => 200,
                    'message' => 'You have pending callback',
                    'data' => $check
                ]);
                exit;
            } else {
                $lastModalDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $checklastcallback->updated_at);
                // dd($lastModalDateTime);
                $currentDateTime = Carbon::now();

                $diffInMinutes = $lastModalDateTime->diffInMinutes($currentDateTime);
                if ($diffInMinutes >= 60) {
                    // Checkcallback::where('employee_id',$request->id)->update(['today_date'=>$currentDate,'last_modal_time'=>$currentTime]);
                    echo json_encode([
                        'status' => 200,
                        'message' => 'You have pending callback',
                        'data' => $check
                    ]);
                    exit;
                }
            }
        } else {
            echo json_encode([
                'status' => 400,
                'message' => 'No pending leads',
                'data' => $check
            ]);
            exit;
        }
    }


    public function notes_view(Request $request)
    {
        $notes_data = Lead::where('id', $request->id)
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
        <th>Phone Number</th>
        <th>Reminder Date</th>  
    </tr>
        </thead>
        <tbody>';
        if (Auth::user()->is_admin == 1) {
            if (!empty($notes_data)) {
                foreach ($notes_data['notes'] as $key => $table_data) {

                    if (isset($table_data->reminder_date) && !empty($table_data->reminder_date)) {
                        $dateData = date("d-m-Y", strtotime($table_data->reminder_date));
                    } else {
                        $dateData = "N/A";
                    }

                    $date = \Carbon\Carbon::parse($table_data->created_at);
                    // dd($date->diffInHours(\Carbon\Carbon::now()) );

                    // highlight if within 24 hours
                    $isRecent = $date->diffInHours(\Carbon\Carbon::now()) <= 24;
                    $tdStyle = $isRecent ? 'style="background:#fff3cd !important;"' : '';

                    $table .= '<tr>
    <td ' . $tdStyle . ' class="wraping notes_comment">
        <p style="white-space: initial; max-height: 100px; overflow-y: auto;">
            ' . htmlspecialchars($table_data->feedback ?? 'N/A') . '
        </p>
    </td>
    
    <td ' . $tdStyle . ' class="wraping">
        ' . $date->format('Y-m-d H:i') . '
    </td>
    
    <td ' . $tdStyle . ' class="wraping">
        ' . (!empty($table_data->reminder_for) ? $table_data->reminder_for : 'N/A') . '
    </td>
    
    <td ' . $tdStyle . ' class="wraping">
        ' . (!empty($table_data->phone_number) ? $table_data->phone_number : 'N/A') . '
    </td>
    
    <td ' . $tdStyle . ' class="wraping">
        ' . $dateData . '
    </td>
</tr>';
                }
            } else {
                $table .= '<tr>
            <td colspan="4" style="text-align:center; font-weight:bold;">
                No Data Found
            </td>
        </tr>';
            }
        } else {
            if (!empty($notes_data)) {
                foreach ($notes_data['notes'] as $key => $table_data) {
                    if (isset($table_data->reminder_date) && !empty($table_data->reminder_date)) {
                        $dateData = date("d-m-Y", strtotime($table_data->reminder_date));
                    } else {
                        $dateData = "N/A";
                    }
                    $date = \Carbon\Carbon::parse($table_data->created_at);
                    $table .= '<tr> <td class="wraping notes_comment"> <p style="white-space: initial; max-height: 100px; overflow-y: auto;"> ' . htmlspecialchars($table_data->feedback ?? 'N/A') . ' </p> </td> <td class="wraping"> ' . ($date ? $date->format('Y-m-d H:i') : 'N/A') . ' </td> <td class="wraping"> ' . (!empty($table_data->reminder_for) ? $table_data->reminder_for : 'N/A') . ' </td> <td class="wraping"> ' . (!empty($table_data->phone_number) ? $table_data->phone_number : 'N/A') . ' </td> <td class="wraping"> ' . ($dateData ?? 'N/A') . ' </td> </tr>';
                }
            } else {
                $table .= '<tr> <td colspan="4" style="text-align:center; font-weight:bold;"> No Data Found </td> </tr>';
            }
        }
        return response()->json(['notes_data' => $notes_data, 'table' => $table]);
        // return view('notes.view')->with(['data'=>$data]);
    }


    public function failed(Request $request)
    {
        if ($request->ajax()) {
            // Eager load the relationships we need (source and notes)
            $query = Lead::select([
                'leads.*',
                'sources.source_name as source_name',
                'sources.description as description'
            ])
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->with([
                    'momReport',
                    'lhsReport',
                    'notes' => function ($query) {
                        $query->latest()->limit(1);
                    }
                ])
                ->where('leads.asign_to', auth()->user()->id)
                ->where('leads.status', '2')
                ->where('sources.is_active', 1);


            if (
                !$request->has('order') ||
                ($request->input('order.0.column') == '0' && $request->input('order.0.dir') === 'asc')
            ) {
                $query->orderBy('closed_on', 'DESC')
                    ->orderBy('leads.updated_at', 'DESC');
            }

            return DataTables::of($query)

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
    
                    $notedetails = Note::where('lead_id', $data->id)->orderByDesc('id')->first();

                    $actionHtml = '';

                    if ($notedetails) {

                        $createdAt = \Carbon\Carbon::parse($notedetails->created_at);

                        // check if note created within last 24 hours
                        if ($createdAt->diffInHours(now()) <= 24) {

                            $actionHtml .= '<a class="viewnotes shake-note" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye label-new" style="color:#8B8000 !important"></i>
                            </a>';

                        } else {

                            $actionHtml .= '<a class="viewnotes" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye" style="color:black"></i>
                            </a>';

                        }
                    }

                    // KEEP ADD NOTE ICON SAME
                    $actionHtml .= '
                    <a class="addnotes"  onclick="showaddmodal(' . $data->id . ')" data-toggle="modal">
                        <i class="fa fa-comment label-new" aria-hidden="true"></i>
                    </a>';

                    $actionHtml .= '
                    <a onclick="showstatusmodal(' . $data->id . ')" data-toggle="modal" data-target="#status-modal" title="Change Status">
                    <i class="fa fa-exchange label-new" aria-hidden="true"></i>
                </a>';
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
            $query = Lead::select([
                'leads.*',
                'sources.source_name as source_name',
                'sources.description as description'
            ])
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->with([
                    'momReport',
                    'lhsReport',
                    'notes' => function ($query) {
                        $query->latest()->limit(1);
                    }
                ])
                ->where('leads.asign_to', auth()->user()->id)
                ->where('leads.status', '4')
                ->where('sources.is_active', 1);


            if (
                !$request->has('order') ||
                ($request->input('order.0.column') == '0' && $request->input('order.0.dir') === 'asc')
            ) {
                $query->orderBy('closed_on', 'DESC')
                    ->orderBy('leads.updated_at', 'DESC');
            }

            return DataTables::of($query)

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
    

                    $notedetails = Note::where('lead_id', $data->id)->orderByDesc('id')->first();

                    $actionHtml = '';

                    if ($notedetails) {

                        $createdAt = \Carbon\Carbon::parse($notedetails->created_at);

                        // check if note created within last 24 hours
                        if ($createdAt->diffInHours(now()) <= 24) {

                            $actionHtml .= '<a class="viewnotes shake-note" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye label-new" style="color:#8B8000 !important"></i>
                            </a>';

                        } else {

                            $actionHtml .= '<a class="viewnotes" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye" style="color:black"></i>
                            </a>';

                        }
                    }


                    // KEEP ADD NOTE ICON SAME
                    $actionHtml .= '
                    <a class="addnotes"  onclick="showaddmodal(' . $data->id . ')" data-toggle="modal">
                        <i class="fa fa-comment label-new" aria-hidden="true"></i>
                    </a>';

                    $actionHtml .= '
                    <a onclick="showstatusmodal(' . $data->id . ')" data-toggle="modal" data-target="#status-modal" title="Change Status">
                    <i class="fa fa-exchange label-new" aria-hidden="true"></i>
                </a>';
                    return $actionHtml;
                })
                ->rawColumns(['action', 'last_updated_note', 'options'])
                ->make(true);
        }

        return view('leads.in_progress');
    }


    public function freshleads(Request $request)
    {
        if ($request->ajax()) {
            // Eager load the relationships we need (source and notes)
            $query = Lead::select([
                'leads.*',
                'sources.source_name as source_name',
                'sources.description as description'
            ])
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->with([
                    'momReport',
                    'lhsReport',
                    'notes' => function ($query) {
                        $query->latest()->limit(1);
                    }
                ])
                ->where('leads.asign_to', auth()->user()->id)
                ->where('leads.status', '1')
                ->where('sources.is_active', 1);


            if (
                !$request->has('order') ||
                ($request->input('order.0.column') == '0' && $request->input('order.0.dir') === 'asc')
            ) {
                $query->orderBy('closed_on', 'DESC')
                    ->orderBy('leads.updated_at', 'DESC');
            }

            return DataTables::of($query)

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
    

                    $notedetails = Note::where('lead_id', $data->id)->orderByDesc('id')->first();

                    $actionHtml = '';

                    if ($notedetails) {

                        $createdAt = \Carbon\Carbon::parse($notedetails->created_at);

                        // check if note created within last 24 hours
                        if ($createdAt->diffInHours(now()) <= 24) {

                            $actionHtml .= '<a class="viewnotes shake-note" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye label-new" style="color:#8B8000 !important"></i>
                            </a>';

                        } else {

                            $actionHtml .= '<a class="viewnotes" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye" style="color:black"></i>
                            </a>';

                        }
                    }


                    // KEEP ADD NOTE ICON SAME
                    $actionHtml .= '
                    <a class="addnotes"  onclick="showaddmodal(' . $data->id . ')" data-toggle="modal">
                        <i class="fa fa-comment label-new" aria-hidden="true"></i>
                    </a>';

                    $actionHtml .= '
                    <a onclick="showstatusmodal(' . $data->id . ')" data-toggle="modal" data-target="#status-modal" title="Change Status">
                    <i class="fa fa-exchange label-new" aria-hidden="true"></i>
                </a>';
                    return $actionHtml;
                })
                ->rawColumns(['action', 'last_updated_note', 'options'])
                ->make(true);
        }

        return view('leads.freshleads');
    }


    public function totalLeads(Request $request)
    {
        if ($request->ajax()) {
            // Eager load the relationships we need (source and notes)
            $query = Lead::select([
                'leads.*',
                'sources.source_name as source_name',
                'sources.description as description'
            ])
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->with([
                    'momReport',
                    'lhsReport',
                    'notes' => function ($query) {
                        $query->latest()->limit(1);
                    }
                ])
                ->where('leads.asign_to', auth()->user()->id)
                // ->where('leads.status', '1')
                ->where('sources.is_active', 1);


            if (
                !$request->has('order') ||
                ($request->input('order.0.column') == '0' && $request->input('order.0.dir') === 'asc')
            ) {
                $query->orderBy('closed_on', 'DESC')
                    ->orderBy('leads.updated_at', 'DESC');
            }

            return DataTables::of($query)

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
    

                    $notedetails = Note::where('lead_id', $data->id)->orderByDesc('id')->first();

                    $actionHtml = '';

                    if ($notedetails) {

                        $createdAt = \Carbon\Carbon::parse($notedetails->created_at);

                        // check if note created within last 24 hours
                        if ($createdAt->diffInHours(now()) <= 24) {

                            $actionHtml .= '<a class="viewnotes shake-note" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye label-new" style="color:#8B8000 !important"></i>
                            </a>';

                        } else {

                            $actionHtml .= '<a class="viewnotes" onclick="shownoteslist(' . $data->id . ')" data-toggle="modal" data-target="#largeModal">
                                <i class="fas fa-eye" style="color:black"></i>
                            </a>';

                        }
                    }


                    // KEEP ADD NOTE ICON SAME
                    $actionHtml .= '
                    <a class="addnotes"  onclick="showaddmodal(' . $data->id . ')" data-toggle="modal">
                        <i class="fa fa-comment label-new" aria-hidden="true"></i>
                    </a>';

                    $actionHtml .= '
                    <a onclick="showstatusmodal(' . $data->id . ')" data-toggle="modal" data-target="#status-modal" title="Change Status">
                    <i class="fa fa-exchange label-new" aria-hidden="true"></i>
                </a>';
                    return $actionHtml;
                })
                ->rawColumns(['action', 'last_updated_note', 'options'])
                ->make(true);
        }

        return view('leads.totalLeads');
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
                $hostnameNew = "http://127.0.0.1:8000";//Config::get('app.url');
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
            if ($request->status == 3) {
                Lead::where('id', $request->lead_id)->update(['status' => $request->status, 'is_notify' => 1, 'is_read' => 1, 'closed_on' => Carbon::now()]);

            } else {
                Lead::where('id', $request->lead_id)->update(['status' => $request->status, 'is_notify' => 1, 'is_read' => 1]);

            }
            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->type = 2;
            $logs->reference_id = $request->lead_id;
            $logs->source_id = Lead::where('id', $request->lead_id)->value('source_id');
            $logs->save();
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

        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->type = 5;
        $logs->description = 'Lead is edited';
        $logs->reference_id = $data->id;
        $logs->source_id = $data->source_id;
        $logs->save();


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

