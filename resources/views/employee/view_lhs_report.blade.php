@extends('layouts.admin')
@section('content')

<style>

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

<h2>View Lead</h2>

@php

$leadId = $data->lead_id ?? null;

$lhs = null;

if($leadId){
$lhs = App\Models\LhsReport::where('lead_id',$leadId)->first();
}

$urls = '?employee_id='.request()->get('employee_id')
.'&campaign_id='.request()->get('campaign_id')
.'&date_from='.request()->get('date_from')
.'&date_to='.request()->get('date_to');

@endphp


<div class="lead-actions">

@if($leadId)
<a href="{{ url('/lhs_report/edit/'.$leadId) }}" class="btn-edit">
<i class="fa fa-edit"></i> Edit Report
</a>
@endif


@if(!empty($lhs))
<a href="{{ url('/employee/export/'.$leadId.'/word_single_down').$urls }}" class="btn-download">
<i class="fa fa-download"></i> Download Word
</a>
@endif


<a href="javascript:void(0);" onclick="goBack()" class="btn-back">
    <i class="fa fa-arrow-left"></i> Back
</a>

</div>


<!-- Row 1 -->
<div class="form-row">
<div class="form-group">
<label>Contact Name</label>
<input type="text"
value="{{ ucfirst($lead_info->prospect_first_name).' '.ucfirst($lead_info->prospect_last_name) }}"
readonly>
</div>

<div class="form-group">
<label>Board Number</label>
<input type="text"
value="{{ $data->board_no }}"
readonly>
</div>
</div>


<!-- Row 2 -->
<div class="form-row">
<div class="form-group">
<label>Designation</label>
<input type="text"
value="{{ $lead_info->designation }}"
readonly>
</div>

<div class="form-group">
<label>Direct Number</label>
<input type="text"
value="{{ $data->direct_no }}"
readonly>
</div>
</div>


<!-- Row 3 -->
<div class="form-row">
<div class="form-group">
<label>Company</label>
<input type="text"
value="{{ $lead_info->company_name }}"
readonly>
</div>

<div class="form-group">
<label>Ext (if any)</label>
<input type="text"
value="{{ $data->ext_if_any }}"
readonly>
</div>
</div>


<!-- Row 4 -->
<div class="form-row">
<div class="form-group">
<label>Industry</label>
<input type="text"
value="{{ $lead_info->company_industry }}"
readonly>
</div>

<div class="form-group">
<label>Cell Number</label>
<input type="text"
value="{{ $lead_info->contact_number_1 }}"
readonly>
</div>
</div>


<!-- Row 5 -->
<div class="form-row">
<div class="form-group">
<label>Email</label>
<input type="text"
value="{{ $lead_info->prospect_email }}"
readonly>
</div>

<div class="form-group">
<label>LinkedIn</label>
<input type="text"
value="{{ $lead_info->linkedin_address }}"
readonly>
</div>
</div>


<!-- Row 6 -->
<div class="form-row">
<div class="form-group">
<label>Employees</label>
<input type="text"
value="{{ $data->employees_strength }}"
readonly>
</div>

<div class="form-group">
<label>Revenue</label>
<input type="text"
value="{{ $data->revenue }}"
readonly>
</div>
</div>


<!-- Row 7 -->
<div class="form-row">
<div class="form-group">
<label>Address</label>
<input type="text"
value="{{ $data->address }}"
readonly>
</div>

<div class="form-group">
<label>Website</label>
<input type="text"
value="{{ $data->website }}"
readonly>
</div>
</div>



<h3 style="margin-top:30px">Company Description</h3>

<div class="form-group">
<div class="description-box">
{!! $data->company_desc !!}
</div>
</div>



<h3 style="margin-top:30px">Lead Comments</h3>


<div class="form-group">
<label>Responsibilities</label>
<div class="description-box">{!! $data->responsibilities !!}</div>
</div>


<div class="form-group">
<label>Team Size</label>
<div class="description-box">{!! $data->team_size !!}</div>
</div>


<div class="form-group">
<label>Pain Areas</label>
<div class="description-box">{!! $data->pain_areas !!}</div>
</div>


<div class="form-group">
<label>Interest / New Initiatives</label>
<div class="description-box">{!! $data->interest_new_initiatives !!}</div>
</div>


<div class="form-group">
<label>Budget</label>
<div class="description-box">{!! $data->budget !!}</div>
</div>


<div class="form-group">
<label>Defined Agenda</label>
<div class="description-box">{!! $data->defined_agenda !!}</div>
</div>


<div class="form-group">
<label>Call Notes</label>
<div class="description-box">{!! $data->call_notes !!}</div>
</div>


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