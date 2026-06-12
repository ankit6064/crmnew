<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Source;
use App\Models\SubmanagerPermissions;
use App\Models\conversationType;
use App\Models\Lead;
use App\Models\Logs;
use App\Models\LhsFiles;
use App\Models\Note;
use App\Models\LhsReport;
use App\Models\MomReport;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use Auth;
use HTML_TO_DOC;
use DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Style\Table;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use App\Models\RestrictEmployeelogin;
use DateTimeZone;

class EmployeeController extends Controller
{
    /**
     * Navigate to the source listing view.
     * @return \Illuminate\View\View
     *
     */
    public function index()
    {
        if (Auth::user()->is_admin == 2) {
            return redirect()->route('employee.manageremployeeindex');
        }
        $total = User::where('is_admin', 1)
            ->count();

        $active = User::where('is_admin', 1)
            ->where('is_active', 1)
            ->count();

        $deactive = User::where('is_admin', 1)
            ->where('is_active', 2)
            ->count();

        return view('employee.index', compact('active', 'deactive', 'total'));
    }

    /**
     * Handle the AJAX request for DataTables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployees(Request $request)
    {
        if ($request->ajax()) {
            if ($request->status_filter == 'total') {
                $status = [1, 2];
            } elseif ($request->status_filter == 'active') {
                $status = [1];
            } else {
                $status = [2];
            }
            $managers = User::where('is_admin', USER)
                ->select('id', 'first_name', 'last_name', 'image', 'email', 'orignal_password', 'address', 'phone_no', 'manager_type', 'is_active')
                ->whereIn('is_active', $status)
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($managers)
                ->editColumn('manager_type', function ($data) {
                    $mangerType = $data->manager_type == MANAGER_TYPE_INTERNAL
                        ? 'Internal'
                        : ($data->manager_type == MANAGER_TYPE_EXTERNAL
                            ? 'External'
                            : 'Not assigned');

                    return $mangerType;
                })

                ->addColumn('status', function ($data) {
                    // Determine if the checkbox should be checked
                    $checked = $data->is_active == 1 ? 'checked' : '';
                    $status = '<input data-sid = "' . $data->source_id . '" class="switchery" type="checkbox" ' . $checked . '>';
                    return $status;
                })
                ->addColumn('actions', function ($data) {
                    // Customize the action buttons
    
                    $checked = $data->is_active == 1 ? 'checked' : '';
                    $status = '<input data-sid = "' . $data->source_id . '" class="switchery" type="checkbox" ' . $checked . '>';



                    $editLink = '
            <a href="' . route('employee.edit', ['employee_id' => $data->id]) . '" style="padding:unset !important">
                <i class="fa-solid fa-pen-to-square text-success editManager"></i>
            </a>';

                    // Delete
                    $deleteLink = '
            <a href="javascript:void(0);"  class="delete-manager" data-id="' . $data->id . '" style="padding:unset !important">
                <i class="fa-solid fa-trash text-danger deleteEmployee" data-id="' . $data->id . '"></i>
            </a>';



                    return $status . ' ' . $editLink . '' . $deleteLink;
                })
                ->rawColumns(['actions', 'status'])
                ->toJson();
        }
        return response()->json(['error' => 'Invalid request'], 400);

    }

    /**
     * Show the form to create a new manager.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $request = 'CreateEmployeeRequest';
        $managers = user::where('is_admin', MANAGER)->orderBy('name')->pluck('name', 'id');
        // Return the view with the employee creation form
        return view('employee.create', compact('request', 'managers'));
    }

    // Handle the form submission to save a employee
    public function store(CreateEmployeeRequest $request)
    {
        try {
            // First, validate the request
            $validated = $request->validated();
            $password = Str::random(12);

            // Add a new value to the request data
            $validated = array_merge($validated, [
                'name' => $request->first_name . ' ' . $request->last_name,
                'orignal_password' => $password,
                'password' => Hash::make($password),
                'is_admin' => USER,
            ]);
            // Create the new employee
            $user = User::create($validated);

            // Redirect with success message
            return redirect()->route('employee.index')->with('success', 'Employee created successfully.');
        } catch (\Exception $e) {
            // Catch any other general exception and log it
            \Log::error('Error creating employee: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);
            // Redirect back with a general error message
            return redirect()->back()->with('error', 'An error occurred while creating the employee. Please try again.');
        }
    }

    // Display the form for editing the employee
    public function edit($employeeID)
    {

        try {
            // Try to find the employee by ID
            $employee = User::findOrFail($employeeID);

            $request = 'EmployeeUpdateRequest';

            $managers = user::where('is_admin', MANAGER)->orderBy('name')->pluck('name', 'id');

            // Return the edit view with the employee data
            return view('employee.edit', compact('employee', 'request', 'managers'));
        } catch (ModelNotFoundException $e) {
            // Handle the case where the employee is not found
            return redirect()->route('employee.index') // Redirect to the employee list page or any other route
                ->with('error', 'Employee not found!');
        } catch (\Exception $e) {
            // Handle any other exceptions that may occur
            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function update(EmployeeUpdateRequest $request, $employeeId)
    {
        try {
            // The request is already validated here, so you can proceed with the update logic
            $employee = User::findOrFail($employeeId);
            $validated = $request->validated();
            $password = Str::random(12);

            // Add a new value to the request data
            $validated = array_merge($validated, [
                'name' => $request->first_name . ' ' . $request->last_name,
                'orignal_password' => $password,
                'password' => Hash::make($password),
                'is_admin' => USER,
            ]);
            $request->merge([
                'name' => $request->first_name . ' ' . $request->last_name,
                'password' => Hash::make($request->orignal_password)
            ]);
            // Update the manager with the validated data
            $employee->update($request->all());
            if (Auth::user()->is_admin == 2) {

                // return redirect()->route('employee.manageremployeeindex')->with('success', 'Employee updated successfully!');

                echo json_encode(['status' => 200, 'message' => 'Employee Details Updated']);
                exit;
            }
            // Redirect with a success message
            return redirect()->route('manager.index')->with('success', 'Manager updated successfully!');
        } catch (ModelNotFoundException $e) {
            // Handle the case where the manager is not found
            return redirect()->route('manager.index') // Redirect to the manager list page or any other route
                ->with('error', 'Manager not found!');
        } catch (\Exception $e) {
            // Handle any other exceptions that may occur
            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function destroy($employeeId)
    {
        try {
            // Find the employee by ID
            $employee = User::findOrFail($employeeId); // Will throw ModelNotFoundException if not found

            // Delete the employee
            $employee->delete();

            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->description = $employee->first_name . ' ' . $employee->last_name . ' account is deleted.';
            $logs->type = 11;
            $logs->save();

            // Redirect with a success message
            return redirect()->route('employee.index')->with('success', 'Employee deleted successfully.');

        } catch (ModelNotFoundException $e) {
            // Handle the case when the employee is not found
            return redirect()->route('employee.index')->with('error', 'Employee not found.');

        } catch (\Exception $e) {
            // Handle general exceptions (e.g., database errors)
            return redirect()->route('employee.index')->with('error', 'An error occurred while trying to delete the employee.');
        }
    }

    public function manageremployeeindex()
    {
        $employees = User::where(function ($query) {
            $query->where('is_admin', USER)
                ->orWhere('is_admin', SUBMANAGER);
        })
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $subamangerpermissions = SubmanagerPermissions::where('user_id', Auth::id())->first();
        if (isset($subamangerpermissions) && !empty($subamangerpermissions)) {
            $permissions = json_decode($subamangerpermissions->user_permissions);
        } else {
            $permissions = [];
        }

        $total = User::where(function ($query) {
            $query->where('is_admin', USER)
                ->orWhere('is_admin', SUBMANAGER);
        })
            ->where('user_id', Auth::id())
            ->count();

        $active = User::where(function ($query) {
            $query->where('is_admin', USER)
                ->orWhere('is_admin', SUBMANAGER);
        })
            ->where('user_id', Auth::id())
            ->where('is_active', 1)
            ->count();

        $deactive = User::where(function ($query) {
            $query->where('is_admin', USER)
                ->orWhere('is_admin', SUBMANAGER);
        })
            ->where('user_id', Auth::id())
            ->where('is_active', 2)
            ->count();


        return view('employee.manageremployeeindex', compact('employees', 'permissions', 'active', 'deactive', 'total'));
    }

    public function createmanageremployees()
    {
        $request = 'CreateEmployeeRequest';
        $managers = user::where('is_admin', MANAGER)->orderBy('name')->pluck('name', 'id');
        // Return the view with the employee creation form
        return view('employee.createmanageremployee', compact('request', 'managers'));
    }

    public function storemanageremployee(CreateEmployeeRequest $request)
    {
        try {
            // First, validate the request
            $validated = $request->validated();
            $password = Str::random(12);

            // Add a new value to the request data
            $validated = array_merge($validated, [
                'name' => $request->first_name . ' ' . $request->last_name,
                'orignal_password' => $password,
                'password' => Hash::make($password),
                'is_admin' => USER,
                'user_id' => Auth::id()
            ]);
            // Create the new employee
            User::create($validated);
            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->description = 'A new employee ' . $request->first_name . ' ' . $request->last_name . ' is added.';
            $logs->type = 7;
            $logs->save();
            // Redirect with success message
            echo json_encode(['status' => 200, 'message' => 'Employee Created']);
        } catch (\Exception $e) {
            // Catch any other general exception and log it
            \Log::error('Error creating employee: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);
            // Redirect back with a general error message
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function manageremployeedata(Request $request)
    {
        if ($request->ajax()) {
            if ($request->status_filter == 'total') {
                $status = [1, 2];
            } elseif ($request->status_filter == 'active') {
                $status = [1];
            } else {
                $status = [2];
            }
            $managers = User::where(function ($query) {
                $query->where('is_admin', USER)
                    ->orWhere('is_admin', SUBMANAGER);
            })
                ->where('user_id', Auth::id())
                ->whereIn('is_active', $status)
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($managers)
                ->addColumn('disable_login', function ($data) {
                    // Determine if the checkbox should be checked
                    $check = RestrictEmployeelogin::where(['employee_id' => $data->id])->first();
                    if (isset($check) && !empty($check)) {
                        $checked = '';
                    } else {
                        $checked = 'checked';
                    }
                    $status = '<input data-sid="' . $data->source_id . '" data-id="' . $data->id . '" class="switchery" type="checkbox" onchange="disablelogin(' . $data->id . ', \'' . addslashes($data->email) . '\');" ' . $checked . '>';
                    return $status;
                })
                ->addColumn('status', function ($data) {
                    // Determine if the checkbox should be checked
                    $checked = $data->is_active == 1 ? 'checked' : '';
                    $status = '<input data-sid = "' . $data->source_id . '"  data-id = "' . $data->id . '" class="switchery" type="checkbox" id="togglebtn" ' . $checked . '>';
                    return $status;
                })
                ->addColumn('totalcampaigns', function ($data) {
                    // Count users where user_id matches the current data id
                    $totalcampaigns = Source::where(function ($q) use ($data) {
                        $q->where('user_id', $data->id)
                            ->orWhere('assign_to_manager', $data->id);
                    })
                        ->where('is_active', 1)
                        ->count();


                    return '<p  class="view_emp" title="View Campagins" onclick="viewCampaigns(' . $data->id . ')">' . $totalcampaigns . '</p>';
                })
                ->addColumn('actions', function ($data) {

                    // Edit
                    $editLink = '
                        <a href="' . route('employee.edit', ['employee_id' => $data->id]) . '">
                            <i class="fa-solid fa-pen-to-square text-success editEmployee"></i>
                        </a>';

                    // Delete
                    $deleteLink = '
                        <a href="javascript:void(0);" data-id="' . $data->id . '">
                            <i class="fa-solid fa-trash text-danger deleteEmployee" data-id="' . $data->id . '"></i>
                        </a>';

                    /**
                     * Login permission switch
                     */
                    $check = RestrictEmployeelogin::where('employee_id', $data->id)->first();
                    $loginChecked = $check ? '' : 'checked';

