@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush


@section('content')
    <style>
        .label-new {
            background: #ffc107;
            padding: 6px;
            border-radius: 5px;
            color: #000;
        }

        .shake-note i {
            animation: shake 1s infinite;
        }

        @keyframes shake {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-2px);
            }

            50% {
                transform: translateX(2px);
            }

            75% {
                transform: translateX(-2px);
            }

            100% {
                transform: translateX(0);
            }
        }

        .form-control {
            min-width: 100%;

        }
    </style>

    <input type="hidden" id="source_id" value="{{ $id }}">

    <div class="main-right">
        <div class="right-side submanager completed-leads closed-leads">

            <div class="row">
                <div class="row align-items-center mb-3">
                    <div class="col-md-8">
                        <h2 class="mb-0">Leads Listing - {{ $source->source_name }}</h2>

                    </div>

                    <div class="col-md-4 text-end">
                        <button type="button" class="btn return-btn"
                            onclick="window.history.back() || (window.location.href='{{ route('sources.employeecampaign') }}');">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </button>
                    </div>
                </div>

            </div>

            <div class="graph campaignslist campaignleadlisting">

                <!-- Filters -->
                <!-- <div class="row">
                                                                    <div class="add-submanager">
                                                                        <input type="search" id="global_filter" name="search" placeholder="search...">
                                                                    </div>

                                                                </div> -->
                <div class="filter-row" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">

                    <!-- Company Filter -->
                    <select id="company_s" class="filter-select">
                        <option value="">Company</option>
                        @foreach($comapnyName as $c)
                            <option value="{{ $c['company_name'] }}">{{ $c['company_name'] }}</option>
                        @endforeach
                    </select>

                    <!-- Timezone -->
                    <select id="company_time" class="filter-select">
                        <option value="">Time Zone</option>
                        @foreach($timeZone as $t)
                            <option value="{{ $t['timezone'] }}">{{ $t['timezone'] }}</option>
                        @endforeach
                    </select>

                    <button id="reset_filters" class="btn btn-secondary">
                        <i class="fa fa-rotate-left"></i> Reset
                    </button>

                </div>



                <!-- Datatable -->
                <div class="table">
                    <div class="table-container">
                        <table id="employee-table">
                            <thead class="thead-main">
                                <tr>
                                    <th>Company Name</th>
                                    <th>Prospect Name</th>
                                    <th>Time Zone </th>
                                    <th>Designation</th>
                                    <th>Phone No.</th>
                                    <th>Phone No. 2</th>
                                    <th>Note Status</th>
                                    <th>Last Updated Note</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- =================================================================== -->
    <!--                        QUICK NOTE MODAL                             -->
    <!-- =================================================================== -->
    <div id="status-modal-quicknote" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <meta name="csrf-token" content="{{ csrf_token() }}" />

                <div class="modal-header">
                    <h4 class="modal-title">Add Quick Note</h4>
                    <button type="button" class="close modal-close" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <input type="radio" name="conversation_type" value="NoResponse" checked> VM / No Response
                        &nbsp;&nbsp;
                        <input type="radio" name="conversation_type" value="Conversation"> Conversation
                    </div>

                    <!-- Reminder Fields -->
                    <div class="form-group">
                        <div id="conversation_type_container" style="display:none;">
                            <div class="row">
                                <div class="col-12 parent-col">
                                    <div class="form-group mb-2">
                                        <label style="font-weight: 600;">Conversation Type</label>
                                        <select id="parent_reminder_for" class="form-control"
                                            onchange="handleParentCategoryChange(this, 'reminder_for', checktype);">
                                            <option value="">select conversation</option>
                                            <option value="Follow up">Follow up</option>
                                            <option value="Declined">Declined</option>
                                            <option value="Meeting Setup">Meeting Setup</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6 child-col" id="reminder_for_child_col" style="display:none;">
                                    <div class="form-group mb-2">
                                        <label style="font-weight: 600;">Sub Conversation Type</label>
                                        <select id="reminder_for" class="form-control" onchange="checktype();">
                                            <option value="">Choose Option</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="reminderdatetime">
                            <label>Reminder Date</label>
                            <input type="date" id="min-date" class="form-control">

                            <label>Reminder Time</label>
                            <input type="time" id="reminder_time" class="form-control">
                        </div>

                        <div id="callbackdatetime" style="display:none;">
                            <label>Callback Date</label>
                            <input type="date" id="callback_date" class="form-control">

                            <label>Callback Time</label>
                            <input type="time" id="callback_time" class="form-control">
                        </div>

                        <label>Phone Number</label>
                        <input type="tel" id="phone_number" class="form-control" placeholder="Phone Number">

                        <label>Note</label>
                        <textarea id="feedback" class="form-control" style="min-height:130px;"></textarea>

                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" id="lead_id_quick_note">
                    <button class="btn btn-default modal-close" data-dismiss="modal">Close</button>
                    <button class="btn btn-info" id="save-data-quick-note">Add Note</button>
                </div>

            </div>
        </div>
    </div>


    <!-- =================================================================== -->
    <!--                      ADD NOTE AFTER DIAL MODAL                      -->
    <!-- =================================================================== -->
    <div id="status-modal-dialnote" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <meta name="csrf-token" content="{{ csrf_token() }}" />

                <div class="modal-header">
                    <h4 class="modal-title">Add Note after Dial</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label for="dial_conversation_type">Call Outcome</label>
                        <select name="dial_conversation_type" id="dial_conversation_type" class="form-control">
                            <option value="NoResponse">VM / No Response</option>
                            <option value="Conversation">Connected</option>
                        </select>
                    </div>

                    <!-- Fields -->
                    <div class="form-group">
                        <div id="dial_conversation_type_container" style="display:none;">
                            <div class="row">
                                <div class="col-12 parent-col">
                                    <div class="form-group mb-2">
                                        <label style="font-weight: 600;">Conversation Type <span
                                                class="text-danger">*</span></label>
                                        <select id="dial_parent_reminder_for" class="form-control"
                                            onchange="handleParentCategoryChange(this, 'dial_reminder_for', checkdialtype);">
                                            <option value="">select conversation</option>
                                            <option value="Follow up">Follow up</option>
                                            <option value="Declined">Declined</option>
                                            <option value="Meeting Setup">Meeting Setup</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6 child-col" id="dial_reminder_for_child_col"
                                    style="display:none;">
                                    <div class="form-group mb-2">
                                        <label style="font-weight: 600;">Sub Conversation Type <span
                                                class="text-danger">*</span></label>
                                        <select id="dial_reminder_for" class="form-control" onchange="checkdialtype();">
                                            <option value="">Choose Option</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="dial_reminderdatetime" style="display:none;">
                            <label>Reminder Date</label>
                            <input type="date" id="dial_min-date" class="form-control">

                            <label>Reminder Time</label>
                            <input type="time" id="dial_reminder_time" class="form-control">
                        </div>

                        <div id="dial_callbackdatetime" style="display:none;">
                            <label>Callback Date</label>
                            <input type="date" id="dial_callback_date" class="form-control">

                            <label>Callback Time</label>
                            <input type="time" id="dial_callback_time" class="form-control">
                        </div>

                        <div id="dial_phone_number_container" style="display:none;">
                            <label>Phone Number</label>
                            <input type="tel" id="dial_phone_number" class="form-control" placeholder="Phone Number">
                        </div>

                        <label>Note</label>
                        <textarea id="dial_feedback" class="form-control" style="min-height:130px;"></textarea>

                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" id="lead_id_dial_note">
                    <button class="btn btn-info" id="save-data-dialnote">Submit</button>
                </div>

            </div>
        </div>
    </div>




    <!-- =================================================================== -->
    <!--                        STATUS CHANGE MODAL                           -->
    <!-- =================================================================== -->
    <div id="status-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="ajaxform">

                    <meta name="csrf-token" content="{{ csrf_token() }}" />

                    <div class="modal-header">
                        <h4 class="modal-title">Change Status</h4>
                        <button type="button" class="close close-status-modal" data-dismiss="modal">×</button>
                    </div>

                    <div class="modal-body">

                        <label>Select Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Select Status</option>
                            <option value="4">In progress</option>
                            <option value="3">Closed</option>
                            <option value="2">Failed</option>
                        </select>

                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <input type="hidden" id="lead_id">
                        <button class="btn btn-default close-status-modal" data-dismiss="modal">Close</button>
                        <button id="save-data" type="button" class="btn btn-info">Save changes</button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="numberModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #192e62; color: #fff; padding: 10px 15px;">
                    <h6 class="modal-title">Contact Numbers</h6>
                    <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 1;"
                        onclick="closemodal();">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="numberRow"
                        style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; align-items: center;">
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- =================================================================== -->
    <!--                       VIEW NOTES MODAL                              -->
    <!-- =================================================================== -->
    <div class="modal fade" id="largeModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">View Notes</h4>
                    <button class="close largemodal-close" data-dismiss="modal" onclick="closemodal()">×</button>
                </div>

                <div class="modal-body">
                    <div id="notes_data" class="table-responsive"></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success largemodal-close" data-dismiss="modal"
                        onclick="closemodal()">Close</button>
                </div>

            </div>
        </div>
    </div>




