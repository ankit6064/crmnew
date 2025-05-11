@extends('layouts.admin')
@section('content')
<style>
   .form-control:disabled, .form-control[readonly] {
    background-color: #e9ecef !important; 
    border: 1px solid #ced4da !important;
    opacity: 0.7;
}
.card-body.lhs label {
         position: inherit;
        }

</style>
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Dashboard</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
            @if(Auth::user()->is_admin == 1)
            <?php  $last_url = redirect()->getUrlGenerator()->previous();  ?>
            <li class="breadcrumb-item"><a href="{{ $last_url }}">View Campaign Leads</a></li>
            @else
            <li class="breadcrumb-item"><a href="{{ url('leads') }}">Leads</a></li>
            @endif
            @if(Auth::user()->is_admin == 1)
            <?php  $last_url = redirect()->getUrlGenerator()->previous();  ?>
            <li class="breadcrumb-item active">View Lead</li>
            @else
            <li class="breadcrumb-item active">View Leads</li>
            @endif
        </ol>
    </div>
    <div>
        <!--<button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10"><i class="ti-settings text-white"></i></button>--->
    </div>
</div>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            @if (Session::has('success'))
            <div class="alert alert-success" role="alert">
                {{Session::get('success')}}
            </div>
            @elseif (Session::has('error'))
            <div class="alert alert-danger" role="alert">
                {{Session::get('error')}}
            </div>
            @endif
            <div class="card card-outline-info table-border-none">
                <div class="card-header">
                    <h4 class="m-b-0 text-white">View page</h4>
                </div>
                  <div class="button_edit">
                 
               
                        <a class="button_edit_anchor" href="{{ url('/lhs_report/edit', [$data['lead_id']]) }}">
                            <span class="label label-success">Edit Report</span>
                        </a>
           
                

                 <?php
                    $lhs = App\Models\LhsReport::where(['lead_id' => $data['lead_id']])->first();
                    $urls = '?employee_id='.request()->get('employee_id').'&campaign_id='.request()->get('campaign_id').'&date_from='.request()->get('date_from').'&date_to='.request()->get('date_to');
                 ?>
                    @if(!empty($lhs))
                        <a href="{{ url('/employee/export/' . $data['lead_id']. '/word_single_down').$urls }}" class="button_edit_anchor"><span class="label label-warning">
                                                            <i class="ti-download"> </i> Word</span>
                                                     </a>

                    @endif


                 </div>



                <div class="card-body">
                    <div class="form-body vw-lead">
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row ">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Contact's Name:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="prospect_first_name"
                                                value="{{ ucfirst($lead_info->prospect_first_name) .' '. ucfirst($lead_info->prospect_last_name)}}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Board Number:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="prospect_last_name"
                                                value="{{ $data->board_no }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Contact's Designation:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="email"
                                                value="{{ $lead_info->designation }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Direct Number:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="source_name"
                                                value="{{ $data->direct_no }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Company:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="contact_number_1"
                                                value="{{ $lead_info->company_name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Ext (if any):</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="contact_number_2"
                                                value="{{ $data->ext_if_any }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Industry:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="company_industry"
                                                value="{{ $lead_info->company_industry }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Cell Number:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="company_name"
                                                value="{{ $lead_info->contact_number_1 }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Employees:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="prospect_name"
                                                value="{{ $data->employees_strength }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Email:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="designation"
                                                value="{{ $lead_info->prospect_email }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Revenue:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="linkedin_address"
                                                value="{{ $data->revenue }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">EA Name:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="bussiness_function"
                                                value="{{ $data->ea_name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Address:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="designation_level"
                                                value="{{ $data->address }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">EA Phone Number:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="timezone"
                                                value="{{ $data->ea_phone_no }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">LinkedIn Profile:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="location"
                                                value="{{ $lead_info->linkedin_address }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">EA Email:</label><br>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="location"
                                                value="{{ $data->ea_email }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Prospect Level:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="location"
                                                value="{{ $data->prospects_level }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Website:</label><br>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="location"
                                                value="{{ $data->website }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Prospect Vertical:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="location"
                                                value="{{ $data->prospect_vertical }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Opt-in Status:</label><br>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="location"
                                                value="{{ $data->opt_in_status }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h4 class="m-b-0 text-white">Company Description</h4>
                </div>
                <div class="Heraeus1">{!! $data->company_desc !!}</div>





                <div class="card-header">
                    <h4 class="m-b-0 text-white">Lead Comments</h4>
                </div>
                <div class="card-body">
                    <div class="form-body vw-lead Manfred">
                        <div class="row p-t-20">
                            <div class="col-md-12 mb-4 show_lead">
                                <div class="row ">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Responsibilities:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">

                                     
                                            <div class="Heraeus1">{!!  $data->responsibilities !!}</div>
                                            <!-- <textarea class="form-control" type="text" name="prospect_first_name" value=""
                                                readonly="">{!! $data->responsibilities !!}</textarea> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4 show_lead">
                                <div class="row ">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Team Size:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">

                                            <div class="Heraeus1">{!! $data->team_size !!}</div>
                                            <!-- <textarea class="form-control" type="text" name="prospect_first_name" value=""
                                                readonly="">{{ strip_tags($data->team_size) }}</textarea> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4 show_lead">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Pain Areas:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">

                                            <div class="Heraeus1">{!! $data->pain_areas !!}</div>

                                          <!--   <textarea class="form-control" type="text" name="prospect_first_name" value=""
                                                readonly="">{{ strip_tags($data->pain_areas) }}</textarea> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4 show_lead">
                                <div class="row ">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Interest/New Initiatives:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <div class="Heraeus1">{!! $data->interest_new_initiatives !!}</div>
                                            <!-- <textarea class="form-control" type="text" name="prospect_first_name" value=""
                                                readonly="">{{ strip_tags($data->interest_new_initiatives) }}</textarea> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4 show_lead">
                                <div class="row ">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Budget:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <div class="Heraeus1">{!! $data->budget !!}</div>
                                            <!-- <textarea class="form-control" type="text" name="prospect_first_name" value=""
                                                readonly="">{{ $data->budget }}</textarea> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4 show_lead">
                                <div class="row ">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Defined Agenda:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <div class="Heraeus1">{!! $data->defined_agenda !!}</div>
                                            <!-- <textarea class="form-control" type="text" name="prospect_first_name" value=""
                                                readonly="">{{ strip_tags($data->defined_agenda) }}</textarea> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4 show_lead">
                                <div class="row ">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Call Notes:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <div class="Heraeus1">{!! $data->call_notes !!}</div>
                                            <!-- <textarea class="form-control" type="text" name="prospect_first_name" value=""
                                                readonly="">{{ strip_tags($data->call_notes) }}</textarea> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 mb-3 show_lead">
                                <div class="row">
                                    <!-- <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label labelstyle">Contact's Designation:</label>
                              </div>
                           </div> -->
                                    <div class="col-md-8">
                                        <div class="form-group">

                                            <textarea class="form-control" type="text" name="email" value=""
                                                readonly>Does the prospect wish to have a Face to Face meeting or teleconference?</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 show_lead">
                                <div class="row">
                                    <!-- <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label labelstyle">Direct Number:</label>
                              </div>
                           </div> -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <textarea class="form-control" type="text" name="source_name" value=""
                                                readonly>{{ $data->meeting_teleconference }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 show_lead">
                                <div class="row">
                                    <!-- <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label labelstyle">Contact's Designation:</label>
                              </div>
                           </div> -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <textarea class="form-control" type="text" name="email" value=""
                                                readonly>Is the contact the decision maker? If No, then who is?</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 show_lead">
                                <div class="row">
                                    <!-- <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label labelstyle">Contact's Designation:</label>
                              </div>
                           </div> -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <textarea class="form-control" type="text" name="email" value=""
                                                readonly>{{ $data->contact_decision_maker }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 show_lead">
                                <div class="row">
                                    <!-- <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label labelstyle">Contact's Designation:</label>
                              </div>
                           </div> -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <textarea class="form-control" type="text" name="email" value=""
                                                readonly>Who else would be the influencers in the decision making process?</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 show_lead">
                                <div class="row">
                                    <!-- <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label labelstyle">Contact's Designation:</label>
                              </div>
                           </div> -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <textarea class="form-control" type="text" name="email" value=""
                                                readonly>{{ $data->influencers_decision_making_process }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 show_lead">
                                <div class="row">
                                    <!-- <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label labelstyle">Contact's Designation:</label>
                              </div>
                           </div> -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <textarea class="form-control" type="text" name="email" value=""
                                                readonly>Is the Company already affiliated with any other similar services? If Yes, Name?</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 show_lead">
                                <div class="row">
                                    <!-- <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label labelstyle">Contact's Designation:</label>
                              </div>
                           </div> -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <textarea class="form-control" type="text" name="email" value=""
                                                readonly>{{ $data->company_already_affiliated }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h4 class="m-b-0 text-white">Meeting Information</h4>
                </div>
                <div class="card-body">
                    <div class="form-body vd-frm">
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row ">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Date 1:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <input type="text" value="{{ $data->meeting_date1 }}" class="form-control"
                                                readonly="" name="prospect_first_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row ">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Time 1:(24 Hours format)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <input type="text" value="{{ $data->meeting_time1 }} {{ $data->timezone_1 }}" class="form-control" readonly=""
                                                name="prospect_first_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row p-t-20">
                            <div class="col-md-6 show_lead">
                                <div class="row ">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Date 2:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <input type="text" value="{{ $data->meeting_date2 }}" class="form-control"
                                                readonly="" name="prospect_first_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 show_lead">
                                <div class="row ">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label labelstyle">Time 2:(24 Hours format)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <input type="text" value="{{ $data->meeting_time2 }} {{ $data->timezone_2 }}" class="form-control" readonly=""
                                                name="prospect_first_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