                    $login_permission = '
                        <input
                            type="checkbox"
                            class="switchery"
                            data-tooltip="' . ($loginChecked ? 'Disable Login' : 'Enable Login') . '"
                            data-id="' . $data->id . '"
                            onchange="disablelogin(' . $data->id . ', \'' . addslashes($data->email) . '\');"
                            ' . $loginChecked . '
                        >';

                    /**
                     * Active / Inactive switch
                     */
                    $statusChecked = $data->is_active == 1 ? 'checked' : '';

                    $changestatus = '
                        <input
                            type="checkbox"
                            class="switchery"
                            data-tooltip="' . ($statusChecked ? 'Deactivate Employee' : 'Activate Employee') . '"
                            data-id="' . $data->id . '"
                            ' . $statusChecked . '
                            onchange="change_status(' . $data->id . ');"
                        >';

                    return $login_permission . ' ' . $editLink . ' ' . $deleteLink . ' ' . $changestatus;
                })

                ->addColumn('sub_manager', function ($data) {
                    // Customize the action buttons
                    $submanager = '<button style="background-color:#192e62;color:#fff;border-radius:3px" onclick="assignsubmanager(' . $data->id . ')">Assign Role</button>';
                    return $submanager;
                })
                ->rawColumns(['actions', 'status', 'disable_login', 'sub_manager', 'totalcampaigns'])
                ->toJson();
        }
        return response()->json(['error' => 'Invalid request'], 400);

    }


    public function manageemployeelogin(Request $request)
    {
        $check = RestrictEmployeelogin::where(['employee_email' => $request->employeemail, 'employee_id' => $request->employeeid])->first();
        $employeedetails = User::where('id', $request->employeeid)->first();

        if (isset($check) && !empty($check)) {
            $check->delete();
            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->description = $employeedetails->first_name . ' ' . $employeedetails->last_name . ' login permission is enabled';
            $logs->type = 8;
            $logs->save();
        } else {
            $restrictlogin = new RestrictEmployeelogin();
            $restrictlogin->employee_email = $request->employeemail;
            $restrictlogin->employee_id = $request->employeeid;
            $restrictlogin->save();

            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->description = $employeedetails->first_name . ' ' . $employeedetails->last_name . ' login permission is disabled';
            $logs->type = 8;
            $logs->save();
        }
        echo json_encode(['status' => 200, 'message' => 'Permission changed successfully']);
    }

    public function statusUpdate(Request $request)
    {
        $manager = User::findOrFail($request->id);
        if ($manager->is_active == 1) {
            $manager->is_active = 2;

            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->description = $manager->first_name . ' ' . $manager->last_name . ' account is deactivated';
            $logs->type = 9;
            $logs->save();
            $manager->save();

            echo json_encode(['status' => 200, 'message' => 'status changed', 'data' => 'disabled']);
            exit;

        } else {
            $manager->is_active = 1;
            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->description = $manager->first_name . ' ' . $manager->last_name . ' account is activated';
            $logs->type = 9;
            $logs->save();
            $manager->save();

            echo json_encode(['status' => 200, 'message' => 'status changed', 'data' => 'enabled']);
            exit;


        }
        // echo json_encode(['status' => 200, 'message' => 'status changed']);
    }

    public function man_daily_report()
    {


        if (request()->get('employee_id')) {
            $employee_id = $_GET['employee_id'];
        } else {
            $employee_id = "";
        }

        if (request()->get('campaign_id')) {
            $campaign_id = $_GET['campaign_id'];
        } else {
            $campaign_id = "";
        }

        if (request()->get('date_from')) {
            $date_from = $_GET['date_from'];
        } else {
            $date_from = "";
        }
        if (request()->get('date_to')) {
            $date_to = $_GET['date_to'];
        } else {
            $date_to = "";
        }


        $admin = User::where(['is_admin' => Null, 'id' => auth()->user()->id])->first();
        $campaignsHtml = '<select class="form-control"  name="campaign_id" id="campaign_id"><option value="">Select Campaign Name</option>';
        if (!empty($admin)) {
            $employees = User::where(['is_admin' => 1])->orderBy('name')->get()->toArray();
            if ($employee_id == NULL) {
                $campaigns = Source::orderBy('source_name')->get()->toArray();
                if (isset($campaigns) && !empty($campaigns)) {
                    foreach ($campaigns as $key => $value) {
                        $campId = $value['id'];
                        $campName = $value['source_name'] . ' ' . $value['description'];
                        if ($campaign_id == $campId) {
                            $campaignsHtml .= '<option value="' . $campId . '" selected>' . $campName . '</option>';
                        } else {
                            $campaignsHtml .= '<option value="' . $campId . '">' . $campName . '</option>';
                        }
                    }
                }
            } else {
                $campaigns = Source::join('relations', 'relations.assign_to_cam', '=', 'sources.id')
                    ->where('relations.assign_to_employee', $employee_id)->orderBy('source_name')->get();
                if (isset($campaigns) && !empty($campaigns)) {
                    foreach ($campaigns as $key => $value) {
                        $campId = $value['assign_to_cam'];
                        $campName = $value['source_name'] . ' ' . $value['description'];
                        if ($campaign_id == $campId) {
                            $campaignsHtml .= '<option value="' . $campId . '" selected>' . $campName . '</option>';
                        } else {
                            $campaignsHtml .= '<option value="' . $campId . '">' . $campName . '</option>';
                        }
                    }
                }
            }
        } else {
            $employees = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])->orderBy('name')->get()->toArray();
            $employee_ids = array_column($employees, 'id');
            if ($employee_ids == NULL) {
                $campaigns = Source::orderBy('source_name')->get()->toArray();
                if (isset($campaigns) && !empty($campaigns)) {
                    foreach ($campaigns as $key => $value) {
                        $campId = $value['id'];
                        $campName = $value['source_name'] . ' ' . $value['description'];
                        if ($campaign_id == $campId) {
                            $campaignsHtml .= '<option value="' . $campId . '" selected>' . $campName . '</option>';
                        } else {
                            $campaignsHtml .= '<option value="' . $campId . '">' . $campName . '</option>';
                        }
                    }
                }
            } else {

                if (request()->get('employee_id')) {
                    $employee_id = $_GET['employee_id'];
                    $campaigns = Source::join('relations', 'relations.assign_to_cam', '=', 'sources.id')
                        ->where('relations.assign_to_employee', $employee_id)->orderBy('source_name')->get();
                } else {
                    $campaigns = Source::join('relations', 'relations.assign_to_cam', '=', 'sources.id')
                        ->whereIN('relations.assign_to_employee', $employee_ids)->orderBy('source_name')->get();
                }

                if (isset($campaigns) && !empty($campaigns)) {
                    foreach ($campaigns as $key => $value) {
                        $campId = $value['assign_to_cam'];
                        $campName = $value['source_name'] . ' ' . $value['description'];
                        if ($campaign_id == $campId) {
                            $campaignsHtml .= '<option value="' . $campId . '" selected>' . $campName . '</option>';
                        } else {
                            $campaignsHtml .= '<option value="' . $campId . '">' . $campName . '</option>';
                        }
                    }
                }
            }
        }
        $campaignsHtml .= '</select>';

        $conversationTypes = conversationType::orderBy('type')->get()->toArray();
        return view('employee.man_daily_report')->with(['employees' => $employees, 'employee_id' => $employee_id, 'campaigns' => $campaigns, 'campaign_id' => $campaign_id, 'date_from' => $date_from, 'date_to' => $date_to, 'campaignsHtml' => $campaignsHtml, 'conversationTypes' => $conversationTypes]);
    }


    public function empdailyreport(Request $request)
    {
        if ($request->ajax()) {
            $campaign_id = $request->get('campaign_id', '');
            $date_from = $request->get('date_from', '');
            $date_to = $request->get('date_to', '');
            $filter_by = $request->get('filter_by', '');
            $reminder_for_conversation = $request->get('reminder_for_conversation', '');

            $query = Lead::select(
                'leads.id',
                'leads.prospect_first_name',
                'leads.prospect_last_name',
                'notes.reminder_for',
                'notes.feedback',
                'notes.updated_at',
                'leads.status',
                'leads.linkedin_address'
            )
                ->join('notes', 'notes.lead_id', '=', 'leads.id')
                ->where('leads.asign_to', (string)auth()->user()->id);


            // Filter by campaign ID
            if (!empty($campaign_id)) {
                $query->where('notes.source_id', (string)$campaign_id);
            }

            // Filter by date range
            if (!empty($date_from) && !empty($date_to)) {
                $query->whereBetween('notes.updated_at', [
                    date('Y-m-d H:i:s', strtotime($date_from)),
                    date('Y-m-d H:i:s', strtotime($date_to))
                ]);
            }

            // Filter by reminder status
            if (!empty($filter_by)) {
                if ($filter_by == 1) {
                    $query->whereNull('notes.reminder_for');
                } elseif ($filter_by == 2) {
                    if (!empty($reminder_for_conversation)) {
                        $query->where('notes.reminder_for', $reminder_for_conversation);
                    } else {
                        $query->whereNotNull('notes.reminder_for');
                    }
                }
            }

            // Optimize with DataTables
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('prospect_first_name', function ($row) {
                    $leadName = '<a href="' . url('/leads', [$row->id]) . '" target="_blank">' . $row->prospect_first_name . ' ' . $row->prospect_last_name . '</a>';
                    $linkedinIcon = $this->getLinkedInIcon($row->linkedin_address);
                    return $leadName . ' ' . $linkedinIcon;
                })
                ->addColumn('conversation_type', fn($row) => $row->reminder_for ?? '')
                ->addColumn('note', function ($row) {
                    return strlen($row->feedback) > 20 ? '<span title="' . $row->feedback . '">' . substr($row->feedback, 0, 20) . '...</span>' : $row->feedback;
                })
                ->editColumn('updated_at', fn($row) => date('d-m-Y H:i:s', strtotime($row->updated_at)))
                ->addColumn('status', function ($row) {
                    return $this->getStatusIcon($row->status);
                })
                ->rawColumns(['prospect_first_name', 'note', 'status'])
                ->make(true);
        }

        // Optimized campaigns query with proper selection
        $campaigns = Lead::select('source_id', DB::raw('COUNT(source_id) as totalLeads'))
            ->where('asign_to', (string)auth()->user()->id)
            ->groupBy('source_id')
            ->with('source') // eager load the source relationship
            ->get();

        $conversationTypes = conversationType::orderBy('type')->get()->toArray();

        return view('employee.employee_daily_report', [
            'campaigns' => $campaigns,
            'campaign_id' => $request->get('campaign_id', ''),
            'date_from' => $request->get('date_from', ''),
            'date_to' => $request->get('date_to', ''),
            'conversationTypes' => $conversationTypes,
        ]);
    }

    // Helper method to generate LinkedIn icon HTML
    private function getLinkedInIcon($linkedinAddress)
    {
        if (strpos($linkedinAddress, 'linkedin') === false) {
            return '<a href="javascript:void(0)"><i style="color: #000" alt="LinkedIn" title="LinkedIn Address Not Valid" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
        }

        $linkedinUrl = strpos($linkedinAddress, 'http://') !== 0 && strpos($linkedinAddress, 'https://') !== 0
            ? 'https://' . $linkedinAddress
            : $linkedinAddress;
        return '<a href="' . $linkedinUrl . '" target="_blank"><i alt="LinkedIn" title="LinkedIn" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>';
    }

    // Helper method to generate status icon HTML
    private function getStatusIcon($status)
    {
        $statusIcons = [
            0 => ['img' => 'pending.png', 'title' => 'Pending'],
            1 => ['img' => 'pending.png', 'title' => 'Pending'],
            2 => ['img' => 'failed.png', 'title' => 'Failed'],
            3 => ['img' => 'completed.png', 'title' => 'Closed'],
            4 => ['img' => 'in-progress.png', 'title' => 'In Progress'],
        ];

        return isset($statusIcons[$status])
            ? '<img style="width: 20px" src="' . asset('public/admin/assets/images/' . $statusIcons[$status]['img']) . '" alt="' . $statusIcons[$status]['title'] . '">'
            : '<img style="width: 20px" src="' . asset('public/admin/assets/images/pending.png') . '" alt="Pending">';
    }

    public function getLeadsData(Request $request)
    {
        // Initialize Filters
        $employee_id = $request->get('employee_id') ?? null;
        $campaign_id = $request->get('campaign_id') ?? null;
        $date_from = $request->get('date_from') ? date('Y-m-d H:i:s', strtotime($request->get('date_from'))) : null;
        $date_to = $request->get('date_to') ? date('Y-m-d H:i:s', strtotime($request->get('date_to'))) : null;
        $change_status = $request->get('change_status') ?? null;
        $reminder_for_conversation = $request->get('reminder_for_conversation') ?? null;
        $filter_by = $request->get('filter_by') ?? null;
        // Base Query
        // DB::enableQueryLog();        
        if (Auth::user()->is_admin == 1) {
            $query = Lead::select(
                'leads.id as lead_id',
                'leads.prospect_first_name',
                'leads.prospect_last_name',
                'leads.linkedin_address',
                'leads.asign_to',
                'notes.id as note_id',
                'notes.feedback',
                'notes.updated_at',
                'notes.status',
                'notes.reminder_for',
                'notes.source_id',
                'leads.note_created_date',
                // Add any other required fields here
            )->where('asign_to', Auth::id())
                ->join('notes', 'notes.lead_id', '=', 'leads.id')
                ->whereNotNull('notes.source_id')
                ->orderByDesc('note_created_date');
        } elseif (Auth::user()->is_admin == null) {
            $query = Lead::select(
                'leads.id as lead_id',
                'leads.prospect_first_name',
                'leads.prospect_last_name',
                'leads.linkedin_address',
                'leads.asign_to',
                'notes.id as note_id',
                'notes.feedback',
                'notes.updated_at',
                'notes.status',
                'notes.reminder_for',
                'notes.source_id',
                'leads.note_created_date',
                // Add any other required fields here
            )->join('notes', 'notes.lead_id', '=', 'leads.id')->orderByDesc('note_created_date');
        } else {
            $employee_ids = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])->orderBy('id')->pluck('id');
            // dd($employee_ids);
            $query = Lead::select(
                'leads.id as lead_id',
                'leads.prospect_first_name',
                'leads.prospect_last_name',
                'leads.linkedin_address',
                'leads.asign_to',
                'notes.id as note_id',
                'notes.feedback',
                'notes.updated_at',
                'notes.status',
                'notes.reminder_for',
                'notes.source_id',
                'leads.note_created_date',
            )->
                whereIN('asign_to', $employee_ids)
                ->join('notes', 'notes.lead_id', '=', 'leads.id');
            // ->whereNotNull('notes.source_id');
        }

        // Apply Filters
        if ($employee_id) {
            $query->where('asign_to', $employee_id);
        }

        if ($campaign_id) {
            $query->where('notes.source_id', $campaign_id);
        }

        if ($change_status) {
            $query->where('notes.status', $change_status);
        }

        if ($date_from && $date_to) {
            $dateFrom = \Carbon\Carbon::parse($date_from)->format('Y-m-d H:i:s');
            $dateTo = \Carbon\Carbon::parse($date_to)->format('Y-m-d H:i:s');
            $query->whereBetween('notes.updated_at', [$dateFrom, $dateTo]);
        }

        if ($filter_by == 1) {
            $query->whereNull('notes.reminder_for');

        } elseif ($filter_by == 2) {
            if ($reminder_for_conversation) {
                $query->where('notes.reminder_for', $reminder_for_conversation);
            }
        }


        // $query->orderByDesc('notes.created_at'); 

        // $queries = DB::getQueryLog();
        // dd($queries);



        // Fetch Data for DataTable
        return datatables()->of($query)
            ->addColumn('lead_name', function ($data) {
                $leadName = '<a href="' . url('/leads/' . $data->lead_id) . '" target="_blank">' . $data->prospect_first_name . ' ' . $data->prospect_last_name . '</a>';

                $linkedinAddress = $data['linkedin_address'] ?? ''; // Ensure the variable exists
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

            ->addColumn('conversation_type', function ($data) {
                return $data->reminder_for ?? '';
            })
            ->addColumn('note', function ($data) {
                $feedback = $data->feedback ?? '';
                return strlen($feedback) > 20 ? substr($feedback, 0, 20) . '...' : $feedback;
            })
            ->addColumn('note_date_time', function ($data) {
                return date('d-m-Y H:i:s', strtotime($data->note_created_date));
            })
            ->addColumn('status', function ($data) {
                $statusIcons = [
                    0 => '<p class="pending">Pending</p>',
                    1 => '<p class="pending">Pending</p>',
                    2 => '<p class="failed">Failed</p>',
                    3 => '<p class="completed">Completed</p>',
                    4 => '<p class="in-progress">In Progress</p>',
                ];
                return $statusIcons[$data->status] ?? '<p class="pending">Pending</p>';
            })
            ->rawColumns(['lead_name', 'status'])
            ->make(true);
    }


    public function allocatedCompaign(Request $request)
    {
        if (isset($_POST['employee_id']) && !empty($_POST['employee_id'])) {
            $employee_id = $request->input('employee_id');
        } else {
            $employee_id = '';
        }

        $admin = User::where(['is_admin' => Null, 'id' => auth()->user()->id])->first();

        if (isset($employee_id) && !empty($employee_id)) {
            $campaigns = Source::join('relations', 'relations.assign_to_cam', '=', 'sources.id')
                ->where('relations.assign_to_employee', $employee_id)->orderBy('source_name')
                ->get()->toArray();
        } else {
            if (!empty($admin)) {
                $campaigns = Source::orderBy('source_name')->get()->toArray();
            } else {
                $employees = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])->orderBy('name')->get()->toArray();
                $employee_ids = array_column($employees, 'id');
                $campaigns = Source::join('relations', 'relations.assign_to_cam', '=', 'sources.id')
                    ->whereIN('relations.assign_to_employee', $employee_ids)->orderBy('source_name')->get();
            }
        }

        if (isset($_POST['querys_']) && !empty($_POST['querys_'])) {
            $campaign_id = $request->input('querys_');
        } else {
            $campaign_id = '';
        }



        if (isset($campaign_id) && !empty($campaign_id)) {
            $campaign_ids = $campaign_id;
        } else {
            $campaign_ids = "";
        }

        if (isset($campaigns) && !empty($campaigns)) { ?>
            <option value="">Select Campaign Name</option>
            <?php
            foreach ($campaigns as $key => $value) {
                $valueID = '';
                if (isset($value['assign_to_cam']) && !empty($value['assign_to_cam'])) {
                    $valueID = $value['assign_to_cam'];
                } else {
                    $valueID = $value['id'];
                }

                echo $valueID;
                ?>
                <option <?php if ($campaign_ids == $valueID) {
                    echo "selected";
                } ?> value="<?php echo $valueID ?>">
                    <?php echo $value['source_name'] ?> (
                    <?php echo $value['description'] ?>)
                </option>
            <?php }
        } else { ?>
            <option value="">Select Campaign Name</option>
            <option value="">No Result Found!</option>
        <?php }
    }

    public function lhs_report($id)
    {
        $get_lead = Lead::where(['id' => $id])->first();
        return view('employee.export_Lhs')->with(['data' => $get_lead]);
    }

    // Save LHS report
    public function lhs_report_save(Request $request)
    {
        // dd($request->all());
        $employeeName = User::find(Auth::user()->id)->first_name . ' ' . User::find(Auth::user()->id)->last_name; // BDM manager 
        $datalead = Lead::find($request->lead_id);
        if (empty($datalead)) {
            $datalead = new Lead;
        }
        $first_name = '';
        $last_name = '';
        if (isset($request->prospect_first_name) && !empty($request->prospect_first_name)) {
            $dataname = $request->prospect_first_name;
            $array = explode(' ', $dataname, 2);
            $first_name = $array[0];
            $last_name = $array[1];
        }
        // $datalead->prospect_first_name = $first_name;
        $datalead->prospect_last_name = $last_name;
        $datalead->designation = $request->designation;
        $datalead->company_name = $request->company_name;
        $datalead->company_industry = $request->company_industry;
        $datalead->contact_number_1 = $request->contact_number_1;
        $datalead->prospect_email = $request->prospect_email;
        // $datalead->linkedin_address = $request->linkedin_address;
        $datalead->save();

        $prev = $request->previous_url;
        $uriSegments = explode("/", parse_url($prev, PHP_URL_PATH));
        $notesCount = Note::where(['lead_id' => $request->lead_id])->count();
        // dd($request->influencers_decision_making_process);
        if ($notesCount == 0) {
            return response()->json(['error' => 'Please add a note first.']);
        } else {

            if (isset($request->meeting_date2) && !empty($request->meeting_date2)) {
                $meatingdate2 = date('Y-m-d', strtotime($request->meeting_date2));
            } else {
                $meatingdate2 = NULL;
            }

            if ($request->timezone_1 == 'Select An Timezone') {
                $timeZone1 = '';
            } else {
                $timeZone1 = $request->timezone_1;
                if (!empty($timeZone1)) {
                    $meeting_time = $request->meeting_time1;
                    $converted = $this->convertAbbreviationToIST($meeting_time, $timeZone1);
                    // dd($converted);

                }
            }

            if ($request->timezone_2 == 'Select An Timezone') {
                $timeZone2 = '';
            } else {
                $timeZone2 = $request->timezone_2;
            }

            $data = array(
                'lead_id' => $request->lead_id,
                'board_no' => $request->board_no,
                'direct_no' => $request->direct_no,
                'employees_strength' => $request->employees_strength,
                'revenue' => $request->revenue,
                'address' => $request->address,
                'website' => $request->website,
                'prospect_vertical' => $request->prospect_vertical,
                'prospects_level' => $request->prospects_level,
                'opt_in_status' => $request->opt_in_status,
                'company_desc' => $request->company_desc,
                'responsibilities' => $request->responsibilities,
                'team_size' => $request->team_size,
                'pain_areas' => $request->pain_areas,
                'interest_new_initiatives' => $request->interest_new_initiatives,
                'budget' => $request->budget,
                'defined_agenda' => $request->defined_agenda,
                'call_notes' => $request->call_notes,
                'meeting_date1' => date('Y-m-d', strtotime($request->meeting_date1)),
                'meeting_date2' => $meatingdate2,
                'meeting_time1' => $request->meeting_time1,
                'meeting_time2' => $request->meeting_time2,
                'timezone_1' => $timeZone1,
                'timezone_2' => $request->timezone_2,
                // 'ext_if_any' => $timeZone2,
                'ext_if_any' => $request->ext_if_any,
                'ea_name' => $request->ea_name,
                'ea_email' => $request->ea_email,
                'prospect_email' => $request->prospect_email,
                'ea_phone_no' => $request->ea_phone_no,
                'meeting_teleconference' => $request->meeting_teleconference,
                'contact_decision_maker' => $request->contact_decision_maker,
                'influencers_decision_making_process' => $request->influencers_decision_making_process,
                'company_already_affiliated' => $request->company_already_affiliated,
            );
            $customMessages = [
                'upload_file.required' => 'Please upload a file of type .mp3 or .zip',
            ];
            $request->validate([
                'board_no' => 'required',
                //'direct_no' =>                 'required|numeric',
                'employees_strength' => 'required',
                'revenue' => 'required',
                'address' => 'required',
                'website' => 'required',
                'prospect_vertical' => 'required',
                'prospects_level' => 'required',
                'company_desc' => 'required',
                'responsibilities' => 'required',
                'team_size' => 'required',
                'opt_in_status' => 'required',
                'pain_areas' => 'required',
                'interest_new_initiatives' => 'required',
                'budget' => 'required',
                'defined_agenda' => 'required',
                'call_notes' => 'required',
                'meeting_date1' => 'required',
                //'meeting_date2' =>               'required',
                'meeting_time1' => 'required',
                //'meeting_time2' =>               'required',
                'timezone_1' => 'required',
                //'timezone_2' =>                  'required',
                //'ext_if_any' =>                  'required',
                'ea_name' => 'required',
                'ea_email' => 'required|email',
                'prospect_email' => 'required|email',
                'ea_phone_no' => 'required|numeric',
                'meeting_teleconference' => 'required|in:Face to Face meeting,Teleconference',
                'contact_decision_maker' => 'required|in:Yes,No',
                'influencers_decision_making_process' => 'required',
                'company_already_affiliated' => 'required',
                'upload_file' => 'required'
            ], $customMessages);
            Lead::where('id', $request->lead_id)->update(['status' => $request->status, 'is_notify' => 1, 'is_read' => 1]);

            $lhsResult = LhsReport::create($data);
            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->type = 3;
            $logs->reference_id = $request->lead_id;
            $logs->save();

            // Store file against LHS
            if ($lhsResult && $request->hasFile('upload_file')) {
                foreach ($request->file('upload_file') as $file) {
                    $fileName = time() . "_" . $file->getClientOriginalName();
                    $fileExt = $file->getClientOriginalExtension();

                    // Store file securely in 'lhs_files' directory
                    $filePath = $file->storeAs('lhs', $fileName, 'public'); // Saves to storage/app/public/lhs_files

                    // Save file info to database
                    LhsFiles::create([
                        "lead_id" => $request->lead_id,
                        "file_name" => $fileName,
                        "file_ext" => $fileExt,
                        "file_path" => $filePath
                    ]);
                }
            }

            $lead_data = Lead::where('id', '=', $request->lead_id)->with('lhsreport')->first();
            $source_id = $lead_data->source_id;
            $source = Source::query()->where("id", '=', $source_id)->first();


            return redirect('leads/closed')->with('success', 'LHS Report Added Successfully.');
        }
    }



    /**
     * Convert time from abbreviation (e.g. AEST) to IST.
     */


    function convertAbbreviationToIST($timeStr, $abbreviation)
    {
        // 1. Use today’s date with the time
        $dateToday = date('Y-m-d');
        $fullDateTime = $dateToday . ' ' . $timeStr;
        // dd($fullDateTime);
        $timezones = DateTimeZone::listAbbreviations();
        $matches = $timezones[strtolower($abbreviation)] ?? [];
        if (empty($matches)) {
            throw new Exception("Unknown timezone abbreviation: $abbreviation");
        }
        $timezoneName = $matches[0]['timezone_id'] ?? null;
        // dd($timezoneName);
        if (!$timezoneName) {
            throw new Exception("Cannot resolve timezone ID for abbreviation: $abbreviation");
        }
        $time = Carbon::createFromFormat('Y-m-d H:i', $fullDateTime, $timezoneName);
        // dd($time);
        $time->setTimezone('Asia/Kolkata');
        return [
            'date' => $time->format('Y-m-d'),
            'time' => $time->format('H:i:s')
        ];
    }



    public function edit_lhs_report($id)
    {

        $get_lead_report = LhsReport::where(['lead_id' => $id])->first();
        $get_lead = Lead::where(['id' => $id])->first();
        return view('employee.edit_export_Lhs')->with(['data' => $get_lead_report, 'lead_info' => $get_lead]);
    }

    public function update_lhs_report(Request $request)
    {

        $input = $request->all();
        // dd($input);
        $request->validate([
            'board_no' => 'required',
            //'direct_no' =>                   'required|numeric',
            'employees_strength' => 'required',
            'revenue' => 'required',
            'address' => 'required',
            'website' => 'required',
            'prospect_vertical' => 'required',
            'prospects_level' => 'required',
            'company_desc' => 'required',
            'responsibilities' => 'required',
            'team_size' => 'required',
            'pain_areas' => 'required',
            'opt_in_status' => 'required',
            'interest_new_initiatives' => 'required',
            'budget' => 'required',
            'defined_agenda' => 'required',
            'call_notes' => 'required',
            'meeting_date1' => 'required',
            //'meeting_date2' =>               'required',
            'meeting_time1' => 'required',
            //'meeting_time2' =>               'required',
            'timezone_1' => 'required',
            // 'timezone_2' =>                  'required',
            //'ext_if_any' =>                  'required',
            //'ea_name' =>                     'required',
            //'ea_email' =>                    'required|email',
            //'ea_phone_no' =>                 'required|numeric',
            'meeting_teleconference' => 'required|in:Face to Face meeting,Teleconference',
            'contact_decision_maker' => 'required|in:Yes,No',
            'influencers_decision_making_process' => 'required',
            'company_already_affiliated' => 'required',
            'prospect_email' => 'required',

        ]);
        // $dataLead = Lead::find($request->lead_id);
        // // $dataLead->prospect_first_name = $input['prospect_first_name'];
        // // $dataLead->prospect_last_name = $input['prospect_last_name'];
        // $dataLead->designation = $input['designation'];
        // $dataLead->company_name = $input['company_name'];
        // $dataLead->company_industry = $input['company_industry'];
        // $dataLead->contact_number_1 = $input['contact_number_1'];
        // $dataLead->prospect_email = $input['prospect_email'];
        // $dataLead->linkedin_address = $input['linkedin_address'];


        $datalead = Lead::find($request->lead_id);
        if (empty($datalead)) {
            $datalead = new Lead;
        }
        $first_name = '';
        $last_name = '';
        if (isset($request->prospect_first_name) && !empty($request->prospect_first_name)) {
            $dataname = $request->prospect_first_name;
            $array = explode(' ', $dataname, 2);
            $first_name = $array[0];
            $last_name = $array[1];
        }
        $datalead->prospect_first_name = $first_name;
        $datalead->prospect_last_name = $last_name;
        $datalead->designation = $request->designation;
        $datalead->company_name = $request->company_name;
        $datalead->company_industry = $request->company_industry;
        $datalead->contact_number_1 = $request->contact_number_1;
        $datalead->prospect_email = $request->prospect_email;
        $datalead->linkedin_address = $request->linkedin_address;
        $datalead->save();

        $data = LhsReport::find($request->lead_lhs_id);
        $data->lead_id = $input['lead_id'];
        $data->board_no = $input['board_no'];
        $data->direct_no = $input['direct_no'];
        $data->employees_strength = $input['employees_strength'];
        $data->revenue = $input['revenue'];
        $data->address = $input['address'];
        $data->website = $input['website'];
        $data->prospect_vertical = $input['prospect_vertical'];
        $data->prospects_level = $input['prospects_level'];
        $data->opt_in_status = $input['opt_in_status'];
        $data->company_desc = $input['company_desc'];
        $data->responsibilities = $input['responsibilities'];
        $data->team_size = $input['team_size'];
        $data->pain_areas = $input['pain_areas'];
        $data->interest_new_initiatives = $input['interest_new_initiatives'];
        $data->budget = $input['budget'];
        $data->defined_agenda = $input['defined_agenda'];
        $data->call_notes = $input['call_notes'];
        $data->meeting_date1 = date('Y-m-d', strtotime($input['meeting_date1']));


        if (isset($input['meeting_date2']) && !empty($input['meeting_date2'])) {
            $data->meeting_date2 = date('Y-m-d', strtotime($input['meeting_date2']));
        } else {
            $data->meeting_date2 = NULL;
        }


        // $data->meeting_date2 = date('Y-m-d', strtotime($input['meeting_date2']));
        $data->meeting_time1 = $input['meeting_time1'];
        $data->meeting_time2 = $input['meeting_time2'];
        if ($input['timezone_1'] == 'Select An Timezone') {
            $timeZone1 = '';
        } else {
            $timeZone1 = $input['timezone_1'];
        }
        $data->timezone_1 = $timeZone1;
        if ($input['timezone_2'] == 'Select An Timezone') {
            $timeZone2 = '';
        } else {
            $timeZone2 = $input['timezone_2'];
        }
        $data->timezone_2 = $timeZone2;
        $data->ext_if_any = $input['ext_if_any'];
        $data->ea_name = $input['ea_name'];
        $data->ea_email = $input['ea_email'];
        $data->ea_phone_no = $input['ea_phone_no'];
        $data->meeting_teleconference = $input['meeting_teleconference'];
        $data->contact_decision_maker = $input['contact_decision_maker'];
        $data->influencers_decision_making_process = $input['influencers_decision_making_process'];
        $data->company_already_affiliated = $input['company_already_affiliated'];


        // $dataLead->save();
        $data->save();
        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->type = 6;
        $logs->reference_id = $request->lead_id;
        $logs->save();
        return redirect()->back()->with('success', 'LHS Report Updated Successfully.');
    }

    public function view_lhs($id)
    {
        $get_lead_report = LhsReport::where(['lead_id' => $id])->first();
        $get_lead = Lead::where(['id' => $id])->first();
        return view('employee.view_lhs_report')->with(['data' => $get_lead_report, 'lead_info' => $get_lead]);
    }

    public function show_mom($leadId)
    {
        $data = Lead::find($leadId);
        return view('employee.create_mom')->with(['data' => $data]);
    }

    // public function create_mom(Request $request)
