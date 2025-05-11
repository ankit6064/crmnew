<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Source;
use App\Models\User;
use App\Models\Note;
use App\Http\Requests\LeadRequest;
use App\Http\Requests\SearchLeadRequest;
use App\Http\Requests\AssignLeadRequest;
use App\Http\Requests\AssignLeadEmployeeRequest;
use App\Models\LhsReport;
use App\Models\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mockery\Matcher\Not;
use App\Models\conversationType;
use App\Models\LhsFiles;

class LeadsController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (request()->get('status')) {
            $status = $_GET['status'];
        } else {
            $status = "";
        }

        $admin = User::where(['is_admin' => Null, 'id' => auth()->user()->id])->first();
        if (!empty($admin)) {
            if (!isset($_GET['status'])) {
                $data = Lead::with('source')->get()->toArray();
                // $data = Lead::where(['user_id'=>auth()->user()->id])->with('source')->with('feedback')->get()->toArray();
            } else {
                if ($_GET['status'] == 3) {
                    $data = Lead::where(['status' => $_GET['status']])->with('source')->orderBy('updated_at', 'DESC')->get()->toArray();
                } else {
                    $data = Lead::where(['status' => $_GET['status']])->with('source')->get()->toArray();
                }
            }
            $sources = Source::get()->toArray();
        } else {
            if (!isset($_GET['status'])) {
                $data = Lead::Where(['asign_to_manager' => auth()->user()->id])->with('source')->get()->toArray();
            } else {
                $data = Lead::with(['source','momReport', 'lhsreport'])->where(['asign_to_manager' => auth()->user()->id, 'status' => $_GET['status']])->get()->toArray();
            }
            $sources = Source::where(['user_id' => auth()->user()->id])->orWhere(['assign_to_manager' => auth()->user()->id])->orderBy('source_name')->get()->toArray();
        }
        $source_ids = "";
        return view('leads.list')->with(['data' => $data, 'sources' => $sources, 'source_ids' => $source_ids]);
    }

    public function closed()
    {
        $data = Lead::with(['source', 'momReport'])
            ->where(['asign_to' => auth()->user()->id])
            ->where(['status' => '3'])
            ->whereHas('source', function ($query) {
                $query->where('is_active', 1);
            })->orderBy('id', 'DESC')->get()->toArray();
        return view('leads.closed')->with(['data' => $data]);
    }


    public function create()
    {
        if (Auth::user()->is_admin == null) {
            $sources = Source::orderBy('source_name')->get()->toArray();
        } else if (Auth::user()->is_admin == 1) {
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
            $sources = Source::where(['assign_to_manager' => auth()->user()->id])->orderBy('source_name')->get()->toArray();
        }
        return view('leads.add')->with(['sources' => $sources]);
    }


    public function store(Request $request)
    {
        $assign = Source::where('id', $request->source_id)->first();

        $approval_status = null;
        if (Auth::user()->is_admin == 1) {
            $approval_status = '2';
        }

        $data = array(
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
        );

        lead::create($data);

        if (Auth::user()->is_admin == 1) {
            return redirect('leads/unapproved')->with('success', 'Lead Added Successfully.');
        } else {
            return redirect('leads/assign_lead_emp/' . $request->source_id)->with('success', 'Lead Added Successfully.');
        }
    }


    public function show($id)
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
        // dd($id);
        $admin = User::where(['is_admin' => Null, 'id' => auth()->user()->id])->first();
        if (!empty($admin)) {
            $sources = Source::orderBy('source_name')->get()->toArray();
            //$sources = Source::where(['user_id'=>auth()->user()->id])->select('id','source_name')->get()->toArray();

        } elseif (Auth::User()->is_admin == 1) {
            $sources = Source::select('id', 'source_name')->orderBy('source_name')->get()->toArray();

        } else {
            $sources = Source::where(['user_id' => auth()->user()->id])->select('id', 'source_name')->orWhere(['assign_to_manager' => auth()->user()->id])->orderBy('source_name')->get()->toArray();

        }

        //    dd($sources);

        $data = Lead::where(['id' => $id])->first();
        return view('leads.edit')->with(['data' => $data, 'sources' => $sources]);
    }


    public function update(Request $request, $id)
    {
        // dd($request);
        // $validator = Validator::make(
        //     $request->all(), [
        //         'source_id' => 'required',
        //         // 'company_name' => 'required|min:3|max:20',
        //         'prospect_first_name' => 'required|min:3|max:20',
        //         'prospect_last_name' => 'required|min:3|max:20',
        //         // 'designation' => 'required|min:3|max:20',
        //         // 'designation_level' => 'required|min:1|max:20',
        //         'prospect_email' => 'required|email|unique:leads,prospect_email,'.$id,
        //         'contact_number_1' => 'required|min:10|numeric|unique:leads,contact_number_1,'.$id,
        //         // 'contact_number_2' => 'required|min:10|numeric|unique:leads,contact_number_2,'.$id,
        //         // 'company_industry' => 'required|min:3|max:20',
        //         // 'linkedin_address' => 'required|min:3',
        //         // 'location' => 'required|min:3',
        //         // 'bussiness_function' => 'required|numeric',
        //         // 'timezone' => 'required|numeric',

        //         /* 'physical_address' => 'required|min:3|max:50',
        //         'city' => 'required|min:3|max:20',
        //         'state' => 'required|min:3|max:20',
        //         'zip_code' => 'required|min:3|max:20',
        //         'country' => 'required|min:3|max:20',
        //         */
        //     ],
        //     $messages = [
        //         'required' => 'The :attribute field is required.',
        //     ]
        // );

        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }   

        $input = $request->all();

        $data = Lead::find($id);

        // $data->source_id = $input['source_id'];

        // if(!empty($input['designation_level'])){
        //     $data->designation_level = $input['designation_level'];
        // }
        $data->designation_level = $input['designation_level'];
        // if(!empty($input['designation'])){
        //     $data->designation = $input['designation'];
        // }
        $data->designation = $input['designation'];
        // if(!empty($input['company_name'])){
        //     $data->company_name = $input['company_name'];
        // }

        $data->company_name = $input['company_name'];

        // if(!empty($input['contact_number_2'])){
        //     $data->contact_number_2 = $input['contact_number_2'];
        // }

        $data->contact_number_2 = $input['contact_number_2'];
        // if(!empty($input['company_industry'])){
        //     $data->company_industry = $input['company_industry'];
        // }

        $data->company_industry = $input['company_industry'];

        // if(!empty($input['bussiness_function'])){
        //     $data->bussiness_function = $input['bussiness_function'];
        // }

        $data->bussiness_function = $input['bussiness_function'];
        // if(!empty($input['prospect_name'])){
        //     $data->prospect_name = $input['prospect_name'];
        // }

        $data->prospect_name = $input['prospect_name'];

        // if(!empty($input['linkedin_address'])){
        //     $data->linkedin_address = $input['linkedin_address'];
        // }
        $data->linkedin_address = $input['linkedin_address'];

        // if(!empty($input['prospect_first_name'])){
        //     $data->prospect_first_name = $input['prospect_first_name'];
        // }
        $data->prospect_first_name = $input['prospect_first_name'];

        // if(!empty($input['prospect_last_name'])){
        //     $data->prospect_last_name = $input['prospect_last_name'];
        // }
        $data->prospect_last_name = $input['prospect_last_name'];

        // if(!empty($input['prospect_email'])){
        //     $data->prospect_email = $input['prospect_email'];
        // }
        $data->prospect_email = $input['prospect_email'];

        // if(!empty($input['contact_number_1'])){
        //     $data->contact_number_1 = $input['contact_number_1'];
        // }
        $data->contact_number_1 = $input['contact_number_1'];

        // $data->location = $input['location'];
        $data->timezone = $input['timezone'];
        /* $data->country = $input['country'];
         $data->lead_name = $input['job_title'];*/


        $data->save();
        if (Auth::user()->is_admin == 1) {
            return redirect('campaign/camp_assign_emp/' . $request->source_id . '')->with('success', 'Lead Updated Successfully.');
            // return redirect('campaign/camp_assign_emp')->with('success', 'Lead Updated Successfully.');
        } else {
            return redirect('leads')->with('success', 'Lead Updated Successfully.');
        }

    }


    public function destroy($id)
    {
        //
    }

    public function delete($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();
        return redirect('leads')->with('success', 'Lead Deleted Successfully.');
    }

    public function searchLead(SearchLeadRequest $request)
    {

        if ($request->has('source_id') && !empty($request->input('source_id'))) {
            $data = Lead::where('source_id', $request->source_id)->with('source')->get()->toArray();
            $source_ids = $request->source_id;
        } else {
            $data = Lead::where(['user_id' => auth()->user()->id])->with('source')->get()->toArray();
            $source_ids = "";
        }

        $sources = Source::where(['user_id' => auth()->user()->id])->get()->toArray();

        return view('leads.list')->with(['data' => $data, 'sources' => $sources, 'source_ids' => $source_ids]);
        //return redirect('leads')->with(['data'=>$data,'sources'=>$sources]);
    }


    public function view()
    {
        $data = Lead::where(['user_id' => auth()->user()->id])->whereNotIn('status', [1])->with('source')->get()->toArray();
        $employees = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])->get()->toArray();
        $employee_ids = "";
        return view('leads.view')->with(['employees' => $employees, 'data' => $data, 'employee_ids' => $employee_ids]);
    }


    public function views()
    {
        //dd('dsddsdd');
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
        //dd($campaign_id);
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


        $data = new Lead;
        if (request()->get('employee_id')) {

            $data = $data->where(['asign_to' => request()->get('employee_id')]);
        }
        if (request()->get('campaign_id')) {
            $data = $data->where(['source_id' => request()->get('campaign_id')]);
        }

        if (request()->get('campaign_id') && request()->get('employee_id')) {
            $data = $data->where('source_id', '=', $_GET['campaign_id'])->where('asign_to', '=', $_GET['employee_id']);
        }

        if (request()->get('date_from') && request()->get('date_to')) {
            // $from = date($_GET['date_from']);
            // $to = date($_GET['date_to']);
            $from = date('Y-m-d', strtotime($_GET['date_from']));
            $to = date('Y-m-d', strtotime($_GET['date_to']));
            $data = $data->whereBetween('created_at', [$from, $to]);

        }
        if (auth()->user()->is_admin == 2) {
            $data = $data->get()->toArray();
        } else {
            $data = $data->with('source')->get()->toArray();
        }
        $admin = User::where(['is_admin' => Null, 'id' => auth()->user()->id])->first();
        if (!empty($admin)) {
            $employees = User::where(['is_admin' => 1])->orderBy('name')->get()->toArray();
            $campaigns = Source::orderBy('source_name')->get()->toArray();
        } else {
            $employees = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1'])->orderBy('name')->get()->toArray();
            $campaigns = Source::where(['assign_to_manager' => auth()->user()->id])->orderBy('source_name')->get()->toArray();
        }

        return view('leads.view')->with(['employees' => $employees, 'data' => $data, 'employee_id' => $employee_id, 'campaigns' => $campaigns, 'campaign_id' => $campaign_id, 'date_from' => $date_from, 'date_to' => $date_to]);
    }

    public function leadview($id)
    {

        //dd('jdjjdj');

        $admin = User::where(['is_admin' => Null, 'id' => auth()->user()->id])->first();
        if (!empty($admin)) {
            $data = Lead::where(['source_id' => $id])->with('source')->with('feedback')->get()->toArray();

            $sources = Source::select('id', 'source_name', 'description')->orderBy('source_name')->get()->toArray();
        } else {

            $data = Lead::where(['source_id' => $id])->with('source')->with('feedback')->get()->toArray();
            $sources = Source::where(['user_id' => auth()->user()->id])->orWhere(['assign_to_manager' => auth()->user()->id])->select('id', 'source_name', 'description')->orderBy('source_name')->get()->toArray();
        }
        $source_ids = $id;
        return view('leads.leadview')->with(['data' => $data, 'sources' => $sources, 'source_ids' => $source_ids]);
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

    public function assigns(Request $request)
    {

        /*if($request->has('employee_id') && !empty($request->input('employee_id'))) {
            $data = Lead::where('asign_to', $request->employee_id)->with('source')->get()->toArray();
        } else {
            $data = Lead::with('source')->get()->toArray();
        }*/
        $data = Lead::with('source')->where(['status' => '1'])->get()->toArray();
        $employees = User::where(['is_admin' => '1'])->get()->toArray();

        return view('leads.assign')->with(['employees' => $employees, 'data' => $data]);
    }


    public function assign_lead_emp($id = null)
    {
        $admin = User::where(['is_admin' => Null, 'id' => auth()->user()->id])->first();
        if (!empty($admin)) {
            $employees = User::where(['is_admin' => 1, 'is_active' => 1])->orderBy('name')->get()->toArray();
            $campaigns = Source::orderBy('source_name')->get()->toArray();
        } else {
            $employees = User::where(['user_id' => auth()->user()->id, 'is_admin' => '1', 'is_active' => 1])->orderBy('name')->get()->toArray();
            $campaigns = Source::where(['assign_to_manager' => auth()->user()->id])->where('is_active', 1)->orderBy('source_name')->get()->toArray();
        }

        $sources = Source::where(function ($query) {
            $query->where(['user_id' => auth()->user()->id])
                ->orWhere(['assign_to_manager' => auth()->user()->id]);
        })
            ->where('is_active', 1) // Condition for is_active = 1
            ->orderBy('source_name')
            ->get()
            ->toArray();


        // $sources = Source::where(['user_id'=>auth()->user()->id])->orWhere(['assign_to_manager'=>auth()->user()->id])->orderBy('source_name')->get()->toArray();
        // $data = Lead::with('source')->where(['asign_to_manager'=>auth()->user()->id,'status'=>'1','asign_to'=>NULL])->orWhere(['user_id'=>auth()->user()->id])->offset(0)
        //                 ->limit(10)->get()->toArray();
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
           <label class="control-label">Enter Assign Leads Count</label>
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
                <td class="wraping"><div class="reassigned"><a class="unassigned" href="javascript:void(0);" data-camp="' . $table_data->source_id . '" data-asign="' . $table_data->asign_to . '"><span class="label label-warning">Withdraw</span></a><a data-toggle="modal" data-target="#RevertModel" class="RevertModel" href="javascript:void(0);" data-id="' . $table_data->source_id . '"  data-total="' . $table_data->totalLeads . '" data-asign="' . $table_data->asign_to . '"><span class="label label-warning">Reassign</span></a></div></td>
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

        $employee_id = $request->user_id;
        $campaign_id = $request->camp_id;
        if (DB::table('leads')->where('source_id', $campaign_id)->where('asign_to', $employee_id)->update(['asign_to' => NULL]) != 0) {
            DB::table('relations')->where('assign_to_cam', $campaign_id)->where('assign_to_employee', $employee_id)->delete();
        }
    }

    public function Reassigned(Request $request)
    {

        $prev_id = $request->user_id;
        $campaign_id = $request->camp_id;
        $assign_leads = $request->leads;
        $employee_id = $request->new_id;
        DB::table('relations')->where('assign_to_cam', $campaign_id)->where('assign_to_employee', $prev_id)->delete();
        DB::table('leads')->where('source_id', $campaign_id)->where('asign_to', $prev_id)->update(['asign_to' => NULL]);

        $data = array(
            'assign_to_cam' => $campaign_id,
            'assign_to_employee' => $employee_id,
            'assign_to_manager' => auth()->user()->id,
            'lead_assigned' => $assign_leads,
        );

        // DB::table('reassigned_log')->insert(
        //     ['camp_id' => $campaign_id, 'user_unassign' =>$prev_id, 'user_assign' =>$employee_id ]
        // );

        $relation = Relation::where('assign_to_employee', $employee_id)->where('assign_to_cam', $campaign_id)->first();
        if ($relation == null) {
            Relation::create($data);
        } else {
            $total_assign = $assign_leads + $relation['lead_assigned'];
            $data1 = array(
                'lead_assigned' => $total_assign,
            );
            Relation::where('id', $relation['id'])->update($data1);
        }
        $sources_data = Source::where(['id' => $campaign_id])->first();
        $get_assign_records = Lead::where('source_id', $campaign_id)->whereNull('asign_to')->take($assign_leads)->get();
        foreach ($get_assign_records as $getassigndata) {
            Lead::where('source_id', $campaign_id)->where('id', $getassigndata->id)->update(['asign_to' => $employee_id]);
        }
    }




    public function assingParticalurleads(Request $request)
    {
        $employee_id = $request->emp_id;
        $assign_leads = $request->assign_leads;
        $campaign_id = $request->cmp_id;
        $data = array(
            'assign_to_cam' => $campaign_id,
            'assign_to_employee' => $employee_id,
            'assign_to_manager' => auth()->user()->id,
            'lead_assigned' => $assign_leads,
        );
        $relation = Relation::where('assign_to_employee', $employee_id)->where('assign_to_cam', $campaign_id)->first();
        if ($relation == null) {
            Relation::create($data);
        } else {
            $total_assign = $assign_leads + $relation['lead_assigned'];
            $data1 = array(
                'lead_assigned' => $total_assign,
            );
            Relation::where('id', $relation['id'])->update($data1);
        }
        $sources_data = Source::where(['id' => $campaign_id])->first();
        $get_assign_records = Lead::where('source_id', $campaign_id)->whereNull('asign_to')->take($assign_leads)->get();
        foreach ($get_assign_records as $getassigndata) {
            Lead::where('source_id', $campaign_id)->where('id', $getassigndata->id)->update(['asign_to' => $employee_id]);
        }
        $update_leads_count = Lead::where('source_id', $campaign_id)->whereNull('asign_to')->count();
        $get_table = DB::table('leads')
            ->select('*', DB::raw('COUNT(asign_to) as totalLeads'))
            ->where('source_id', $campaign_id)
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
                <!--th>Campaign End Date</th-->
                <th>Action</th>
                </tr>
                </thead>
                <tbody>';
        foreach ($get_table as $key => $table_data) {
            $User_info = User::where(['id' => $table_data->asign_to])->first();
            $table .= '<tr>
                    <td class="wraping"> ' . $sources_data->source_name . ' (' . $sources_data->description . ') </td>
                    <td class="wraping"> ' . $table_data->totalLeads . ' </td>
                    <td class="wraping"> ' . $User_info->name . ' </td>
                    <!--td class="wraping"> ' . $sources_data->start_date . ' </td-->
                    <!--td class="wraping"> ' . $sources_data->end_date . ' </td-->
                    <td class="wraping"><div class="reassigned"><a class="unassigned" href="javascript:void(0);" data-camp="' . $table_data->source_id . '" data-asign="' . $table_data->asign_to . '"><span class="label label-warning">Withdraw</span></a><a data-toggle="modal" data-target="#RevertModel" class="RevertModel" href="javascript:void(0);" data-id="' . $table_data->source_id . '" data-total="' . $table_data->totalLeads . '" data-asign="' . $table_data->asign_to . '"><span class="label label-warning">Reassign</span></a></div></td></tr>';
        }
        $table .= '</tbody> </table>';

        return response()->json([
            'success' => true,
            'data' => $update_leads_count,
            'table' => $table,
        ]);
        //return redirect('leads/assigned_leads')->with('success', 'Leads Assigned Successfully.');

    }



    public function failed()
    {
        $data = Lead::with('source')
            ->where(['asign_to' => auth()->user()->id])
            ->with('feedback')
            ->where(['status' => '2'])
            ->whereHas('source', function ($query) {
                $query->where('is_active', 1);
            })->orderBy('updated_at', 'DESC')->get()->toArray();
        return view('leads.failed')->with(['data' => $data]);
    }


    // This fuction is used to get the list of unapproved leads for default listing.....
    public function unapproved()
    {
        $leadsData = Lead::with('source')->where('user_id', auth()->user()->id)
            ->where('approval_status', '2')
            ->orderBy('updated_at', 'DESC')
            ->paginate(10); // Change '10' to the number of items per page you prefer
        return view('leads.unapproved')->with(['leadsDatanew' => $leadsData]);
    }


    // This fuction is used to get the list of unapproved leads for pagination content listing.....
    public function unapprovedLeadsajaxPagination(Request $request)
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
            if ($columnSortColumn == '0') {
                $orderByColumn = 'company_name';
            } else if ($columnSortColumn == "1") {
                $orderByColumn = 'prospect_first_name';
            } else if ($columnSortColumn == "2") {
                $orderByColumn = 'designation';
            } else if ($columnSortColumn == "3") {
                $orderByColumn = 'created_at';
            }


            // Total records
            $totalRecords = Lead::where('user_id', auth()->user()->id)->where('approval_status', '2')->count();

            $baseQuery = Lead::with('source')->where('user_id', auth()->user()->id)->where('approval_status', '2');

            if (isset($orderByColumn) && !empty($orderByColumn)) {
                $leadsData = $baseQuery->orderBy($orderByColumn, $columnSortOrder);
            } else {
                $leadsData = $baseQuery->orderBy('created_at', 'desc');
            }

            $leadsData = $baseQuery->skip($start)->take($rowperpage)->get()->toArray();


            if ($columnSortColumn == "4") {
                usort($leadsData, function ($a, $b) use ($columnSortOrder) {
                    if ($columnSortOrder === 'asc') {
                        return strcmp($a['source']['source_name'], $b['source']['source_name']);
                    } else {
                        return strcmp($b['source']['source_name'], $a['source']['source_name']);
                    }
                });
            }


            // Transform the data to match the expected structure
            $formattedData = [];
            foreach ($leadsData as $lead) {
                $var = $lead['linkedin_address'];
                if (strpos($var, 'linkedin') == -1) {
                    $linkdin = '<td><a href="javascript:void(0)" ><i style="color: #000" alt="LinkedIn" title="LinkedIn Address Not Valid" class="fa-brands fa-linkedin" aria-hidden="true"></i></a></td>';
                } else {
                    $linkdin = '<td><a href="" target="_blank" ><i  alt="LinkedIn" title="LinkedIn" class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
                    </td>';
                }

                $formattedData[] = [
                    'company_name' => $lead['company_name'],
                    'prospect_full_name' => '<a href="/leads/' . $lead['id'] . '">' . $lead['prospect_first_name'] . ' ' . $lead['prospect_last_name'] . '</a>',
                    'designation' => $lead['designation'],
                    'created_at' => date('d M, Y', strtotime($lead['created_at'])),
                    'source_name' => $lead['source']['source_name'] . ' ' . $lead['source']['description'],
                    'LinkedIn' => $linkdin,
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



    // This fuction is used to get the list of unapproved leads for default listing.....
    public function unapprovedLeads()
    {
        $leadsData = Lead::with('source')->where('asign_to_manager', auth()->user()->id)
            ->where('approval_status', '2')
            ->orderBy('updated_at', 'DESC')
            ->paginate(10); // Change '10' to the number of items per page you prefer
        return view('leads.unapproved_leads')->with(['leadsDatanew' => $leadsData]);
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

            // Total records
            $totalRecords = Lead::where('asign_to_manager', auth()->user()->id)->where('approval_status', '2')->count();

            $baseQuery = Lead::with('source')->where('asign_to_manager', auth()->user()->id)->where('approval_status', '2');

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

            // echo "<pre>";
            // print_r($leadsData);
            // die();

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
                    'prospect_full_name' => '<a href="/leads/' . $lead["id"] . '">' . $lead["prospect_first_name"] . ' ' . $lead["prospect_last_name"] . '</a>',
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

    // assign  lead to manager by admin
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
    public function assignLeadsEmployee(AssignLeadRequest $request)
    {
        $data = array(
            'employee_id' => $request->employee_id,
            'lead_id' => $request->lead_id
        );

        foreach ($request->lead_id as $leadid) {
            Lead::where('id', $leadid)->update(['asign_to' => $request->employee_id]);
        }

        return redirect('leads/assign')->with('success', 'Leads Assigned Successfully.');

    }
    public function assignHalfLeadsEmployee(Request $request)
    {
        //dd($request);
        $employee_id = $request->employee_id;
        $lead_id = $request->lead_id;
        $source_id = $request->source_id;

        Lead::where('source_id', $source_id)->update(['asign_to' => $request->employee_id]);
        return redirect('leads/assign_lead_emp')->with('success', 'Leads Assigned Successfully.');

    }

    public function getLeads(Request $request)
    {
        $data = array();
        if ($request->has('employee_id')) {
            $data = Lead::where('asign_to', $request->employee_id)->orderBy('prospect_first_name')->orderBy('prospect_last_name')->get()->toArray();
        }
        return response()->json($data);
    }
    /*     ANMOL         */
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
            'phone_number'=>$request->phone_number
        );
        Note::create($data);
        $date = date('Y-m-d H:i:s');
        Lead::where('id', $request->lead_id)->update(array('note_created_date' => $date));
        return response()->json(['success' => 'Note Added Successfully']);
        // }
    }
    public function changeStatus(Request $request)
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
        //dd( $request);
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
                // if ($conversationType != 'Meeting Set-up') {
                //     return response()->json(['error' => 'You can\'t create LHS report as conversation type is not "meeting set-up"']);
                // }
                $html = '';
                $hostname = Config::get('app.url');
                $hostnameNew = "http://192.168.31.91";//Config::get('app.url');
                $Current_url = $hostnameNew . "/employee/lhs_report/" . $request->lead_id . "?status=" . $request->status;
                $html = '<li class="error_list"><span class="tab">Please add  LHS Report first.</span><a href="' . $Current_url . '" ><span class="tab">Click here to add Lhs Report</span></a></li>';
                return response()->json(['error' => 'Please add LHS Report first.', 'lhs_link' => $html]);
            }
            //$('.alert.alert-danger.print-error-msg').show();
            //var base_url = $('meta[name="base_url"]').attr('content');
            // var  Current_url = base_url+"/employee/lhs_report/"+ lead_id+"?status="+selected_val;
            //  $('ul.custom_text').html('<li class="error_list"><span class="tab">Please add  LHS Report first.</span><a href="'+Current_url+'" ><span class="tab">Click here to add Lhs Report</span></a></li>');


        } else {
            // Note::where('lead_id', $request->lead_id)->orderBy('updated_at', 'desc')
            // ->first()
            // ->update([
            //     'updated_at' => date('Y-m-d G:i:s')
            //  ]);
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
            Lead::where('id', $request->lead_id)->update(['status' => $request->status, 'is_notify' => 1, 'is_read' => 1]);
            $notification_count = Lead::where('is_notify', '!=', 0)->count();
            if ($request->status == 2) {
                $status = 'failed';
            } elseif ($request->status == 3) {
                $status = 'close';
            } else {
                $status = 'in_progress';
            }
            //dd($notification_count);
            return response()->json(['success' => 'Updated Successfully.', 'notification_count' => $notification_count, 'status' => $status]);
        }
    }
    public function search(Request $request)
    {
        $data = Lead::where('timezone', 'LIKE', '%$value%')->get();
        return view('leads.list')->with(['data' => $data]);
        // return response()->json($data);
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


}
