@extends('layouts.admin')
@section('content')
    <?php
    $fullName = '';
    if ($data['prospect_first_name'] != 'NA' && $data['prospect_last_name'] == 'NA') {
        $fullName = $data['prospect_first_name'];
    } elseif ($data['prospect_first_name'] == 'NA' && $data['prospect_last_name'] != 'NA') {
        $fullName = $data['prospect_last_name'];
    } elseif ($data['prospect_first_name'] != 'NA' && $data['prospect_last_name'] != 'NA') {
        $fullName = $data['prospect_first_name'] . ' ' . $data['prospect_last_name'];
    }
    ?>

    <div class="main-right addsubmanager addlead">
        <div class="right-side add-sub">
            <div class="graph">

                <!-- Action Buttons -->
                <div class="button_edit edit_leads mb-3" style="text-align: right;">
                    @if (auth()->user()->manager_type != 2)
                        <a class="btn btn-success btn-sm" href="{{ url('/editlead/' . $data['id']) }}">
                            <i class="ti-pencil"></i> Edit Lead
                        </a>
                    @endif

                    <?php
    $lhs = App\Models\LhsReport::where(['lead_id' => $data['id']])->first();
    $urls = '?employee_id=' . request()->get('employee_id') . '&campaign_id=' . request()->get('campaign_id') . '&date_from=' . request()->get('date_from') . '&date_to=' . request()->get('date_to');
                    ?>

                    @if (!empty($lhs))
                        <a href="{{ url('/employee/export/' . $data['id'] . '/word_single_down') . $urls }}"
                            class="btn btn-warning btn-sm">
                            <i class="ti-download"></i> Word
                        </a>
                        <a href="{{ route('employee.show_mom', [$data['id']]) }}" class="btn btn-info btn-sm">
                            Create MoM
                        </a>
                    @endif

                    @if (!empty($lhsFiles))
                        <a href="{{ url($lhsFiles->file_path . '/' . $lhsFiles->file_name) }}" class="btn btn-primary btn-sm"
                            download="file.mp3">
                            <i class="ti-download"></i> File
                        </a>
                    @endif
                </div>

                <h2>Lead Details: {{ $fullName }}</h2>

                <!-- Lead Information Section -->
                <form method="post" action="{{ route('leads.update', $data['id']) }}" id="leadForm">

                    @csrf
                    {{ method_field('PATCH') }}

                    <input type="hidden" id="edit_mode" value="1">

                    <!-- Row 1 -->
                    <div class="form-row">

                        <!-- CAMPAIGN -->
                        <div class="form-group">
                            <label>Campaign Name</label>
                            <input type="text" id="prospect_email" name="campaign_name" placeholder="Enter Email"
                                value="{{ $data['source']['source_name'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('source_name') }}</small>
                        </div>
                        <!-- EMAIL -->
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" id="prospect_email" name="prospect_email" placeholder="Enter Email"
                                value="{{ $data['prospect_email'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('prospect_email') }}</small>
                        </div>
                    </div>

                    <!-- AJAX LOADED SUB-CAMPAIGN -->
                    <div class="form-row appended_items"></div>

                    <!-- Row 2 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" id="prospect_first_name" name="prospect_first_name"
                                value="{{ $data['prospect_first_name'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('prospect_first_name') }}</small>
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" id="prospect_last_name" name="prospect_last_name"
                                value="{{ $data['prospect_last_name'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('prospect_last_name') }}</small>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="form-row">

                        <div class="form-group">
                            <label>Organization Industry</label>
                            <input type="text" id="company_industry" name="company_industry"
                                value="{{ $data['company_industry'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('company_industry') }}</small>
                        </div>

                        <div class="form-group">
                            <label>Organization</label>
                            <input type="text" id="company_name" name="company_name" value="{{ $data['company_name'] }}"
                                readonly>
                            <small class="text-danger error">{{ $errors->first('company_name') }}</small>
                        </div>
                    </div>

                    <!-- Row 4 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Contact No</label>
                            <input type="text" id="contact_number_1" name="contact_number_1"
                                value="{{ $data['contact_number_1'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('contact_number_1') }}</small>
                        </div>

                        <div class="form-group">
                            <label>Second Contact No</label>
                            <input type="text" id="contact_number_2" name="contact_number_2"
                                value="{{ $data['contact_number_2'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('contact_number_2') }}</small>
                        </div>
                    </div>

                    <!-- Row 5 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Prospect Name</label>
                            <input type="text" id="prospect_name" name="prospect_name"
                                value="{{ $data['prospect_first_name'] }} {{ $data['prospect_last_name'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('prospect_name') }}</small>
                        </div>

                        <div class="form-group">
                            <label>Designation</label>
                            <input type="text" id="designation" name="designation" value="{{ $data['designation'] }}"
                                readonly>
                            <small class="text-danger error">{{ $errors->first('designation') }}</small>
                        </div>
                    </div>

                    <!-- Row 6 -->
                    <div class="form-row">

                        <div class="form-group">
                            <label>LinkedIn Address</label>
                            <input type="text" id="linkedin_address" name="linkedin_address"
                                value="{{ $data['linkedin_address'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('linkedin_address') }}</small>
                        </div>

                        <div class="form-group">
                            <label>Business Function</label>
                            <input type="text" id="bussiness_function" name="bussiness_function"
                                value="{{ $data['bussiness_function'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('bussiness_function') }}</small>
                        </div>
                    </div>

                    <!-- Row 7 -->
                    <div class="form-row">

                        <div class="form-group">
                            <label>Designation Level</label>
                            <input type="text" id="designation_level" name="designation_level"
                                value="{{ $data['designation_level'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('designation_level') }}</small>
                        </div>

                        <div class="form-group">
                            <label>Time Zone</label>
                            <input type="text" id="timezone" name="timezone" value="{{ $data['timezone'] }}" readonly>
                            <small class="text-danger error">{{ $errors->first('timezone') }}</small>
                        </div>
                    </div>

                </form>

                <div class="form-group">
    <label class="control-label" style="font-weight: 600; margin-bottom: 8px;">Status</label>
    <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; border: 1px solid #e9ecef;">
        <!-- Status Indicator -->
        <div style="margin-bottom: 10px;">
            @if ($data['status'] == 1)
                <span style="background: #ffc107; color: #212529; padding: 6px 16px; border-radius: 20px; font-weight: 500; font-size: 14px;">
                    <i class="ti-time"></i> Pending
                </span>
                @elseif($data['status'] == 0)
                <span style="background: #dc3545; color: white; padding: 6px 16px; border-radius: 20px; font-weight: 500; font-size: 14px;">
                    <i class="ti-close"></i> fresh lead
                </span>
            @elseif($data['status'] == 2)
                <span style="background: #dc3545; color: white; padding: 6px 16px; border-radius: 20px; font-weight: 500; font-size: 14px;">
                    <i class="ti-close"></i> Failed
                </span>
            @elseif($data['status'] == 4)
                <span style="background: #ffc107; color: #212529; padding: 6px 16px; border-radius: 20px; font-weight: 500; font-size: 14px;">
                    <i class="ti-reload"></i> In Progress
                </span>
            @else
                <span style="background: #28a745; color: white; padding: 6px 16px; border-radius: 20px; font-weight: 500; font-size: 14px;">
                    <i class="ti-check"></i> Closed
                </span>
            @endif
        </div>

        <!-- Action Buttons -->
        @if (auth()->user()->manager_type != 2)
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                
                @if (in_array($data['status'], [1, 4]))
                    <button type="button" 
                            onclick="showstatusmodal('{{ $data['id'] }}')"
                            data-toggle="modal" 
                            data-target="#status-modal"
                            style="background: transparent; border: 1px solid #007bff; color: #007bff; padding: 6px 15px; border-radius: 20px; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: all 0.3s;">
                        <i class="ti-exchange-vertical" style="font-size: 12px;"></i>
                        Change Status
                    </button>
                @endif

                <button type="button" 
                        onclick="showaddmodal('{{ $data['id'] }}')"
                        data-toggle="modal" 
                        data-target="#status-modal-quicknote"
                        style="background: transparent; border: 1px solid #6c757d; color: #6c757d; padding: 6px 15px; border-radius: 20px; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: all 0.3s;">
                    <i class="ti-pencil-alt" style="font-size: 12px;"></i>
                    Add Quick Note
                </button>

                @if ($data['status'] == 3)
                    <a href="{{ url('/employee/lhs_report/view_lhs', [$data['id']]) }}" 
                       style="background: transparent; border: 1px solid #28a745; color: #28a745; padding: 6px 15px; border-radius: 20px; font-size: 13px; text-decoration: none; display: flex; align-items: center; gap: 5px; transition: all 0.3s;">
                        <i class="ti-file" style="font-size: 12px;"></i>
                        View Report
                    </a>
                @endif

            </div>
        @endif

    </div>