// {
//     try {
//         // Validate request
//         $request->validate([
//             'meeting_datetime' => 'required|date',
//             'time_zone' => 'required',
//             'company_name' => 'required',
//             'exl_participants' => 'required',
//             'customer_participants_and_designations' => 'required',
//             'meeting_notes' => 'required',
//             'lead_id' => 'required|exists:leads,id',
//         ]);

    //         // Prepare data for insertion
//         $fillable = $request->all();
//         $fillable['setup_by_id'] = Auth::id();
//         $isMomCreated = MomReport::create($fillable);

    //         if ($isMomCreated) {
//             // Fetch the latest inserted record
//             $lastInsertedRecord = MomReport::latest()->first();

    //             // Fetch associated users
//             $bdm = User::find($lastInsertedRecord->bdm_id);
//             $setupBy = Auth::user();

    //             $lastInsertedRecord->managerName = $bdm ? ($bdm->first_name . ' ' . $bdm->last_name) : 'N/A';
//             $lastInsertedRecord->employeeName = $setupBy->first_name . ' ' . $setupBy->last_name;

    //             // Fetch lead data
//             $lead = Lead::findOrFail($request->lead_id);
//             $source = Source::find($lead->source_id);
//             $meetingDateTime = Carbon::parse($lastInsertedRecord->meeting_datetime);

    //             // Format date as "19th of December"
