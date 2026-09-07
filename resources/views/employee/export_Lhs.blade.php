@extends('layouts.admin')

@section('content')
<style>
    /* Styling for read-only fields to match new design aesthetics */
    .form-control:disabled,
    .form-control[readonly] {
        background-color: #f4f4f4 !important;
        border: 1px solid #ddd !important;
        color: #666;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        justify-content: flex-start;
    }

    .section-title {
        margin: 30px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
        font-size: 1.2rem;
        color: #333;
    }

/* Fixed Textarea Design to match New Design System */
.form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: #fff;
        font-family: inherit;
        resize: vertical; /* Allows user to adjust height but keeps width stable */
    }

    /* Full width container for specific textareas */
    .form-group.full-width {
        flex: 0 0 100%;
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

    /* jQuery validation error styling */
    input.error,
    textarea.error,
    select.error {
        border: 1px solid #dc3545 !important;
    }
    label.error {
        color: #dc3545 !important;
        font-size: 13px !important;
        font-weight: normal !important;
        margin-top: 5px !important;
        display: block !important;
    }
</style>

<?php
// Phone Number Parsing Logic
$boardNumber = '';
if (isset($data->contact_number_1) && !empty($data->contact_number_1)) {
    $board_no = $data->contact_number_1;
    $boardNumber = (strpos($board_no, ' , ') > -1 || strpos($board_no, '/') > -1) 
        ? explode(strpos($board_no, '/') > -1 ? '/' : ',', $board_no)[0] 
        : substr($board_no, 0, 15);
}

$cellNumber = '';
if (isset($data->contact_number_2) && !empty($data->contact_number_2)) {
    $cell_no = $data->contact_number_2;
    $cellNumber = (strpos($cell_no, ',') > -1 || strpos($cell_no, '/') > -1) 
        ? explode(strpos($cell_no, '/') > -1 ? '/' : ',', $cell_no)[0] 
        : substr($cell_no, 0, 15);
}
?>

<div class="main-right addsubmanager addlead">
    <div class="right-side add-sub">
        <div class="graph">
            <h2>Add LHS Report</h2>

            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @elseif (Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif

            <form method='post' action="{{ url('lhs_report_save') }}" id="lhsReportForm" enctype="multipart/form-data">
                @csrf
                
                <input type="hidden" name='lead_id' value="{{ $data->id }}">
                @if(isset($_GET["status"]))
                    <input type="hidden" name='status' value="{{ intval($_GET['status']) }}">
                @endif
                <input type="hidden" name='previous_url' value="{{ URL::previous() }}">

                <div class="form-row">
                    <div class="form-group">
                        <label>Contact's Name</label>
                        <input type="text" name='prospect_first_name' 
                            value="{{ $data->prospect_first_name . ' ' . $data->prospect_last_name }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Board Number</label>
                        <input type="text" name='board_no' value="{{ $boardNumber }}" placeholder="Enter Board Number">
                        @error('board_no') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Contact's Designation</label>
                        <input type="text" name='designation' value="{{ $data->designation }}" placeholder="Enter Designation">
                        @error('designation') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Direct Number</label>
                        <input type="text" name='direct_no' value="{{ old('direct_no') }}" placeholder="Enter Direct Number">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Company</label>
                        <input type="text" name='company_name' value="{{ $data->company_name }}" placeholder="Enter Company">
                    </div>
                    <div class="form-group">
                        <label>Ext (if any)</label>
                        <input type="text" name='ext_if_any' value="{{ old('ext_if_any') }}" placeholder="Enter Extension">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Industry</label>
                        <input type="text" name='company_industry' value="{{ $data->company_industry }}" placeholder="Enter Industry">
                    </div>
                    <div class="form-group">
                        <label>Cell Number</label>
                        <input type="text" name='contact_number_1' value="{{ $cellNumber }}" placeholder="Enter Cell Number">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Employees</label>
                        <input type="text" name='employees_strength' value="{{ old('employees_strength') }}" placeholder="Enter Employees">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name='prospect_email' value="{{ $data->prospect_email }}" placeholder="Enter Email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Revenue</label>
                        <input type="text" name='revenue' value="{{ old('revenue') }}" placeholder="Enter Revenue">
                    </div>
                    <div class="form-group">
                        <label>EA Name</label>
                        <input type="text" name='ea_name' value="{{ old('ea_name') }}" placeholder="Enter EA Name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name='address' value="{{ old('address') }}" placeholder="Enter Address">
                    </div>
                    <div class="form-group">
                        <label>EA Phone Number</label>
                        <input type="text" name='ea_phone_no' value="{{ old('ea_phone_no') }}" placeholder="Enter EA Phone">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>LinkedIn Profile</label>
                        <input type="text" name='linkedin_address' value="{{ $data->linkedin_address }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>EA Email</label>
                        <input type="text" name='ea_email' value="{{ old('ea_email') }}" placeholder="Enter EA Email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Prospect Level</label>
                        <input type="text" name='prospects_level' value="{{ old('prospects_level') }}" placeholder="Enter Prospect Level">
                    </div>
                    <div class="form-group">
                        <label>Website</label>
                        <input type="text" name='website' value="{{ old('website') }}" placeholder="Enter Website">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Prospect Vertical</label>
                        <input type="text" name='prospect_vertical' value="{{ old('prospect_vertical') }}" placeholder="Enter Prospect Vertical">
                    </div>
                    <div class="form-group">
                        <label>Opt-in Status</label>
                        <input type="text" name='opt_in_status' value="{{ old('opt_in_status') }}" placeholder="Enter Opt-in Status">
                    </div>
                </div>

                <h3 class="section-title">Analysis & Discovery</h3>

                <div class="form-group">
                    <label>Company Description</label>
                    <textarea name='company_desc' class="form-control" rows="3">{{ old('company_desc') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Responsibilities</label>
                        <textarea name='responsibilities' rows="3">{{ old('responsibilities') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Team Size</label>
                        <textarea name='team_size' rows="3">{{ old('team_size') }}</textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Pain Areas</label>
                        <textarea name='pain_areas' rows="3">{{ old('pain_areas') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Interest/New Initiatives</label>
                        <textarea name='interest_new_initiatives' rows="3">{{ old('interest_new_initiatives') }}</textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Budget</label>
                        <textarea name='budget' rows="3">{{ old('budget') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Defined Agenda</label>
                        <textarea name='defined_agenda' rows="3">{{ old('defined_agenda') }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label>Call Notes</label>
                    <textarea name='call_notes' rows="4">{{ old('call_notes') }}</textarea>
                </div>

                <h3 class="section-title">Meeting Details</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Prospect wish for Meeting/Teleconference?</label>
                        <select name='meeting_teleconference'>
                            <option value="">Select Option</option>
                            <option value="Face to Face meeting" {{ old('meeting_teleconference') == 'Face to Face meeting' ? 'selected' : '' }}>Face to Face meeting</option>
                            <option value="Teleconference" {{ old('meeting_teleconference') == 'Teleconference' ? 'selected' : '' }}>Teleconference</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Is contact the decision maker? (If No, who?)</label>
                        <select name='contact_decision_maker'>
                            <option value="">Select Option</option>
                            <option value="Yes" {{ old('contact_decision_maker') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('contact_decision_maker') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Who else would be the influencers in the decision making process?</label>
                        <input type="text" name='influencers_decision_making_process' value="{{ old('influencers_decision_making_process') }}" placeholder="Enter Influencers">
                    </div>
                    <div class="form-group">
                        <label>Is the Company already affiliated with any other similar services? If Yes, Name?</label>
                        <input type="text" name='company_already_affiliated' value="{{ old('company_already_affiliated') }}" placeholder="Enter Affiliations">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Meeting Date 1</label>
                        <input type="date" name="meeting_date1" value="{{ old('meeting_date1') }}">
                    </div>
                    <div class="form-group">
                        <label>Meeting Time 1</label>
                        <input type="time" name="meeting_time1" value="{{ old('meeting_time1') }}">
                    </div>
                    <div class="form-group">
                        <label>Timezone 1</label>
                        <select name='timezone_1' required>
                            <option value="">Select Timezone</option>
                            @foreach(DateTimeZone::listAbbreviations() as $key => $val)
                                <option value="{{ strtoupper($key) }}" {{ old('timezone_1') == strtoupper($key) ? 'selected' : '' }}>{{ strtoupper($key) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Meeting Date 2</label>
                        <input type="date" name="meeting_date2" value="{{ old('meeting_date2') }}">
                    </div>
                    <div class="form-group">
                        <label>Meeting Time 2</label>
                        <input type="time" name="meeting_time2" value="{{ old('meeting_time2') }}">
                    </div>
                    <div class="form-group">
                        <label>Timezone 2</label>
                        <select name='timezone_2'>
                            <option value="">Select Timezone</option>
                            @foreach(DateTimeZone::listAbbreviations() as $key => $val)
                                <option value="{{ strtoupper($key) }}" {{ old('timezone_2') == strtoupper($key) ? 'selected' : '' }}>{{ strtoupper($key) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Upload Recording/File (.mp3, .zip, .rar)</label>
                    <input type="file" name="upload_file[]" accept=".mp3,.zip,.rar">
                </div>

              

                <div class="btn-group">
                    <button type="submit" class="btn btn-save" id="saveButton">Save</button>
                    <button type="reset" class="btn btn-cancel">Reset</button>

                    <a href="{{ URL::previous() }}" style="padding:0px !important">
                        <button type="button" class="btn btn-cancel" style="background-color:black">Back</button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
<script>
    $(document).ready(function () {
        $('#lhsReportForm').validate({
            rules: {
                board_no: "required",
                employees_strength: "required",
                revenue: "required",
                address: "required",
                company_desc: "required",
                responsibilities: "required",
                team_size: "required",
                pain_areas: "required",
                interest_new_initiatives: "required",
                call_notes: "required",
                meeting_teleconference: "required",
                contact_decision_maker: "required",
                meeting_date1: "required",
                meeting_time1: "required",
                timezone_1: "required",
                prospect_email: {
                    required: true,
                    email: true
                },
                ea_email: {
                    email: true
                }
            },
            messages: {
                board_no: "Please enter Board Number.",
                employees_strength: "Please enter Employees strength.",
                revenue: "Please enter Revenue.",
                address: "Please enter Address.",
                company_desc: "Please enter Company Description.",
                responsibilities: "Please enter Responsibilities.",
                team_size: "Please enter Team Size.",
                pain_areas: "Please enter Pain Areas.",
                interest_new_initiatives: "Please enter Interest/New Initiatives.",
                call_notes: "Please enter Call Notes.",
                meeting_teleconference: "Please select Meeting/Teleconference option.",
                contact_decision_maker: "Please select decision maker option.",
                meeting_date1: "Please select Meeting Date.",
                meeting_time1: "Please select Meeting Time.",
                timezone_1: "Please select Timezone.",
                prospect_email: {
                    required: "Please enter Email.",
                    email: "Please enter a valid Email."
                },
                ea_email: {
                    email: "Please enter a valid Email."
                }
            },
            errorClass: 'error text-danger',
            validClass: 'valid',
            highlight: function (element) {
                $(element).addClass('error').removeClass('valid');
            },
            unhighlight: function (element) {
                $(element).removeClass('error').addClass('valid');
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            }
        });
    });
</script>
@endpush
@endsection