</div>

                <!-- Notes History Section -->
                <div class="notes-history mt-4">
                    <h3>Notes History</h3>
                    <?php
    $notesCount = App\Models\Note::where(['lead_id' => $data['id']])->count();
    $LhsReportCount = App\Models\LhsReport::where(['lead_id' => $data['id']])->count();
                    ?>
                    <input type="hidden" id="notes_count_{{ $data['id'] }}" name="notes_count" value="{{ $notesCount }}">
                    <input type="hidden" id="Lhsreport_count_{{ $data['id'] }}" name="Lhsreport_count"
                        value="{{ $LhsReportCount }}">

                    @if(count($record['notes'] ?? []) > 0)
                        <div class="table-container" id="table_data">
                            <table id="employee-table" class="table table-striped table-hover">
                                <thead class="thead-main">
                                    <tr>
                                        <th>Note</th>
                                        <th>Reminder Date</th>
                                        <th>Conversation Type</th>
                                        <th>Phone Number</th>
                                        <th>Updated On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(Auth::user()->is_admin != 1)
                                    @foreach (($record['notes'] ?? []) as $note)
                                                            <tr>
                                                                <td width="300">
                                                                    <p class="notes_comment">
                                                                        @if(strlen($note['feedback'] ?? '') > 50)
                                                                            {{ \Illuminate\Support\Str::limit($note['feedback'], 50) }}
                                                                            <a href="javascript:void(0);" class="show-more-note" data-note="{{ $note['feedback'] }}" style="color: #007bff; font-weight: 600; cursor: pointer; text-decoration: underline; display: inline-block; margin-left: 5px; padding: 0;">Show More</a>
                                                                        @else
                                                                            {{ $note['feedback'] ?? '' }}
                                                                        @endif
                                                                    </p>
                                                                </td>
                                                                <td>
                                                                    <?php
                                        if (isset($note['reminder_date']) && !empty($note['reminder_date'])) {
                                            echo date('d M, Y', strtotime($note['reminder_date']));
                                        } else {
                                            echo 'N/A';
                                        }
                                                                        ?>
                                                                </td>
                                                                <td>{{ !empty($note['reminder_for']) ? $note['reminder_for'] :'N/A' }}</td>
                                                                <td>{{ !empty($note['phone_number']) ? $note['phone_number'] :'N/A' }}</td>
                                                                <td style="white-space:nowrap !important">
                                                                    <?php 
                                                                        $date = \Carbon\Carbon::parse($note['updated_at']);
                                                                        ?>
                                                                    {{ date('d M, Y h:i A', strtotime($date)) }}
                                                                </td>
                                                            </tr>
                                    @endforeach

                                    @else