//             $formattedDate = $meetingDateTime->format('jS \o\f F'); 

    //             // Format time as "10 AM"
//             $formattedTime = $meetingDateTime->format('g A'); 

    //             // Concatenate the formatted values
//             $meetingDateTimeFormatted = "{$formattedDate} at {$formattedTime}";

    //             // Prepare content for Word document
//             $htmlContent = "            
//             <div style='border: 2px solid #000; padding: 50px; width: 100%; max-width: 800px; margin: 0 auto;border:1px solid black'>

    //                 <div style='text-align: center; margin-bottom: 20px;'>
//                     <h2 style='margin: 0;font-weight: bold'>Minutes Of Meeting</h2>
//                 </div>

    //                 <table style='width: 100%; border-collapse: collapse; text-align: left;'>
//                     <tbody>
//                         <tr>
//                             <th style='border: 1px solid #000; padding: 5px; width:50%;font-weight: bold'> DATE & TIME</th>
//                             <td style='border: 1px solid #000; padding: 5px; width:50%'> {$meetingDateTimeFormatted} {$lastInsertedRecord->time_zone}</td>
//                             <th style='border: 1px solid #000; padding: 5px; width:50%;font-weight: bold'> ACCOUNT</th>
//                             <td style='border: 1px solid #000; padding: 5px; width:50%'> {$lastInsertedRecord->account}</td>
//                         </tr>
//                         <tr>
//                             <th style='border: 1px solid #000; padding: 5px; width:50%;font-weight: bold'> BDM</th>
//                             <td style='border: 1px solid #000; padding: 5px; width:50%'> {$lastInsertedRecord->managerName}</td>
//                             <th style='border: 1px solid #000; padding: 5px; width:50%;font-weight: bold'> SETUP BY</th>
//                             <td style='border: 1px solid #000; padding: 5px; width:50%'> {$lastInsertedRecord->employeeName}</td>
//                         </tr>
//                     </tbody>
//                 </table>

    //                 <table style='width: 100%; border-collapse: collapse; text-align: left; margin-top: 40px; margin-bottom: 40px;'>
