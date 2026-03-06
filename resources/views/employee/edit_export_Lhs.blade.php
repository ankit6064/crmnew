@extends('layouts.admin')
@section('content')
<style>
.form-group textarea{
    width: 100%;
    min-height: 90px;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    line-height: 1.4;
    resize: vertical;
    background: #fff;
}

.form-group textarea:focus{
    border-color: #4a90e2;
    outline: none;
    box-shadow: 0 0 3px rgba(74,144,226,0.2);
}
.description-box{
border:1px solid #ddd;
padding:15px;
border-radius:6px;
background:#fafafa;
min-height:60px;
}

/* Action buttons */
.lead-actions{
display:flex;
gap:10px;
margin-bottom:20px;
justify-content:flex-end;
}

.btn-edit{
background:#28a745;
color:#fff;
padding:8px 15px;
border-radius:6px;
text-decoration:none;
}

.btn-download{
background:#ffc107;
color:#000;
padding:8px 15px;
border-radius:6px;
text-decoration:none;
}

.btn-back{
background:#6c757d;
color:#fff;
padding:8px 15px;
border-radius:6px;
text-decoration:none;
}

.lead-actions a:hover{
opacity:0.9;
}
</style>


<div class="main-right addsubmanager addlead">
<div class="right-side add-sub">
<div class="graph">

<h2>Edit LHS Report</h2>

@if (Session::has('success'))
<div class="alert alert-success">
{{ Session::get('success') }}
</div>
@endif

@if (Session::has('error'))
<div class="alert alert-danger">
{{ Session::get('error') }}
</div>
@endif


<form method="post" action="{{ route('employee.update_lhs_report') }}" id="lhsForm">
@csrf

<input type="hidden" name="lead_id" value="{{ $lead_info->id }}">
<input type="hidden" name="lead_lhs_id" value="{{ $data->id }}">


<!-- ROW 1 -->
<div class="form-row">

<div class="form-group">
<label>Contact Name</label>
<input type="text" id="prospect_first_name" name="prospect_first_name"
value="{{ $lead_info->prospect_first_name .' '. $lead_info->prospect_last_name }}"
readonly>
<small class="text-danger error" id="prospect_first_name_error"></small>
</div>

<div class="form-group">
<label>Board Number</label>
<input type="text" id="board_no" name="board_no"
placeholder="Enter Board Number"
value="{{ $data->board_no }}">
<small class="text-danger error" id="board_no_error"></small>
</div>

</div>


<!-- ROW 2 -->
<div class="form-row">

<div class="form-group">
<label>Designation</label>
<input type="text" id="designation" name="designation"
value="{{ $lead_info->designation }}" readonly>
</div>

<div class="form-group">
<label>Direct Number</label>
<input type="text" id="direct_no" name="direct_no"
placeholder="Enter Direct Number"
value="{{ $data->direct_no }}">
</div>

</div>


<!-- ROW 3 -->
<div class="form-row">

<div class="form-group">
<label>Company</label>
<input type="text" id="company_name" name="company_name"
value="{{ $lead_info->company_name }}">
</div>

<div class="form-group">
<label>Ext (if any)</label>
<input type="text" id="ext_if_any" name="ext_if_any"
value="{{ $data->ext_if_any }}">
</div>

</div>


<!-- ROW 4 -->
<div class="form-row">

<div class="form-group">
<label>Industry</label>
<input type="text" id="company_industry" name="company_industry"
value="{{ $lead_info->company_industry }}">
</div>

<div class="form-group">
<label>Cell Number</label>
<input type="text" id="contact_number_1" name="contact_number_1"
value="{{ $lead_info->contact_number_2 }}">
</div>

</div>


<!-- ROW 5 -->
<div class="form-row">

<div class="form-group">
<label>Employees</label>
<input type="text" id="employees_strength" name="employees_strength"
value="{{ $data->employees_strength }}">
</div>

<div class="form-group">
<label>Email</label>
<input type="text" id="prospect_email" name="prospect_email"
value="{{ $lead_info->prospect_email }}">
</div>

</div>


<!-- ROW 6 -->
<div class="form-row">

<div class="form-group">
<label>Revenue</label>
<input type="text" id="revenue" name="revenue"
value="{{ $data->revenue }}">
</div>