@foreach (($record['notes'] ?? []) as $note)

@php
    $date = \Carbon\Carbon::parse($note['updated_at']);
    $isRecent = $date->diffInHours(\Carbon\Carbon::now()) <= 24;
    $tdStyle = $isRecent ? 'background:#fff3cd !important;' : '';
@endphp

<tr>
    <td width="300" style="{{ $tdStyle }}">
        <p class="notes_comment">
            @if(strlen($note['feedback'] ?? '') > 100)
                {{ \Illuminate\Support\Str::limit($note['feedback'], 100) }}
                <a href="javascript:void(0);" class="show-more-note" data-note="{{ $note['feedback'] }}" style="color: #007bff; font-weight: 600; cursor: pointer; text-decoration: underline; display: inline-block; margin-left: 5px; padding: 0;">Show More</a>
            @else
                {{ $note['feedback'] ?? '' }}
            @endif
        </p>
    </td>

    <td style="{{ $tdStyle }}">
        @if(!empty($note['reminder_date']))
            {{ date('d M, Y', strtotime($note['reminder_date'])) }}
        @else
            N/A
        @endif
    </td>

    <td style="{{ $tdStyle }}">
        {{ !empty($note['reminder_for']) ? $note['reminder_for'] : 'N/A' }}
    </td>

    <td style="{{ $tdStyle }}">
        {{ !empty($note['phone_number']) ? $note['phone_number'] : 'N/A' }}
    </td>

    <td style="white-space:nowrap !important; {{ $tdStyle }}">
        {{ $date->format('d M, Y h:i A') }}
    </td>