//                     <tbody>
//                         <tr>
//                             <th style='border: 1px solid #000; padding: 5px;font-weight: bold'> Edgeverve’s Participants</th>
//                             <td style='border: 1px solid #000; padding: 5px;'> {$lastInsertedRecord->exl_participants}</td>
//                         </tr>
//                         <tr>
//                             <th style='border: 1px solid #000; padding: 5px;font-weight: bold'> Customer Participants and Designations</th>
//                             <td style='border: 1px solid #000; padding: 5px;'> {$lastInsertedRecord->customer_participants_and_designations}</td>
//                         </tr>
//                         <tr>
//                             <th style='border: 1px solid #000; padding: 5px;font-weight: bold'> Company</th>
//                             <td style='border: 1px solid #000; padding: 5px;'> {$lastInsertedRecord->company_name}</td>
//                         </tr>
//                     </tbody>
//                 </table>

    //                 <table style='width: 200%; border-collapse: collapse; text-align: left;'>
//                     <tbody>
//                         <tr>
//                             <th style='border: 1px solid #000; padding: 5px;width:200%;font-weight: bold'> Meeting Notes:</th>
//                         </tr>
//                         <tr>
//                             <td style='border: 1px solid #000; padding: 5px;width:200%'> {$lastInsertedRecord->meeting_notes}</td>
//                         </tr>
//                     </tbody>
//                 </table>

    //                 <table style='width: 200%; border-collapse: collapse; text-align: left;'>