@endsection




@push('scripts')

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#spinner-overlay').show(); // Show full-page spinner

            var sourceId = $('#source_id').val();
            var url = "{{ url('campaign/camp_assign_emp') }}/" + sourceId;

            var table = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                searching: true, // Enable search globally
                ajax: {
                    url: url,
                    data: function (d) {
                        d.timeZone = $('#company_time :selected').val();
                        d.cName = $('#company_s :selected').val();
                        d.orderBy = $('#note_time :selected').val();

                    },
                    error: function (xhr, error, thrown) {
                        console.error("AJAX Error:", xhr.responseText);
                    }
                },
                columns: [
                    { data: 'company_name', name: 'company_name', orderable: true, searchable: true },
                    { data: 'prospect_first_name', name: 'prospect_first_name' },
                    { data: 'timezone', name: 'timezone', orderable: false },
                    { data: 'designation', name: 'designation', orderable: false },
                    { data: 'contact_number_1', name: 'contact_number_1', orderable: false },
                    { data: 'contact_number_2', name: 'contact_number_2', orderable: false },
                    { data: 'note_status', name: 'note_status', orderable: false, searchable: false },
                    { data: 'update_note_date', name: 'update_note_date' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },

                ],
                drawCallback: function () {

                    tippy('.viewnotes', {
                        content: 'View Notes',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.addnotes', {
                        content: 'Add Note',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.changestatus', {
                        content: 'Change Status',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.editLeads', {
                        content: 'Edit Leads',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.deleteLeads', {
                        content: 'Delete Leads',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                },
                initComplete: function () {
                    var api = this.api();
                    var searchInput = $('div.dataTables_filter input');

                    // Style the label container to align items nicely
                    $('div.dataTables_filter label').css({
                        'display': 'inline-flex',
                        'align-items': 'center',
                        'gap': '8px'
                    });

                    // Set placeholder, width, and style the search input
                    searchInput.attr('placeholder', 'Search by company, prospect, designation')
                        .css({
                            'width': '250px',
                            'display': 'inline-block',
                            'height': '32px',
                            'margin': '0'
                        });

                    $('#spinner-overlay').hide();
                },
                rawColumns: ['action', 'note_status'] // Ensure HTML is rendered in the actions and note_status column
            });

            // Show spinner overlay on processing start
            table.on('preXhr.dt', function (e, settings, data) {
                $('#spinner-overlay').show(); // Show full-page spinner
            });

            // Hide spinner overlay when data is loaded
            table.on('xhr.dt', function (e, settings, json, xhr) {
                $('#spinner-overlay').hide(); // Hide full-page spinner
            });

            // Ensure filters work by putting event listener inside $(document).ready()
            $('#company_time, #company_s, #note_time').on('change', function () {
                console.log('Filter changed! Reloading table...');
                table.ajax.reload(); // Reload DataTable with new filter values
            });

            $('#global_filter').on('keyup', function () {

                table.ajax.reload(); // Reload DataTable with new filter values
            });
        });

    </script>

    <script>



        // ======================================================
        //  OPEN QUICK NOTE MODAL
        // ======================================================
        function showaddmodal(id) {
            $('#lead_id_quick_note').val(id);

            // Default radio button to VM/No Response
            $('input[name="conversation_type"][value="NoResponse"]').prop('checked', true);

            $('#min-date').val('');
            $('#reminder_time').val('');
            $('#parent_reminder_for').val('');
            $('#reminder_for').empty().append('<option value="">Choose Option</option>');
            $('#reminder_for_child_col').hide();
            $('#feedback').val('VM/No Response');
            $('#phone_number').val('');
            $('#callback_date').val('');
            $('#callback_time').val('');

            // Hide Conversation Type dropdown, reset datetime fields
            $('#conversation_type_container').hide();
            $('#reminderdatetime').show();
            $('#callbackdatetime').hide();

            $('#status-modal-quicknote').modal('show');
        }

        function checktype() {
            var remindfor = $('#reminder_for').val();
            if (remindfor == 'Callback') {
                $('#reminderdatetime').css('display', 'none');
                $('#callbackdatetime').css('display', 'block');
            } else {
                $('#reminderdatetime').css('display', 'block');
                $('#callbackdatetime').css('display', 'none');
            }
        }

        $(document).ready(function () {
            $('#feedback').val('VM/No Response');

            $('input[type=radio][name=conversation_type]').change(function () {
                if (this.value == 'NoResponse') {
                    $('#feedback').val('VM/No Response');
                    $('#min-date').val("");
                    $('#parent_reminder_for').val("");
                    $('#reminder_for').empty().append('<option value="">Choose Option</option>');
                    $('#reminder_for_child_col').hide();
                    $('#reminder_time').val("");
                    $('#phone_number').val('');
                    $('#callback_date').val('');
                    $('#callback_time').val('');

                    // Hide Conversation Type dropdown, show standard reminder fields
                    $('#conversation_type_container').hide();
                    $('#reminderdatetime').show();
                    $('#callbackdatetime').hide();
                } else if (this.value == 'Conversation') {
                    $('#feedback').val('');
                    $('#phone_number').val('');
                    $('#min-date').val("");
                    $('#parent_reminder_for').val("");
                    $('#reminder_for').empty().append('<option value="">Choose Option</option>');
                    $('#reminder_for_child_col').hide();
                    $('#reminder_time').val("");
                    $('#callback_date').val('');
                    $('#callback_time').val('');

                    // Show Conversation Type dropdown
                    $('#conversation_type_container').show();
                    checktype();
                }
            });
        });


        // ======================================================
        //  SAVE QUICK NOTE
        // ======================================================
        $('#save-data-quick-note').click(function () {

            let lead_id = $('#lead_id_quick_note').val();
            let source_id = $('#source_id').val();
            let reminder_date = $('#min-date').val();
            let reminder_time = $('#reminder_time').val();
            let reminder_for = $('#reminder_for').val();
            let feedback = $('#feedback').val();
            let phone_number = $('#phone_number').val();
            let callback_date = $('#callback_date').val();
            let callback_time = $('#callback_time').val();
            let type = $('input[name=conversation_type]:checked').val();

            let _token = $('meta[name="csrf-token"]').attr('content');

            $.post("{{ url('leads/add_note') }}", {
                lead_id, source_id, reminder_date, reminder_time, reminder_for, feedback, type, phone_number, callback_date, callback_time, _token
            }, function (res) {

                if (res.success) {
                    toastr.success(res.success);
                    $('#status-modal-quicknote').modal('hide');
                } else {
                    toastr.error("Something went wrong");
                }

            }).fail(function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    toastr.error(xhr.responseJSON.error, 'Error!');
                } else {
                    toastr.error("Something went wrong", "Error!");
                }
            });

        });


        // ======================================================
        //  OPEN DIAL NOTE MODAL
        // ======================================================
        function showdialnoteaddmodal(id, phone) {
            window.dialnote_submitted = false;
            $('#lead_id_dial_note').val(id);

            // Save pending note state to localStorage
            localStorage.setItem('pending_dial_note', JSON.stringify({ lead_id: id, phone: phone }));

            // Default dropdown to VM/No Response
            $('#dial_conversation_type').val('NoResponse');

            $('#dial_min-date').val('');
            $('#dial_reminder_time').val('');
            $('#dial_parent_reminder_for').val('');
            $('#dial_reminder_for').empty().append('<option value="">Choose Option</option>');
            $('#dial_reminder_for_child_col').hide();
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
                    $('#dial_parent_reminder_for').val("");
                    $('#dial_reminder_for').empty().append('<option value="">Choose Option</option>');
                    $('#dial_reminder_for_child_col').hide();
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
                    $('#dial_parent_reminder_for').val("");
                    $('#dial_reminder_for').empty().append('<option value="">Choose Option</option>');
                    $('#dial_reminder_for_child_col').hide();
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
        });


        // ======================================================
        //  SAVE DIAL QUICK NOTE
        // ======================================================
        $('#save-data-dialnote').off('click').on('click', function (event) {
            event.preventDefault();
            let submitBtn = $('#save-data-dialnote');
            let originalHtml = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

            let lead_id = $('#lead_id_dial_note').val();
            let source_id = $('#source_id').val();
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
                    // Reload DataTable to reflect note updates reactively without refreshing the page
                    $('#employee-table').DataTable().ajax.reload(null, false);
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


        // ======================================================
        //  OPEN STATUS CHANGE MODAL
        // ======================================================
        function showstatusmodal(id) {
            $('#lead_id').val(id);
            $('#status-modal .print-error-msg').hide();
            $('#status-modal .print-error-msg ul').html('');
            $('#status').val('');
            $('#status-modal').modal('show');
        }


        // ======================================================
        //  SAVE STATUS
        // ======================================================
        $('#save-data').click(function () {

            let lead_id = $('#lead_id').val();
            let status = $('#status').val();
            let _token = $('meta[name="csrf-token"]').attr('content');

            $.post("{{ url('changeStatus') }}", { lead_id, status, _token }, function (res) {

                if (res.success) {
                    toastr.success(res.success);
                    $('#status-modal').modal('hide');
                    location.reload();
                } else {
                    if (res.lhs_link) {
                        $('#status-modal .print-error-msg').show();
                        $('#status-modal .print-error-msg ul').html(res.lhs_link);
                    } else {
                        $('#status-modal .print-error-msg').show();
                        $('#status-modal .print-error-msg ul').html('<li>' + res.error + '</li>');
                    }
                    toastr.error(res.error);
                }

            });

        });


        // ======================================================
        //  VIEW NOTES
        // ======================================================
        function shownoteslist(id) {
            $('#spinner-overlay').show();
            $.get("{{ url('leads/notes_view') }}/" + id, function (res) {
                $('#notes_data').html(res.table);
                $('#largeModal').modal('show');
            }).fail(function () {
                toastr.error('Something went wrong', 'Error!');
            }).always(function () {
                $('#spinner-overlay').hide();
            });
        }



        function deleteLead(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform the AJAX request to delete the manager
                    $.ajax({
                        url: `delete/${id}`, // Adjust this URL to match your route
                        type: 'GET', // Use GET request for deletion
                        success: function (response) {
                            // Handle successful response (e.g., show a success message)
                            Swal.fire(
                                'Deleted!',
                                'The lead has been deleted.',
                                'success'
                            );

                            // Redraw the DataTable to reflect the changes
                            $('#employee-table').DataTable()
                                .draw(); // Redraw the DataTable
                        },
                        error: function (xhr, status, error) {
                            // Handle error (e.g., show an error message)
                            Swal.fire(
                                'Error!',
                                'There was an issue deleting the lead.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        $('#reset_filters').on('click', function () {

            // Reset dropdowns
            $('#company_s').val('');
            $('#company_time').val('');
            $('#note_time').val('');

            // Reset search
            $('#global_filter').val('');

            location.reload(true);
        });

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

        $('.modal-close').on('click', function (event) {
            $('#status-modal-quicknote').modal('hide');
        });

        $('.largemodal-close').on('click', function (event) {
            $('#largeModal').modal('hide');
        });

    </script>

@endpush