<div class="form-group">
<label>EA Name</label>
<input type="text" id="ea_name" name="ea_name"
value="{{ $data->ea_name }}">
</div>

</div>


<!-- ROW 7 -->
<div class="form-row">

<div class="form-group">
<label>Address</label>
<input type="text" id="address" name="address"
value="{{ $data->address }}">
</div>

<div class="form-group">
<label>EA Phone Number</label>
<input type="text" id="ea_phone_no" name="ea_phone_no"
value="{{ $data->ea_phone_no }}">
</div>

</div>


<!-- ROW 8 -->
<div class="form-row">

<div class="form-group">
<label>LinkedIn Profile</label>
<input type="text" id="linkedin_address" name="linkedin_address"
value="{{ $lead_info->linkedin_address }}">
</div>

<div class="form-group">
<label>EA Email</label>
<input type="text" id="ea_email" name="ea_email"
value="{{ $data->ea_email }}">
</div>

</div>


<!-- ROW 9 -->
<div class="form-row">

<div class="form-group">
<label>Prospect Level</label>
<input type="text" id="prospects_level" name="prospects_level"
value="{{ $data->prospects_level }}">
</div>

<div class="form-group">
<label>Website</label>
<input type="text" id="website" name="website"
value="{{ $data->website }}">
</div>

</div>


<!-- ROW 10 -->
<div class="form-row">

<div class="form-group">
<label>Prospect Vertical</label>
<input type="text" id="prospect_vertical" name="prospect_vertical"
value="{{ $data->prospect_vertical }}">
</div>

<div class="form-group">
<label>Opt-in Status</label>
<input type="text" id="opt_in_status" name="opt_in_status"
value="{{ $data->opt_in_status }}">
</div>

</div>


<!-- TEXTAREA SECTION -->

<div class="form-row">
<div class="form-group" style="width:100%">
<label>Company Description</label>
<textarea name="company_desc">{{ $data->company_desc }}</textarea>
</div>
</div>


<div class="form-row">
<div class="form-group">
<label>Responsibilities</label>
<textarea name="responsibilities">{{ $data->responsibilities }}</textarea>
</div>

<div class="form-group">
<label>Team Size</label>
<textarea name="team_size">{{ $data->team_size }}</textarea>
</div>
</div>


<div class="form-row">
<div class="form-group">
<label>Pain Areas</label>
<textarea name="pain_areas">{{ $data->pain_areas }}</textarea>
</div>

<div class="form-group">
<label>Interest / New Initiatives</label>
<textarea name="interest_new_initiatives">{{ $data->interest_new_initiatives }}</textarea>
</div>
</div>


<div class="form-row">
<div class="form-group">
<label>Budget</label>
<textarea name="budget">{{ $data->budget }}</textarea>
</div>

<div class="form-group">
<label>Defined Agenda</label>
<textarea name="defined_agenda">{{ $data->defined_agenda }}</textarea>
</div>
</div>


<div class="form-row">
<div class="form-group" style="width:100%">
<label>Call Notes</label>
<textarea name="call_notes">{{ $data->call_notes }}</textarea>
</div>
</div>


<!-- MEETING OPTION -->
<div class="form-row">

<div class="form-group">
<label>Meeting Preference</label>
<select name="meeting_teleconference">
<option value="">Select Option</option>

<option value="Face to Face meeting"
{{ $data->meeting_teleconference == 'Face to Face meeting' ? 'selected' : '' }}>
Face to Face meeting
</option>

<option value="Teleconference"
{{ $data->meeting_teleconference == 'Teleconference' ? 'selected' : '' }}>
Teleconference
</option>

</select>
</div>


<div class="form-group">
<label>Is Contact Decision Maker?</label>

<select name="contact_decision_maker">

<option value="">Select</option>

<option value="Yes"
{{ $data->contact_decision_maker == 'Yes' ? 'selected' : '' }}>
Yes
</option>

<option value="No"
{{ $data->contact_decision_maker == 'No' ? 'selected' : '' }}>
No
</option>

</select>

</div>

</div>


<!-- BUTTONS -->
<div class="btn-group">

<button type="submit" class="btn btn-save">
Update
</button>


<button type="button" class="btn btn-back" onclick="goBack()">
Back
</button>

</div>


</form>

</div>
</div>
</div>
<script>
function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = "{{ url('leads/closed') }}";
    }
}
</script>
@endsection