//                 <tbody>
//                     <tr>
//                         <th style='border: 1px solid #000; padding: 5px;width:200%;font-weight: bold'> Additional Notes:</th>
//                     </tr>
//                     <tr>
//                         <td style='border: 1px solid #000; padding: 5px;width:200%'> {$lastInsertedRecord->additional_notes}</td>
//                     </tr>
//                 </tbody>
//             </table>

    //             </div>";




    //             // Create a new PHPWord object
//             $phpWord = new PhpWord();
//             $section = $phpWord->addSection();

    //             // Convert HTML content to Word
//             \PhpOffice\PhpWord\Shared\Html::addHtml($section, $htmlContent);

    //             // Define file path
//             $filePath = 'mom/' . date("Y-m-d") . '/';
//             $filename = $lead->prospect_first_name . $lead->prospect_last_name . "_" . time() . "_" . $lead->id . '.doc';

    //             // Ensure directory exists
//             Storage::disk('public')->makeDirectory($filePath);

    //             // Save the Word file to storage
//             $fullFilePath = storage_path("app/public/{$filePath}{$filename}");
//             $phpWord->save($fullFilePath, 'RTF');
//             // Update database with file path
//             MomReport::where('id',$isMomCreated->id)->update(['mom_file_path' => "{$filePath}{$filename}"]);

    //             // Prepare email details