</tr>

@endforeach
@endif
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No notes found for this lead.
                        </div>
                    @endif
                </div>

                <!-- Buttons -->
                <div class="btn-group mt-3">
                    <button type="button" class="btn btn-cancel" onclick="window.history.back()">Back</button>
                </div>

            </div>
        </div>
    </div>

    <!-- View Full Note Modal -->
    <div id="view-note-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="viewNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: #0d3a6b; color: white;">
                    <h4 class="modal-title" style="color: white;">Full Note</h4>
                    <button type="button" class="close close-view-note-modal" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 1; border: none; background: transparent; font-size: 24px;">×</button>
                </div>
                <div class="modal-body" style="padding: 20px; max-height: 400px; overflow-y: auto;">
                    <p id="full-note-content" style="white-space: pre-wrap; word-break: break-word; font-family: 'Poppins', sans-serif; font-size: 14px; line-height: 1.6; color: #333; margin: 0;"></p>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-default waves-effect close-view-note-modal" data-dismiss="modal" style="background: #6c757d; color: white; border-radius: 4px; padding: 6px 12px;">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div id="status-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="ajaxform">
                    <meta name="csrf-token" content="{{ csrf_token() }}" />

                    <div class="modal-header">
                        <h4 class="modal-title">Change Status</h4>
                        <button type="button" class="close close-status-modal" data-dismiss="modal" aria-hidden="true"
                            style="color:black">×</button>
                    </div>
                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul class="custom_text"></ul>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Select Status: </label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="4">In progress</option>
                                <option value="3">Closed</option>
                                <option value="2">Failed</option>
                            </select>
                            @if($errors->has('status'))
                                <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="lead_id" name="lead_id">
                        <button type="button" class="btn btn-default waves-effect close-status-modal"
                            data-dismiss="modal">Close</button>
                        <button id="save-data" type="button" class="btn btn-info waves-effect waves-light ">Save
                            changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Notes Add Modal -->
    <div id="status-modal-quicknote" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form">
                    <meta name="csrf-token" content="{{ csrf_token() }}" />

                    <div class="modal-header">
                        <h4 class="modal-title">Add Quick Note</h4>
                        <button type="button" id="modelclose" class="close modal-close" data-dismiss="modal"
                            aria-hidden="true" style="color:black">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group responseconvers" style="display: flex; gap: 20px;">
                            <div>
                                <input type="radio" class="conversation_type" id="NoResponse" name="conversation_type"
                                    value="NoResponse" checked="checked">
                                  <label for="NoResponse">VM/No Response</label>
                            </div>
                            <div>
                                  <input type="radio" class="conversation_type" id="Conversation" name="conversation_type"
                                    value="Conversation">
                                  <label for="Conversation">Conversation</label>
                            </div>
                        </div>

                        <div class="NoResponseData">
                            <div class="form-group" id="status" name="status">
                                <label class="control-label">Reminder Date</label>
                                <input type="date" class="form-control" placeholder="Reminder Date" name="reminder_date"
                                    value="{{ old('reminder_date') }}" id="min-date">

                                <label class="control-label">Reminder Time</label>
                                <input type="time" class="form-control" id="reminder_time" name="reminder_time">

                                <label class="control-label">Conversation Type</label>
                                <select id="reminder_for" class="form-control required" name="reminder_for">
                                    <option value="">Choose Conversation Type</option>
                                    <option value="Declined">Declined</option>
                                    <option value="DNC">DNC</option>
                                    <option value="Follow-up Call">Follow-up Call</option>
                                    <option value="Follow-up Email/Info Requested">Follow-up Email/Info Requested</option>
                                    <option value="Meeting Set-up">Meeting Set-up</option>
                                    <option value="Not Interested">Not Interested</option>
                                    <option value="Not Right Party">Not Right Party</option>
                                    <option value="Reference Shared">Reference Shared</option>
                                </select>

                                <div class="alert alert-danger print-error-msg-1" style="display:none">
                                    <ul class="custom_text-1"></ul>
                                </div>

                                @if(Auth::user()->is_admin == 1)
                                    <label class="control-label">Phone Number</label>
                                    <input type="tel" class="form-control" placeholder="Phone Number" name="phone_number"
                                        value="" id="phone_number" pattern="[0-9]{10}"
                                        title="Please enter a valid 10-digit phone number">
                                @else
                                    <input type="tel" class="form-control" placeholder="Phone Number" name="phone_number"
                                        value="" id="phone_number" pattern="[0-9]{10}"
                                        title="Please enter a valid 10-digit phone number" style="display:none">
                                @endif

                                <label class="control-label">Note</label>
                                <input type="hidden" class="form-control" name="source_id"
                                    value="{{ $data->source_id ?? '' }}">
                                <textarea required type="text" class="form-control required" name="feedback" id="feedback"
                                    placeholder="Enter Note" style="min-height: 130px;">{{ old('note') }}</textarea>

                                <div class="alert alert-danger print-error-msg" style="display:none">
                                    <ul class="custom_text" id="showerrormessage"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="hidden" id="lead_id_quick_note" name="lead_id_quick_note">
                        <button type="button" class="btn btn-default waves-effect modal-close"
                            data-dismiss="modal">Close</button>
                        <button id="save-data-quick-note" type="button" class="btn btn-info waves-effect waves-light ">Add
                            Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <style>
            #employee-table td:last-child {
                display: table-cell !important;
            }
        </style>

        <script>
            $(document).ready(function () {
                var table = $('#employee-table').DataTable({
                    "order": [],
                    "pageLength": 10,
                    "columnDefs": [{
                        "type": "date",
                        "targets": [1, 4]
                    }]
                });

                $('.modal').on('hidden.bs.modal', function () {
                    $("#reminder_for").val('');
                    $('.alert.alert-danger.print-error-msg').hide();
                    $('#status').prop('selectedIndex', 0);
                });

                // Show more note modal handler
                $(document).on('click', '.show-more-note', function (e) {
                    e.preventDefault();
                    var note = $(this).attr('data-note');
                    $('#full-note-content').text(note);
                    $('#view-note-modal').modal('show');
                });

                $('.close-view-note-modal').on('click', function () {
                    $('#view-note-modal').modal('hide');
                });
            });
        </script>

        <script>
            $(document).ready(function () {
                $('#feedback').val('VM/No Response');

                $('input[type=radio][name=conversation_type]').change(function () {
                    if (this.value == 'NoResponse') {
                        $('#feedback').val('VM/No Response');
                        $('#min-date').val("");
                        $('#reminder_for').val("");
                        $('#reminder_time').val("");
                        $('#phone_number').val('');
                        $('.alert.alert-danger.print-error-msg-1').hide();
                        $('.alert.alert-danger.print-error-msg').hide();
                    } else if (this.value == 'Conversation') {
                        $('.alert.alert-danger.print-error-msg-1').hide();
                        $('.alert.alert-danger.print-error-msg').hide();
                        $('#feedback').val('');
                        $('#phone_number').val('');
                        $('#min-date').val("");
                        $('#reminder_for').val("");
                        $('#reminder_time').val("");
                    }
                });
            });
        </script>

        <script>
            $("#save-data-quick-note").click(function (event) {
                event.preventDefault();

                let feedback = $("[name=feedback]").val();
                var selectedVal = "";
                var selected = $("input[type=radio][name=conversation_type]:checked");

                if (selected.length > 0) {
                    selectedVal = selected.val();
                }

                var selecteddata = ""
                if (selectedVal == 'Conversation') {
                    selecteddata = $('#reminder_for').val();
                } else {
                    selecteddata = 1;
                }

                if (selecteddata == 0) {
                    $('.alert.alert-danger.print-error-msg-1').show();
                    $('ul.custom_text-1').html(
                        '<li class="error_list"><span class="tab">Conversation Type Cannot Be Empty!</span></li>'
                    );
                } else if (feedback == 0) {
                    $('.alert.alert-danger.print-error-msg-1').hide();
                    $('.alert.alert-danger.print-error-msg').show();
                    $('ul.custom_text').html(
                        '<li class="error_list"><span class="tab">Note Field Cannot Be Empty!</span></li>'
                    );
                } else {
                    $('.alert.alert-danger.print-error-msg').hide();
                    $('ul.custom_text').html('');

                    let phone_number = $("[name=phone_number]").val();
                    let feedback = $("[name=feedback]").val();
                    let reminder_date = $("[name=reminder_date]").val();
                    let reminder_time = $("[name=reminder_time]").val();
                    let reminder_for = $("[name=reminder_for]").val();
                    let lead_id = $("input[name=lead_id_quick_note]").val();
                    let source_id = $("input[name=source_id]").val();
                    let _token = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: '{{ route('add_note') }}',
                        type: "POST",
                        data: {
                            reminder_date: reminder_date,
                            reminder_time: reminder_time,
                            reminder_for: reminder_for,
                            lead_id: lead_id,
                            source_id: source_id,
                            feedback: feedback,
                            phone_number: phone_number,
                            _token: _token
                        },
                        success: function (response) {
                            if ($.isEmptyObject(response.error)) {
                                toastr.success(response.success, 'Success!')
                                location.reload(true);
                            } else {
                                toastr.error(response.error, 'Error!');
                            }
                        },
                    });
                }
            });
        </script>

        <script>
            $(document).ready(function () {
                $("#save-data").click(function (event) {
                    event.preventDefault();

                    let status = $("select[name=status]").val();
                    let lead_id = $("input[name=lead_id]").val();
                    let _token = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: '{{ route('changeStatus') }}',
                        type: "POST",
                        data: {
                            lead_id: lead_id,
                            status: status,
                            _token: _token
                        },
                        success: function (response) {
                            if ($.isEmptyObject(response.error)) {
                                toastr.success(response.success, 'Success!')

                                if (response) {
                                    $(".print-error-msg").css('display', 'none');

                                    if (response.status == 'failed') {
                                        var Current_url = "{{ url('/leads/failed') }}";
                                        window.location.href = Current_url;
                                    } else if (response.status == 'close') {
                                        var Current_url = "{{ url('/leads/closed') }}";
                                        window.location.href = Current_url;
                                    } else {
                                        location.reload(true);
                                    }
                                    $("#ajaxform")[0].reset();
                                }
                            } else {
                                $('.alert.alert-danger.print-error-msg').show();
                                $('ul.custom_text').html(response.lhs_link);
                                toastr.error(response.error, 'Error!');
                            }
                        },
                    });
                });
            });
        </script>

        <script>
            function showaddmodal(id) {
                $('#lead_id_quick_note').val(id);
                $('#status-modal-quicknote').modal('show');
            }

            $('.modal-close').on('click', function (event) {
                $('#status-modal-quicknote').modal('hide');
            });

            function showstatusmodal(id) {
                $('#lead_id').val(id);
                $('#status-modal').modal('show');
            }

            $('.close-status-modal').on('click', function (event) {
                $('#status-modal').modal('hide');
            });
        </script>

    @endpush

@endsection