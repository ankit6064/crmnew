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
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <input type="text" id="contact_number_1" name="contact_number_1"
                                    value="{{ $data['contact_number_1'] }}" readonly style="flex: 1;">
                                <?php
                                    $contact1 = $data['contact_number_1'] ?? '';
                                    $numbers1 = [];
                                    if (!empty($contact1)) {
                                        if (strpos($contact1, '/-') !== false) {
                                            $numbers1 = explode('/-', $contact1);
                                        } else {
                                            $numbers1 = preg_split('/[,\s;]+/', $contact1);
                                        }
                                        $numbers1 = array_map('trim', $numbers1);
                                        $numbers1 = array_map(function ($num) {
                                            return str_replace('-', '', $num);
                                        }, $numbers1);
                                        $numbers1 = array_values(array_filter($numbers1, function ($num) {
                                            return preg_match('/\d/', $num);
                                        }));
                                    }
                                    $count1 = count($numbers1);
                                ?>
                                @if ($count1 > 0)
                                    @php
                                        $badgeText1 = $count1 <= 1 ? 'Dial' : '+ show more';
                                    @endphp
                                    <span class="badge" 
                                          style="cursor:pointer; background-color:#192e62; color:#fff; padding: 10px 15px; font-size: 12px; font-weight: 500; border-radius: 4px; display: inline-block; white-space: nowrap;" 
                                          onclick="showAllNumbers('{{ implode(', ', $numbers1) }}', {{ $data['id'] }})">
                                          {{ $badgeText1 }}
                                    </span>
                                @endif
                            </div>
                            <small class="text-danger error">{{ $errors->first('contact_number_1') }}</small>
                        </div>

                        <div class="form-group">
                            <label>Second Contact No</label>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <input type="text" id="contact_number_2" name="contact_number_2"
                                    value="{{ $data['contact_number_2'] }}" readonly style="flex: 1;">
                                <?php
                                    $contact2 = $data['contact_number_2'] ?? '';
                                    $numbers2 = [];
                                    if (!empty($contact2)) {
                                        if (strpos($contact2, '/-') !== false) {
                                            $numbers2 = explode('/-', $contact2);
                                        } else {
                                            $numbers2 = preg_split('/[,\s;]+/', $contact2);
                                        }
                                        $numbers2 = array_map('trim', $numbers2);
                                        $numbers2 = array_map(function ($num) {
                                            return str_replace('-', '', $num);
                                        }, $numbers2);
                                        $numbers2 = array_values(array_filter($numbers2, function ($num) {
                                            return preg_match('/\d/', $num);
                                        }));
                                    }
                                    $count2 = count($numbers2);
                                ?>
                                @if ($count2 > 0)
                                    @php
                                        $badgeText2 = $count2 <= 1 ? 'Dial' : '+ show more';
                                    @endphp
                                    <span class="badge" 
                                          style="cursor:pointer; background-color:#192e62; color:#fff; padding: 10px 15px; font-size: 12px; font-weight: 500; border-radius: 4px; display: inline-block; white-space: nowrap;" 
                                          onclick="showAllNumbers('{{ implode(', ', $numbers2) }}', {{ $data['id'] }})">
                                          {{ $badgeText2 }}
                                    </span>
                                @endif
                            </div>
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

                                <div id="conversation_type_container" style="display:none;">
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
                                </div>

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

    <!-- View Contact Numbers Modal -->
    <div class="modal fade" id="numberModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #192e62; color: #fff; padding: 10px 15px;">
                    <h6 class="modal-title" style="color: white; margin: 0; font-weight: 600;">Contact Numbers</h6>
                    <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 1; border: none; background: transparent; font-size: 24px;"
                        onclick="closemodal();">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 15px;">
                    <div id="numberRow" style="display: none;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note After Dial Modal -->
    <div id="status-modal-dialnote" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dialNoteModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <meta name="csrf-token" content="{{ csrf_token() }}" />
                <div class="modal-header" style="background: #0d3a6b; color: white;">
                    <h4 class="modal-title" style="color: white;">Add Note after Dial</h4>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="form-group">
                        <label for="dial_conversation_type" class="control-label">Call Outcome</label>
                        <select name="dial_conversation_type" id="dial_conversation_type" class="form-control">
                            <option value="NoResponse">VM / No Response</option>
                            <option value="Conversation">Connected</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div id="dial_conversation_type_container" style="display:none;">
                            <label class="control-label">Conversation Type <span class="text-danger">*</span></label>
                            <select id="dial_reminder_for" class="form-control" onchange="checkdialtype();">
                                <option value="">Choose Option</option>
                                <option value="Callback">Callback</option>
                                <option value="Declined">Declined</option>
                                <option value="DNC">DNC</option>
                                <option value="Follow-up Call">Follow-up Call</option>
                                <option value="Follow-up Email/Info Requested">Follow-up Email/Info Requested</option>
                                <option value="Meeting Set-up">Meeting Set-up</option>
                                <option value="Not Interested">Not Interested</option>
                                <option value="Not Right Party">Not Right Party</option>
                                <option value="Reference Shared">Reference Shared</option>
                            </select>
                        </div>

                        <div id="dial_reminderdatetime" style="display:none; margin-top: 10px;">
                            <label class="control-label">Reminder Date</label>
                            <input type="date" id="dial_min-date" class="form-control">

                            <label class="control-label" style="margin-top: 10px;">Reminder Time</label>
                            <input type="time" id="dial_reminder_time" class="form-control">
                        </div>

                        <div id="dial_callbackdatetime" style="display:none; margin-top: 10px;">
                            <label class="control-label">Callback Date</label>
                            <input type="date" id="dial_callback_date" class="form-control">

                            <label class="control-label" style="margin-top: 10px;">Callback Time</label>
                            <input type="time" id="dial_callback_time" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="text" class="form-control" value="{{ $data['prospect_email'] }}" disabled>
                        </div>
                        <div id="dial_phone_number_container" style="display:none; margin-top: 10px;">
                            <label class="control-label">Phone Number</label>
                            <input type="tel" id="dial_phone_number" class="form-control" placeholder="Phone Number">
                        </div>

                        <label class="control-label" style="margin-top: 10px;">Note</label>
                        <textarea id="dial_feedback" class="form-control" style="min-height:130px;"></textarea>

                        <div class="alert alert-danger print-error-msg" style="display:none; margin-top: 10px;">
                            <ul></ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6;">
                    <input type="hidden" id="lead_id_dial_note">
                    <button class="btn btn-info waves-effect waves-light" id="save-data-dialnote">Submit</button>
                </div>
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
                        $('#conversation_type_container').hide();
                    } else if (this.value == 'Conversation') {
                        $('.alert.alert-danger.print-error-msg-1').hide();
                        $('.alert.alert-danger.print-error-msg').hide();
                        $('#feedback').val('');
                        $('#phone_number').val('');
                        $('#min-date').val("");
                        $('#reminder_for').val("");
                        $('#reminder_time').val("");
                        $('#conversation_type_container').show();
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
                        error: function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error, 'Error!');
                            } else {
                                toastr.error("Something went wrong", "Error!");
                            }
                        }
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
                                if (response.lhs_link) {
                                    $('#status-modal .print-error-msg').show();
                                    $('#status-modal .print-error-msg ul').html(response.lhs_link);
                                } else {
                                    $('#status-modal .print-error-msg').show();
                                    $('#status-modal .print-error-msg ul').html('<li>' + response.error + '</li>');
                                }
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
                $('input[name="conversation_type"][value="NoResponse"]').prop('checked', true);
                $('#min-date').val('');
                $('#reminder_time').val('');
                $('#reminder_for').val('');
                $('#feedback').val('VM/No Response');
                $('#phone_number').val('');
                $('#conversation_type_container').hide();
                $('#status-modal-quicknote').modal('show');
            }

            $('.modal-close').on('click', function (event) {
                $('#status-modal-quicknote').modal('hide');
            });

            function showstatusmodal(id) {
                $('#lead_id').val(id);
                $('#status-modal .print-error-msg').hide();
                $('#status-modal .print-error-msg ul').html('');
                $('#status').val('');
                $('#status-modal').modal('show');
            }

            $('.close-status-modal').on('click', function (event) {
                $('#status-modal').modal('hide');
            });

            // Contact Numbers Modal and Dial Functionality
            function showAllNumbers(numbers, leadId) {
                if (!numbers) return;
                let numList = [];
                if (numbers.indexOf('/-') !== -1) {
                    numList = numbers.split('/-');
                } else {
                    numList = numbers.split(/[,\s;]+/);
                }
                numList = numList.map(n => n.trim()).filter(n => n.length > 0);

                let rowHtml = '<table class="table table-bordered table-striped text-center" style="margin-top: 10px; width: 100%;">';
                rowHtml += '<thead>';
                rowHtml += '  <tr>';
                rowHtml += '    <th style="text-align: center; width: 80px;">Dial</th>';
                rowHtml += '    <th style="text-align: center;">Phone Number</th>';
                rowHtml += '  </tr>';
                rowHtml += '</thead>';
                rowHtml += '<tbody>';

                numList.forEach(function (num) {
                    rowHtml += '  <tr>';
                    rowHtml += '    <td>';
                    rowHtml += '      <a href="javascript:void(0);" onclick="dialNumber(\'' + num.replace(/'/g, "\\'") + '\', ' + leadId + ')" class="btn btn-xs btn-success" style="border-radius: 50%; padding: 5px 8px; background-color: #28a745; border-color: #28a745;" title="Dial via API">';
                    rowHtml += '        <i class="fa fa-phone" style="color: white;"></i>';
                    rowHtml += '      </a>';
                    rowHtml += '    </td>';
                    rowHtml += '    <td style="font-size: 15px; font-weight: 500; vertical-align: middle; text-align: left; padding-left: 15px;">' + num + '</td>';
                    rowHtml += '  </tr>';
                });
                rowHtml += '</tbody>';
                rowHtml += '</table>';

                $('#numberRow').css('display', 'block').html(rowHtml);
                $('#numberModal').modal('show');
            }

            function closemodal() {
                $('#numberModal').modal('hide');
            }

            function dialNumber(num, leadId) {
                if (!num) return;

                toastr.info('Initiating dial via API for ' + num + '...');

                $.ajax({
                    url: "{{ route('employee.dial') }}",
                    method: 'POST',
                    data: {
                        phone: num,
                        lead_id: leadId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            toastr.success('Dialer response: ' + response.message);
                            $('#numberModal').modal('hide');
                            showdialnoteaddmodal(leadId, num);
                        } else {
                            toastr.error(response.error || 'Failed to place call.');
                        }
                    },
                    error: function (xhr) {
                        let errorMsg = 'An error occurred while trying to dial.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        toastr.error(errorMsg, 'Dialer Error');
                    }
                });
            }

            function showdialnoteaddmodal(id, phone) {
                window.dialnote_submitted = false;
                $('#lead_id_dial_note').val(id);

                // Save pending note state to localStorage
                localStorage.setItem('pending_dial_note', JSON.stringify({ lead_id: id, phone: phone }));

                // Default dropdown to VM/No Response
                $('#dial_conversation_type').val('NoResponse');

                $('#dial_min-date').val('');
                $('#dial_reminder_time').val('');
                $('#dial_reminder_for').val('');
                $('#dial_feedback').val('VM/No Response');

                // Prefill and disable/enable phone number
                $('#dial_phone_number').val(phone || '');
                if (phone) {
                    $('#dial_phone_number').prop('disabled', true);
                } else {
                    $('#dial_phone_number').prop('disabled', false);
                }

                $('#dial_callback_date').val('');
                $('#dial_callback_time').val('');

                // Hide Conversation Type dropdown and datetime fields, but show phone number
                $('#dial_conversation_type_container').hide();
                $('#dial_reminderdatetime').hide();
                $('#dial_callbackdatetime').hide();
                $('#dial_phone_number_container').show();

                $('#status-modal-dialnote').modal('show');
            }

            function checkdialtype() {
                var remindfor = $('#dial_reminder_for').val();
                if (remindfor == 'Callback') {
                    $('#dial_reminderdatetime').css('display', 'none');
                    $('#dial_callbackdatetime').css('display', 'block');
                } else {
                    $('#dial_reminderdatetime').css('display', 'block');
                    $('#dial_callbackdatetime').css('display', 'none');
                }
            }

            $(document).ready(function () {
                // Restore modal if pending note exists on page load
                let pendingNote = localStorage.getItem('pending_dial_note');
                if (pendingNote) {
                    let noteData = JSON.parse(pendingNote);
                    showdialnoteaddmodal(noteData.lead_id, noteData.phone);
                }

                // Prevent escape key from closing dialnote modal
                $(document).on('keydown', function (event) {
                    if (event.key === "Escape" && ($('#status-modal-dialnote').hasClass('show') || $('#status-modal-dialnote').is(':visible'))) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                });

                // Prevent closing the modal unless submitted
                $('#status-modal-dialnote').on('hide.bs.modal', function (e) {
                    if (!window.dialnote_submitted) {
                        e.preventDefault();
                        return false;
                    }
                });

                $('#dial_conversation_type').change(function () {
                    if (this.value == 'NoResponse') {
                        $('#dial_feedback').val('VM/No Response');
                        $('#dial_min-date').val("");
                        $('#dial_reminder_for').val("");
                        $('#dial_reminder_time').val("");
                        if (!$('#dial_phone_number').prop('disabled')) {
                            $('#dial_phone_number').val('');
                        }
                        $('#dial_callback_date').val('');
                        $('#dial_callback_time').val('');

                        // Hide other fields, showing only note option and phone number
                        $('#dial_conversation_type_container').hide();
                        $('#dial_reminderdatetime').hide();
                        $('#dial_callbackdatetime').hide();
                        $('#dial_phone_number_container').show();
                    } else if (this.value == 'Conversation') {
                        $('#dial_feedback').val('');
                        if (!$('#dial_phone_number').prop('disabled')) {
                            $('#dial_phone_number').val('');
                        }
                        $('#dial_min-date').val("");
                        $('#dial_reminder_for').val("");
                        $('#dial_reminder_time').val("");
                        $('#dial_callback_date').val('');
                        $('#dial_callback_time').val('');

                        // Show Conversation Type dropdown and phone number container
                        $('#dial_conversation_type_container').show();
                        $('#dial_phone_number_container').show();
                        checkdialtype();
                    }
                });

                window.addEventListener('beforeunload', function (e) {
                    if (!window.dialnote_submitted && ($('#status-modal-dialnote').hasClass('show') || $('#status-modal-dialnote').is(':visible'))) {
                        e.preventDefault();
                        e.returnValue = 'Please submit the call note first.';
                        return 'Please submit the call note first.';
                    }
                });

                // Save dial quick note
                $('#save-data-dialnote').off('click').on('click', function (event) {
                    event.preventDefault();
                    let submitBtn = $('#save-data-dialnote');
                    let originalHtml = submitBtn.html();
                    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

                    let lead_id = $('#lead_id_dial_note').val();
                    let source_id = "{{ $data->source_id ?? $data['source_id'] ?? '' }}";
                    let reminder_date = $('#dial_min-date').val();
                    let reminder_time = $('#dial_reminder_time').val();
                    let reminder_for = $('#dial_reminder_for').val();
                    let feedback = $('#dial_feedback').val();
                    let phone_number = $('#dial_phone_number').val();
                    let callback_date = $('#dial_callback_date').val();
                    let callback_time = $('#dial_callback_time').val();
                    let type = $('#dial_conversation_type').val();

                    if (type === 'Conversation' && !reminder_for) {
                        toastr.error("Conversation Type is required", "Validation Error");
                        submitBtn.prop('disabled', false).html(originalHtml);
                        return false;
                    }

                    let _token = $('meta[name="csrf-token"]').attr('content');

                    $.post("{{ url('leads/add_note') }}", {
                        lead_id, source_id, reminder_date, reminder_time, reminder_for, feedback, type, phone_number, callback_date, callback_time, _token,
                        is_dial_note: 1
                    }, function (res) {
                        submitBtn.prop('disabled', false).html(originalHtml);

                        if (res.success) {
                            toastr.success(res.success);
                            // Clear pending note state from localStorage
                            localStorage.removeItem('pending_dial_note');
                            window.dialnote_submitted = true;
                            $('#status-modal-dialnote').modal('hide');
                            // Reload page to reflect note updates reactively
                            location.reload(true);
                        } else {
                            toastr.error("Something went wrong");
                        }

                    }).fail(function (xhr) {
                        submitBtn.prop('disabled', false).html(originalHtml);
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error, 'Error!');
                        } else {
                            toastr.error("Something went wrong", "Error!");
                        }
                    });
                });
            });
        </script>

    @endpush

@endsection