//             $to = User::find($lead->user_id)->email ?? null;
//             $subject = "MoM_{$lastInsertedRecord->employeeName} : {$lead->prospect_first_name} {$lead->prospect_last_name}_{$request->company_name}_{$source->source_name}";

    //             $mailContent = [
//                 "subject" => $subject,
//                 "path" => $filePath,
//                 "filename" => $filename,
//                 "data" => $lastInsertedRecord,
//             ];

    //             // Send email notification (Uncomment in production)
//             // Mail::to($to)
//             //     ->cc('Amrinder.d@Revvlocity.com')
//             //     ->send(new MomNotification($mailContent));

    //             return redirect('leads/closed')->with('success', 'MOM Report Added Successfully.');
//         }
//     } catch (Exception $e) {
//         Log::error("Error creating MOM report: " . $e->getMessage());
//         return redirect()->back()->with('error', 'Something went wrong. Please try again.');
//     }
// }


    public function create_mom(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'meeting_datetime' => 'required|date',
                'time_zone' => 'required',
                'company_name' => 'required',
                'exl_participants' => 'required',
                'customer_participants_and_designations' => 'required',
                'meeting_notes' => 'required',
            ]);

            $fillable = $request->all();
            $fillable['setup_by_id'] = Auth::user()->id;
            $isMomCreated = MomReport::create($fillable);
            if ($isMomCreated) {
                $lastInsertedRecord = MomReport::orderBy('id', 'desc')->first();
                $managerName = User::find($lastInsertedRecord->bdm_id)->first_name . ' ' . User::find($lastInsertedRecord->bdm_id)->last_name; // BDM manager 
                $employeeName = User::find($lastInsertedRecord->setup_by_id)->first_name . ' ' . User::find($lastInsertedRecord->setup_by_id)->last_name; // BDM manager 
                $lastInsertedRecord->managerName = $managerName;
                $lastInsertedRecord->employeeName = $employeeName;
                $lead_data = Lead::where('id', '=', $request->lead_id)->first();
                $source = Source::where("id", '=', $lead_data->source_id)->first();
                include_once 'HtmlToDoc.class.php'; // Load library                
                $htd = new HTML_TO_DOC();
                $momView = view("emails.mom-report")->with(['data' => $lastInsertedRecord]);
                $fullPath = 'mom/' . $source->source_name . '-' . $source->id . '/';
                Storage::disk('public')->makeDirectory($fullPath);
                $filename = $lead_data->prospect_first_name . $lead_data->prospect_last_name . "_" . time() . "_" . $request->lead_id;
                $htd->createDoc($momView, storage_path("app/public/{$fullPath}{$filename}.doc"));
                MomReport::where('id', $isMomCreated->id)->update(['mom_file_path' => $fullPath . $filename . '.doc']);

                $leads = Lead::where('id', $request->lead_id)->update(['status' => '5']);
                $logs = new Logs();
                $logs->user_id = Auth::id();
                $logs->type = 4;
                $logs->reference_id = $request->lead_id;
                $logs->save();
                return redirect('leads/closed')->with('success', 'MOM Report Added Successfully.');
            }
        }
        //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }

    }


    // public function wordEmployeeDownSingle($id)
// {
//     try {
//         // Fetch lead data
//         $data = Lead::where('status', 3)
//             ->where("id", $id)
//             ->with('lhsreport')
//             ->first();

    //         if (!$data || !$data->lhsreport || empty($data->lhsreport->lead_id)) {
//             return redirect('leads/export_excel_pdf')->with('error', 'Data not found');
//         }

    //         // Fetch source name
//         $source_name = Source::where("id", $data->source_id)->value('source_name') ?? 'Unknown';

    //         // Generate file details
//         $firstname = $data->prospect_first_name ?? 'Unknown';
//         $lastname = $data->prospect_last_name ?? 'Unknown';
//         $date = date("-d-m-Y");
//         $timestamp = time();

    //         $relativePath = "public/Excel$date/$source_name performance $timestamp$id/";
//         $absolutePath = storage_path("app/$relativePath");
//         $filename = "$firstname$lastname$date.doc";

    //         // Ensure directory exists
//         if (!File::exists($absolutePath)) {
//             File::makeDirectory($absolutePath, 0777, true, true);
//         }

    //         // Initialize PHPWord and add section
//         $phpWord = new PhpWord();
//         $section = $phpWord->addSection();

    //         // Convert Blade view to HTML string
//         $htmlContent = view("lhs_report", ['data' => $data])->render();

    //         // Sanitize HTML
//         $cleanHtml = self::sanitizeHtml($htmlContent);

    //         // Add formatted HTML to Word document
//         Html::addHtml($section, $cleanHtml, false, false);

    //         // Save the Word file
//         $fullFilePath = "$absolutePath$filename";
//         $writer = IOFactory::createWriter($phpWord, 'RTF');
//         $writer->save($fullFilePath);

    //         // Return download response
