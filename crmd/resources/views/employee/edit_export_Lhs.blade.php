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
         <li class="breadcrumb-item"><a href="{{ url('leads/closed') }}">Closed Leads</a></li>
         <li class="breadcrumb-item active">Edit LHS Report</li>
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
   <!-- Row -->
   <div class="row">
      <div class="col-lg-12">
         
         @if (Session::has('success'))
         <div class="alert alert-success" role="alert">
             {{Session::get('success')}}
         </div>
      @elseif (Session::has('error'))
         <div class="alert alert-danger" role="alert">
             {{Session::get('error')}}
         </div>
      @endif
         <div class="card card-outline-info">
            <div class="card-header">
               <h4 class="m-b-0 text-white">Edit LHS Report</h4>
            </div>
            <div class="card-body lhs">
               <form method='post' action="{{ route('employee.update_lhs_report') }}">
                  @csrf
                  <div class="form-body ">
                     <div class="row p-t-20">
                        <div class="col-md-6">
                           <div class="form-group">
                              <input type="hidden" id="lead_id" name='lead_id'class="form-control" placeholder="NA" value="{{ $lead_info->id}}">
                              <input type="hidden" id="lead_lhs_id" name='lead_lhs_id'class="form-control" placeholder="NA" value="{{ $data->id}}">
                             <?php if(isset($_GET["status"])){ 
                                echo $_GET["status"];
                                ?>
                              <input type="hidden" id="lead_status" name='status'class="form-control" placeholder="NA" value="{{ $data->id}}">
                              <?php  } ?>

                              <label class="control-label read-only-label">Contact's Name:</label>
                              <input type="text" id="prospect_first_name" name='prospect_first_name'class="form-control read-only-input"  placeholder="Enter Contact Name" value="{{ $lead_info->prospect_first_name .' '. $lead_info->prospect_last_name}}" >
                              @if($errors->has('prospect_first_name'))
                              <div class="alert alert-danger">{{ $errors->first('prospect_first_name') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Board Number:</label>
                              <input type="text" id="board_no" name='board_no' class="form-control form-control-danger" placeholder="Enter Board Number" value="{{ $data->board_no }}">
                              @if($errors->has('board_no'))
                              <div class="alert alert-danger">{{ $errors->first('board_no') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label read-only-label">Contact's Designation:</label>
                              <input type="text" id="designation" name='designation'class="form-control read-only-input"  placeholder="Enter Contact's Designation" value="{{ $lead_info->designation }}">
                              @if($errors->has('designation'))
                              <div class="alert alert-danger">{{ $errors->first('designation') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Direct Number:</label>
                              <input type="text" id="direct_no" name='direct_no'class="form-control" placeholder="Enter Direct Number" value="{{ $data->direct_no }}">
                              @if($errors->has('direct_no'))
                              <div class="alert alert-danger">{{ $errors->first('direct_no') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label read-only-label">Company:</label>
                              <input type="text" id="company_name" name='company_name'class="form-control"  placeholder="Enter Company" value="{{ $lead_info->company_name }}">
                              @if($errors->has('company_name'))
                              <div class="alert alert-danger">{{ $errors->first('company_name') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Ext (if any):</label>
                              <input type="text" id="ext_if_any" name='ext_if_any'class="form-control" placeholder="Enter Ext (if any)" value="{{ $data->ext_if_any }}">
                              @if($errors->has('ext_if_any'))
                              <div class="alert alert-danger">{{ $errors->first('ext_if_any') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label read-only-label">Industry:</label>
                              <input type="text" id="company_industry" name='company_industry'class="form-control" placeholder="Enter Industry"  value="{{ $lead_info->company_industry }}">
                              @if($errors->has('company_industry'))
                              <div class="alert alert-danger">{{ $errors->first('industry') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label read-only-label">Cell Number:</label>
                              <input type="text" id="contact_number_1" name='contact_number_1'class="form-control" placeholder="Enter Cell Number"  value="{{ $lead_info->contact_number_2 }}">
                              @if($errors->has('contact_number_1'))
                              <div class="alert alert-danger">{{ $errors->first('contact_number_1') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Employees:</label>
                              <input type="text" id="employees_strength" name='employees_strength'class="form-control" placeholder="Enter Employees" value="{{ $data->employees_strength  }}">
                              @if($errors->has('employees_strength'))
                              <div class="alert alert-danger">{{ $errors->first('employees_strength') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label read-only-label">Email</label>
                              <input type="text" id="prospect_email" name='prospect_email' class="form-control" placeholder="Enter Email"  value="{{ $lead_info->prospect_email }}">
                              @if($errors->has('prospect_email'))
                              <div class="alert alert-danger">{{ $errors->first('prospect_email') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Revenue:</label>
                              <input type="text" id="revenue" name='revenue'class="form-control" placeholder="Enter Revenue" value="{{ $data->revenue }}">
                              @if($errors->has('revenue'))
                              <div class="alert alert-danger">{{ $errors->first('revenue') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">EA Name:</label>
                              <input type="text" id="ea_name" name='ea_name'class="form-control" placeholder="Enter EA Name" value="{{ $data->ea_name }}">
                              @if($errors->has('ea_name'))
                              <div class="alert alert-danger">{{ $errors->first('ea_name') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Address</label>
                              <input type="text" id="address" name='address' class="form-control" placeholder="Enter Address" value="{{ $data->address }}">
                              @if($errors->has('address'))
                              <div class="alert alert-danger">{{ $errors->first('address') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">EA Phone Number:</label>
                              <input type="text" id="ea_phone_no" name='ea_phone_no'class="form-control" placeholder="Enter EA Phone Number" value="{{ $data->ea_phone_no }}">
                              @if($errors->has('ea_phone_no'))
                              <div class="alert alert-danger">{{ $errors->first('ea_phone_no') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label read-only-label">LinkedIn Profile:</label>
                              <input type="text" id="linkedin_address" name='linkedin_address'class="form-control"  placeholder="Enter EA LinkedIn Profile" value="{{ $lead_info->linkedin_address }}">
                              @if($errors->has('linkedin_address'))
                              <div class="alert alert-danger">{{ $errors->first('linkedin_address') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">EA Email:</label>
                              <input type="text" id="ea_email" name='ea_email' class="form-control" placeholder="Enter EA Email:" value="{{ $data->ea_email }}">
                              @if($errors->has('ea_email'))
                              <div class="alert alert-danger">{{ $errors->first('ea_email') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Prospect Level:</label>
                              <input type="text" id="prospects_level" name='prospects_level'class="form-control" placeholder="Enter Prospect Level" value="{{ $data->prospects_level }}">
                              @if($errors->has('prospects_level'))
                              <div class="alert alert-danger">{{ $errors->first('prospects_level') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Website:</label>
                              <input type="text" id="website" name='website'class="form-control" placeholder="Enter Website" value="{{ $data->website }}">
                              @if($errors->has('website'))
                              <div class="alert alert-danger">{{ $errors->first('website') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Prospect Vertical:</label>
                              <input type="text" id="prospect_vertical" name='prospect_vertical'class="form-control" placeholder="Enter Prospect Vertical" value="{{ $data->prospect_vertical }}">
                              @if($errors->has('prospect_vertical'))
                              <div class="alert alert-danger">{{ $errors->first('prospect_vertical') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Opt-in Status:</label>
                              <input type="text" id="opt_in_status" name='opt_in_status'class="form-control" placeholder="Enter Opt-in Status" value="{{ $data->opt_in_status }}">
                              @if($errors->has('opt_in_status'))
                              <div class="alert alert-danger">{{ $errors->first('opt_in_status') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-12">
                           <div class="form-group">
                              <label class="control-label">Company Description</label>
                              <textarea placeholder="Enter Company Description" id="company_desc" name='company_desc' class="form-control company_desc">{{ $data->company_desc }}</textarea>
                              @if($errors->has('company_desc'))
                              <div class="alert alert-danger">{{ $errors->first('company_desc') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Responsibilities:</label>
                              <textarea placeholder="Enter Responsibilities" id="responsibilities" name='responsibilities' class="form-control">{{ $data->responsibilities }}</textarea>
                              @if($errors->has('responsibilities'))
                              <div class="alert alert-danger">{{ $errors->first('responsibilities') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Team Size:</label>
                              <textarea placeholder="Enter Team Size" id="team_size" name='team_size' class="form-control">{{ $data->team_size }}</textarea>
                              @if($errors->has('team_size'))
                              <div class="alert alert-danger">{{ $errors->first('team_size') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Pain Areas:</label>
                              <textarea placeholder="Enter Pain Areas" id="pain_areas" name='pain_areas' class="form-control">{{ $data->pain_areas }}</textarea>
                              @if($errors->has('pain_areas'))
                              <div class="alert alert-danger">{{ $errors->first('pain_areas') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Interest/New Initiatives:</label>
                              <textarea placeholder="Enter Interest/New Initiatives" id="interest_new_initiatives" name='interest_new_initiatives' class="form-control">{{ $data->interest_new_initiatives }}</textarea>
                              @if($errors->has('interest_new_initiatives'))
                              <div class="alert alert-danger">{{ $errors->first('interest_new_initiatives') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Budget:</label>
                              <textarea placeholder="Enter Budget" id="budget" name='budget' class="form-control">{{ $data->budget }}</textarea>
                              @if($errors->has('budget'))
                              <div class="alert alert-danger">{{ $errors->first('budget') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Defined Agenda:</label>
                              <textarea placeholder="Enter Defined Agenda" id="defined_agenda" name='defined_agenda' class="form-control">{{ $data->defined_agenda }}</textarea>
                              @if($errors->has('defined_agenda'))
                              <div class="alert alert-danger">{{ $errors->first('defined_agenda') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-12">
                           <div class="form-group">
                              <label class="control-label">Call Notes:</label>
                              <textarea placeholder="Enter Call Notes" id="call_notes" name='call_notes' class="form-control editor">{{ $data->call_notes }}</textarea>
                              @if($errors->has('call_notes'))
                              <div class="alert alert-danger">{{ $errors->first('call_notes') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label"></label>
                              <h5>Does the prospect wish to have a Face to Face meeting or teleconference?</h5>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label"></label>
                              {{-- <input type="text" id="meeting_teleconference" name='meeting_teleconference'class="form-control" placeholder="Teleconference" value="{{ $data->meeting_teleconference }}">
                              @if($errors->has('meeting_teleconference'))
                              <div class="alert alert-danger">{{ $errors->first('meeting_teleconference') }}</div>
                              @endif --}}
                              <select id="meeting_teleconference" name='meeting_teleconference'class="form-control">
                                 <option vlaue="">Select Any Option</option> 
                                 <option vlaue="Face to Face meeting"{{ $data->meeting_teleconference == 'Face to Face meeting' ? 'selected="selected"' : '' }}>  Face to Face meeting </option> 
                                 <option vlaue="Teleconference"{{ $data->meeting_teleconference == 'Teleconference' ? 'selected="selected"' : '' }}>  Teleconference </option> 
                              </select>
                              @if($errors->has('meeting_teleconference'))
                              <div class="alert alert-danger">{{ $errors->first('meeting_teleconference') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label"></label>
                              <h5>Is the contact the decision maker? If No, then who is?</h5>
                              {{-- <input type="text" id="prospect_first_name" name='prospect_first_name'class="form-control" placeholder="" value="{{ old('prospect_first_name') }}">
                              @if($errors->has('prospect_first_name'))
                              <div class="alert alert-danger">{{ $errors->first('prospect_first_name') }}</div>
                              @endif --}}
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label"></label>
                              <select id="contact_decision_maker" name='contact_decision_maker'class="form-control">
                                 <option vlaue="">Select Any Option</option> 
                                 <option vlaue="Yes" {{ $data->contact_decision_maker == 'Yes' ? 'selected="selected"' : '' }}>Yes</option> 
                                 <option vlaue="No"{{ $data->contact_decision_maker == 'No' ? 'selected="selected"' : '' }}>No</option> 
                                 
                              </select>
                              {{-- <input type="text" id="contact_decision_maker" name='contact_decision_maker'class="form-control" placeholder="Yes/No" value="{{ $data->contact_decision_maker }}">
                              @if($errors->has('contact_decision_maker'))
                              <div class="alert alert-danger">{{ $errors->first('contact_decision_maker') }}</div>
                              @endif --}}
                              @if($errors->has('contact_decision_maker'))
                              <div class="alert alert-danger">{{ $errors->first('contact_decision_maker') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label"></label>
                              <h5>Who else would be the influencers in the decision making process?</h5>
                              {{-- <input type="text" id="prospect_first_name" name='prospect_first_name'class="form-control" placeholder="Who else would be the influencers in the decision making process?" value="{{ old('prospect_first_name') }}">
                              @if($errors->has('prospect_first_name'))
                              <div class="alert alert-danger">{{ $errors->first('prospect_first_name') }}</div>
                              @endif --}}
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label"></label>
                              <input type="text" id="influencers_decision_making_process" name='influencers_decision_making_process'class="form-control" placeholder="NA" value="{{ $data->influencers_decision_making_process }}">
                              @if($errors->has('influencers_decision_making_process'))
                              <div class="alert alert-danger">{{ $errors->first('influencers_decision_making_process') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label"></label>
                              <h5>Is the Company already affiliated with any other similar services? If Yes, Name?</h5>
                              {{-- <input type="text" id="prospect_first_name" name='prospect_first_name'class="form-control" placeholder="Is the Company already affiliated with any other similar services? If Yes, Name?" value="{{ old('prospect_first_name') }}">
                              @if($errors->has('prospect_first_name'))
                              <div class="alert alert-danger">{{ $errors->first('prospect_first_name') }}</div>
                              @endif --}}
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label"></label>
                              <input type="text" id="company_already_affiliated" name='company_already_affiliated'class="form-control" placeholder="NA" value="{{ $data->company_already_affiliated }}">
                              @if($errors->has('company_already_affiliated'))
                              <div class="alert alert-danger">{{ $errors->first('company_already_affiliated') }}</div>
                              @endif
                           </div>
                        </div>
                     </div>
                     {{-- <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Date 1:</label>
                              <input type="date" class="form-control" placeholder="23-Dec-21" name="meeting_date1" value="{{ $data->meeting_date1 }}" >
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Date 1:</label>
                              <input type="date" class="form-control" placeholder="23-Dec-21" name="meeting_date2" value="{{ $data->meeting_date2 }}" >
                           </div>
                        </div>
                     </div> --}}
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="control-label">Date 1:</label>
                              <input type="date" class="form-control date" placeholder="23-Dec-21" name="meeting_date1" value="{{ $data->meeting_date1 }}" >
                              @if($errors->has('meeting_date1'))
                              <div class="alert alert-danger">{{ $errors->first('meeting_date1') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="control-label">Time 1:</label>
                              <input type="time" class="form-control" id="appt" name="meeting_time1" value="{{ $data->meeting_time1 }}">                           </div>
                              @if($errors->has('meeting_time1'))
                              <div class="alert alert-danger">{{ $errors->first('meeting_time1') }}</div>
                              @endif
                           </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label"> TimeZone 1:</label>
                              <?php $tzlist = DateTimeZone::listAbbreviations();
                              $tzzlistrr = array_keys($tzlist);
                              ?>
                              <select id="time_zone" name='timezone_1'class="form-control">
                                 <option vlaue="">Select An Timezone</option> 
                                 <?php $i=0; ?>
                                 @foreach ($tzzlistrr as $tzzlistrrrr)
                                      
                                 <?php  $tzzlist = array_keys($tzlist);  ?>
                                 <option vlaue={{ strtoupper($tzzlist[$i]) }} {{ $data->timezone_1 == strtoupper($tzzlist[$i]) ? 'selected="selected"' : '' }}> {{ strtoupper($tzzlist[$i]) }}</option> 
                                 <?php $i++;?>
                                 @endforeach
                              </select>
                              @if($errors->has('timezone_1'))
                              <div class="alert alert-danger">{{ $errors->first('timezone_1') }}</div>
                              @endif
                           </div>
                        </div>
                   
                     
                     </div>
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="control-label">Date 2:</label>
                              <input type="date" class="form-control date" placeholder="23-Dec-21" name="meeting_date2" value="{{ $data->meeting_date2 }}" >
                              @if($errors->has('meeting_date2'))
                              <div class="alert alert-danger">{{ $errors->first('meeting_date2') }}</div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="control-label">Time 1:</label>
                              <input type="time" class="form-control" id="appt" name="meeting_time2" value="{{ $data->meeting_time2 }}">                           </div>
                              @if($errors->has('meeting_time2'))
                              <div class="alert alert-danger">{{ $errors->first('meeting_time2') }}</div>
                              @endif
                           </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="control-label">TimeZone 2:</label>
                           <?php $tzlist = DateTimeZone::listAbbreviations();
                           $tzzlistrr = array_keys($tzlist);
                           ?>
                           <select id="time_zone" name='timezone_2'class="form-control">
                              <option vlaue="">Select An Timezone</option> 
                              <?php $i=0; ?>
                              @foreach ($tzzlistrr as $tzzlistrrrr)
                                   
                              <?php  $tzzlist = array_keys($tzlist);  ?>
                              <option vlaue={{ strtoupper($tzzlist[$i]) }} {{ $data->timezone_2 == strtoupper($tzzlist[$i]) ? 'selected="selected"' : '' }}> {{ strtoupper($tzzlist[$i]) }}</option> 
                              <?php $i++;?>
                              @endforeach
                           </select>
                           @if($errors->has('opt_in_status'))
                           <div class="alert alert-danger">{{ $errors->first('opt_in_status') }}</div>
                           @endif
                        </div>
                     </div>
                     
                  </div>
                  @if(Auth::User()->is_admin != 1)
                  <div class="form-actions">
                     <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                     <input type="reset" class="btn btn-inverse" value="Cancel" />
                     <a href="{{ url('/leads') }}"><button type="button" class="btn btn-info">Back</button></a>

                  </div>
                  @else
                  <div class="form-actions">
                     <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                     <input type="reset" class="btn btn-inverse" value="Cancel" />
                     {{-- <a href="{{ url('/leads') }}"><button type="button" class="btn btn-info">Back</button></a> --}}
                     <a href="{{ url('leads/closed') }}"><button type="button" class="btn btn-info">Back</button></a>

                  </div>
                  @endif
               </form>
            </div>
         </div>
      </div>
   </div>
   <!-- Row -->
   <!-- ============================================================== -->
   <!-- End PAge Content -->
   <!-- ============================================================== -->
   <!-- ============================================================== -->
   <!-- Right sidebar -->
   <!-- ============================================================== -->
   <!-- .right-sidebar -->
   <div class="right-sidebar">
      <div class="slimscrollright">
         <div class="rpanel-title"> Service Panel <span><i class="ti-close right-side-toggle"></i></span> </div>
         <div class="r-panel-body">
            <ul id="themecolors" class="m-t-20">
               <li><b>With Light sidebar</b></li>
               <li><a href="javascript:void(0)" data-theme="default" class="default-theme">1</a></li>
               <li><a href="javascript:void(0)" data-theme="green" class="green-theme">2</a></li>
               <li><a href="javascript:void(0)" data-theme="red" class="red-theme">3</a></li>
               <li><a href="javascript:void(0)" data-theme="blue" class="blue-theme working">4</a></li>
               <li><a href="javascript:void(0)" data-theme="purple" class="purple-theme">5</a></li>
               <li><a href="javascript:void(0)" data-theme="megna" class="megna-theme">6</a></li>
               <li class="d-block m-t-30"><b>With Dark sidebar</b></li>
               <li><a href="javascript:void(0)" data-theme="default-dark" class="default-dark-theme">7</a></li>
               <li><a href="javascript:void(0)" data-theme="green-dark" class="green-dark-theme">8</a></li>
               <li><a href="javascript:void(0)" data-theme="red-dark" class="red-dark-theme">9</a></li>
               <li><a href="javascript:void(0)" data-theme="blue-dark" class="blue-dark-theme">10</a></li>
               <li><a href="javascript:void(0)" data-theme="purple-dark" class="purple-dark-theme">11</a></li>
               <li><a href="javascript:void(0)" data-theme="megna-dark" class="megna-dark-theme ">12</a></li>
            </ul>
            <ul class="m-t-20 chatonline">
               <li><b>Chat option</b></li>
               <li>
                  <a href="javascript:void(0)"><img src="../assets/images/users/1.jpg" alt="user-img" class="img-circle"> <span>Varun Dhavan <small class="text-success">online</small></span></a>
               </li>
               <li>
                  <a href="javascript:void(0)"><img src="../assets/images/users/2.jpg" alt="user-img" class="img-circle"> <span>Genelia Deshmukh <small class="text-warning">Away</small></span></a>
               </li>
               <li>
                  <a href="javascript:void(0)"><img src="../assets/images/users/3.jpg" alt="user-img" class="img-circle"> <span>Ritesh Deshmukh <small class="text-danger">Busy</small></span></a>
               </li>
               <li>
                  <a href="javascript:void(0)"><img src="../assets/images/users/4.jpg" alt="user-img" class="img-circle"> <span>Arijit Sinh <small class="text-muted">Offline</small></span></a>
               </li>
               <li>
                  <a href="javascript:void(0)"><img src="../assets/images/users/5.jpg" alt="user-img" class="img-circle"> <span>Govinda Star <small class="text-success">online</small></span></a>
               </li>
               <li>
                  <a href="javascript:void(0)"><img src="../assets/images/users/6.jpg" alt="user-img" class="img-circle"> <span>John Abraham<small class="text-success">online</small></span></a>
               </li>
               <li>
                  <a href="javascript:void(0)"><img src="../assets/images/users/7.jpg" alt="user-img" class="img-circle"> <span>Hritik Roshan<small class="text-success">online</small></span></a>
               </li>
               <li>
                  <a href="javascript:void(0)"><img src="../assets/images/users/8.jpg" alt="user-img" class="img-circle"> <span>Pwandeep rajan <small class="text-success">online</small></span></a>
               </li>
            </ul>
         </div>
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Right sidebar -->
   <!-- ============================================================== -->
</div>

@endsection