//         return response()->download($fullFilePath, $filename, [
//             'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
//         ]);
//     } catch (\Exception $e) {
//         return back()->with('error', 'Error generating Word file: ' . $e->getMessage());
//     }
// }

    public function wordEmployeeDownSingle($id)
    {
        include_once 'HtmlToDoc.class.php';
        // Initialize class
        $htd = new HTML_TO_DOC();

        /*            Lead data get       */
        $data = Lead::where('status', '=', 3)
            ->where("id", '=', $id)
            ->with('lhsreport')
            ->first();

        /* Source name get */
        $name = Source::query()->where("id", '=', $data['source_id'])->first();

        $source_name = $name['source_name'];
        $lead_id = $data['lead_id'];
        /*      Data Get         */
        if ($data['status'] == 3 && !empty($data['lhsreport']->lead_id)) {

            $view = view("lhs_report")->with(['data' => $data]);
            $firstname = $data['prospect_first_name'];
            $lastname = $data['prospect_last_name'];
            $path = "storage/app/public/Excel" . date("-d-m-Y") . "/" . $source_name . ' performance ' . time() . $id . "/";
            $filename = $firstname . $lastname . date("-d-m-Y");
            File::makeDirectory($path, $mode = 0777, true, true);
            $htd->createDoc("$view", $path . $filename);
            $headers = array('Content-Type' => 'application/octet-stream');

            return response()->download($path . $filename . '.doc', $filename . '.doc', $headers);
        } else {
            return redirect('leads/export_excel_pdf');
        }
    }
    /**
     * Sanitize HTML content before adding to Word document
     */
    private static function sanitizeHtml($html)
    {
        // Strip unwanted tags, keep basic formatting
        return strip_tags($html, '<p><br><b><strong><i><em><ul><ol><li><h1><h2><h3><h4><h5><h6><table><tr><td><th>');
    }

    public function assignsubmanager(Request $request)
    {
        $userId = $request->employeeid;
        $sources = Source::where('user_id', $userId)
            ->orWhere('assign_to_manager', $userId)
            ->where('is_active', 1)
            ->get();

        $employees = User::where('is_admin', USER)
            ->where('user_id', Auth::id())
            ->where('id', '!=', $userId)
            ->select('id', 'first_name', 'last_name')
            ->where('is_active', 1)
            ->orderBy('first_name')
            ->get();
        // dd($sources);

        // if (!empty($sources[0])) {
        $html = view('employee.submanagermodal', compact('employees', 'sources', 'request'))->render();
        echo json_encode(['status' => 400, 'message' => '.', 'html' => $html]);
        exit;
        // } else {
        //     User::where('id', $request->employeeid)->update(['is_admin' => 3]);
        //     $userdetails = User::where('id', $request->employeeid)->first();

        //     $logs = new Logs();
        //     $logs->user_id = Auth::id();
        //     $logs->description = $userdetails->first_name . ' ' . $userdetails->last_name . ' is assigned as sub manager';
        //     $logs->type = 10;
        //     $logs->save();
        //     echo json_encode(['status' => 200, 'message' => 'There is no assigned campaign to this employee']);
        //     exit;
        // }
    }

    public function transferleademployee(Request $request)
    {
        if (isset($request->campaignid) && !empty($request->campaignid)) {
            for ($i = 0; $i < count($request->campaignid); $i++) {
                $data = Lead::where('asign_to', $request->old_employee_id)->where('source_id', $request->campaignid[$i])->update(['asign_to' => $request->transferleadtoemployee[$i]]);
            }
        }
        User::where('id', $request->old_employee_id)->update(['is_admin' => 3]);
        $userdetails = User::where('id', $request->old_employee_id)->first();

        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->description = $userdetails->first_name . ' ' . $userdetails->last_name . ' is assigned as sub manager';
        $logs->type = 10;
        $logs->save();
        echo json_encode(['status' => 200, 'message' => 'Campaigns Transfered']);
        exit;
    }

    public function assignemployees(Request $request)
    {
        if (isset($request->selectedEmployees) && !empty($request->selectedEmployees)) {
            foreach ($request->selectedEmployees as $employees) {
                User::where('id', $employees)->update(['user_id' => $request->submanagerid]);
            }
        }
        echo json_encode(['status' => 200, 'message' => 'Employee Assigned']);
        exit;
    }

    public function submanagerlisting(Request $request)
    {
        if (Auth::user()->is_admin == null) {
            $totalsubmanagers = User::where('is_admin', SUBMANAGER)
                ->count();
            $inactive = User::where('is_admin', SUBMANAGER)
                ->where('is_active', operator: 2)
                ->count();
            $active = User::where('is_admin', SUBMANAGER)
                ->where('is_active', 1)
                ->count();
        } else {
            $totalsubmanagers = User::where('is_admin', SUBMANAGER)
                ->where('user_id', Auth::id())
                ->count();
            $inactive = User::where('is_admin', SUBMANAGER)
                ->where('user_id', Auth::id())
                ->where('is_active', operator: 2)
                ->count();
            $active = User::where('is_admin', SUBMANAGER)
                ->where('user_id', Auth::id())
                ->where('is_active', 1)
                ->count();
        }
        return view('employee.submanagerlisting', compact('inactive', 'active', 'totalsubmanagers'));
    }

    public function submanagerlistingdata(Request $request)
    {
        if ($request->ajax()) {
            if ($request->status_filter == 'total') {
                $status = [1, 2];
            } elseif ($request->status_filter == 'active') {
                $status = [1];
            } else {
                $status = [2];
            }
            if (Auth::user()->is_admin == null) {
                $managers = User::where('is_admin', SUBMANAGER)
                    ->whereIn('is_active', $status)
                    ->select('id', 'first_name', 'last_name', 'image', 'email', 'orignal_password', 'address', 'phone_no', 'manager_type', 'is_active')
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $managers = User::where('is_admin', SUBMANAGER)
                    ->where('user_id', Auth::id())
                    ->whereIn('is_active', $status)
                    ->select('id', 'first_name', 'last_name', 'image', 'email', 'orignal_password', 'address', 'phone_no', 'manager_type', 'is_active')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            return DataTables::of($managers)
                ->addColumn('totalemployees', function ($data) {
                    // Count users where user_id matches the current data id
                    $totalemployees = User::where('user_id', $data->id)->where('is_admin', 1)->count();

                    // Return an anchor tag with the count
                    return '<p  class="view_emp" title="View Employee" onclick="viewemployees(' . $data->id . ')">' . $totalemployees . '</p>';
                })
                ->addColumn('totalcampaigns', function ($data) {
                    // Count users where user_id matches the current data id
                    $totalcampaigns = Source::where(function ($q) use ($data) {
                        $q->where('user_id', $data->id)
                            ->orWhere('assign_to_manager', $data->id);
                    })
                        ->where('is_active', 1)
                        ->count();


                    return '<p  class="view_emp" title="View Campagins" onclick="viewCampaigns(' . $data->id . ')">' . $totalcampaigns . '</p>';
                })
                ->addColumn('phone_no', function ($data) {
                    if (empty($data->phone_no)) {
                        $data->phone_no = 'N/A';

                    }

                })


                ->addColumn('actions', function ($data) {
                    // Customize the action buttons
                    $editLink = '<i class="fa-solid fa-pen-to-square text-success editEmployee" 
                        data-url="' . route('employee.submanageredit', ['employee_id' => $data->id]) . '" style="cursor: pointer; margin-right: 8px;"></i>';

                    /**
                     * Login permission switch
                     */
                    $check = RestrictEmployeelogin::where('employee_id', $data->id)->first();
                    $loginChecked = $check ? '' : 'checked';

                    $login_permission = '
                        <input
                            type="checkbox"
                            class="switchery"
                            data-tooltip="' . ($loginChecked ? 'Disable Login' : 'Enable Login') . '"
                            data-id="' . $data->id . '"
                            onchange="disablelogin(' . $data->id . ', \'' . addslashes($data->email) . '\');"
                            ' . $loginChecked . '
                        >';

                    /**
                     * Active / Inactive status switch
                     */
                    $statusChecked = $data->is_active == 1 ? 'checked' : '';
                    $changestatus = '
                        <input
                            data-sid="' . $data->source_id . '"
                            data-id="' . $data->id . '"
                            class="switchery"
                            type="checkbox"
                            id="togglebtn"
                            data-tooltip="' . ($statusChecked ? 'Deactivate Employee' : 'Activate Employee') . '"
                            ' . $statusChecked . '
                        >';

                    return $login_permission . ' ' . $editLink . ' ' . $changestatus;
                })

                ->rawColumns(['actions', 'status', 'totalemployees', 'totalcampaigns'])
                ->toJson();
        }
        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function viewcampaigns(Request $request)
    {
        $userId = $request->managerid; // or pass via request if needed

        $campaigns = Source::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhere('assign_to_manager', $userId);
        })
            ->where('is_active', 1)
            ->get();

        $html = view('employee.managerempcampaigns', compact('campaigns'))->render();

        return response()->json([
            'status' => 200,
            'message' => 'Campaign Listing',
            'html' => $html
        ]);
    }

    public function viewemployees(Request $request)
    {
        $employees = User::select('id', 'first_name', 'last_name')->where('user_id', $request->managerid)->where('is_admin', 1)->get();
        $html = view('employee.manageremp', compact('employees'))->render();
        echo json_encode(['status' => 200, 'message' => 'Employee Listing', 'html' => $html]);
        exit;

    }

    public function submanageredit(Request $request)
    {
        $employee = User::where('id', $request->employee_id)->first();
        $employees = User::where('user_id', $request->employee_id)->where('is_admin', 1)->get();
        $unassignedemployees = User::where('user_id', Auth::id())->where('id', '!=', $request->employee_id)->where('is_admin', 1)->get();
        return view('employee.submanageredit', compact('employee', 'employees', 'unassignedemployees'));
    }

    public function removeemployee(Request $request)
    {
        User::where('id', $request->id)->update(['user_id' => Auth::id()]);
        echo json_encode(['status' => 200, 'message' => 'Employee Removed']);
        exit;
    }

    public function createmanager(Request $request)
    {
        $employees = User::where(['user_id' => Auth::id(), 'is_admin' => 1])->get();
        return view('employee.createmanager', compact('employees'));
    }

    public function managerstore(Request $request)
    {
        $password = Str::random(12);
        $check = User::where('email', $request->email)->first();
        if (!empty($check)) {
            echo json_encode(['status' => 400, 'message' => 'Email Already Exists']);
            exit;
        }
        $submanager = new User();
        $submanager->user_id = Auth::id();
        $submanager->first_name = $request->first_name;
        $submanager->last_name = $request->last_name;
        $submanager->orignal_password = $password;
        $submanager->password = Hash::make($password);
        $submanager->email = $request->email;
        $submanager->is_admin = SUBMANAGER;
        $submanager->phone_no = $request->phone_no;
        $submanager->address = $request->address;
        $submanager->save();

        $logs = new Logs();
        $logs->user_id = Auth::id();
        $logs->description = 'A new sub manager ' . $request->first_name . ' ' . $request->last_name . ' is added.';
        $logs->type = 7;
        $logs->save();
        $employeeNames = [];

        if (isset($request->employees) && !empty($request->employees)) {
            foreach ($request->employees as $employeeId) {
                // Update each employee's user_id to the new submanager
                User::where('id', $employeeId)->update(['user_id' => $submanager->id]);

                // Fetch name for log
                $employee = User::find($employeeId);
                if ($employee) {
                    $employeeNames[] = $employee->first_name . ' ' . $employee->last_name;
                }
            }
            $logMessage = implode(', ', $employeeNames) . ' assigned to ' . $request->first_name . ' ' . $request->last_name;

            $logs = new Logs();
            $logs->user_id = Auth::id();
            $logs->description = $logMessage;
            $logs->type = 16;
            $logs->save();

        }
        echo json_encode(['status' => 200, 'message' => 'Sub Manager Created']);
        exit;
    }




}
