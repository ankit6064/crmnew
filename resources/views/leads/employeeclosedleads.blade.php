@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Custom styles for daterangepicker to match the premium design exactly */
        .daterangepicker {
            font-family: 'Poppins', sans-serif !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.08) !important;
            border-radius: 10px !important;
            padding: 10px !important;
            margin-top: 5px !important;
        }

        .daterangepicker .calendar-table th,
        .daterangepicker .calendar-table td {
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
            height: 32px !important;
            width: 32px !important;
            line-height: 32px !important;
            border-radius: 4px !important;
            color: #475569 !important;
        }

        .daterangepicker td.off,
        .daterangepicker td.off.in-range,
        .daterangepicker td.off.start-date,
        .daterangepicker td.off.end-date {
            color: #cbd5e1 !important;
            background-color: transparent !important;
        }

        .daterangepicker td.available:hover,
        .daterangepicker th.available:hover {
            background-color: #f1f5f9 !important;
        }

        .daterangepicker td.active,
        .daterangepicker td.active:hover {
            background-color: #3b82f6 !important;
            /* Blue background color from screenshot */
            color: #ffffff !important;
            border-radius: 50% !important;
            /* Circular active day */
        }

        .daterangepicker td.in-range {
            background-color: #eff6ff !important;
            color: #3b82f6 !important;
        }

        .daterangepicker .drp-buttons {
            border-top: 1px solid #f1f5f9 !important;
            padding: 12px 10px 6px 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 15px !important;
        }

        .daterangepicker .drp-selected {
            font-size: 13px !important;
            color: #64748b !important;
            font-weight: 500 !important;
            margin-right: auto !important;
        }

        .daterangepicker .cancelBtn {
            background: none !important;
            border: none !important;
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            padding: 0 !important;
            cursor: pointer !important;
            box-shadow: none !important;
        }

        .daterangepicker .cancelBtn:hover {
            color: #0f172a !important;
            text-decoration: underline !important;
        }

        .daterangepicker .applyBtn {
            background-color: #4b3fb3 !important;
            /* Dark Purple/Indigo background from screenshot */
            border: none !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            padding: 8px 18px !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            transition: all 0.2s ease !important;
        }

        .daterangepicker .applyBtn:hover {
            background-color: #3c3293 !important;
        }
    </style>
@endpush


@section('content')

    <input type="hidden" id="source_id" value="{{ $id }}">

    <div class="main-right">
        <div class="right-side submanager completed-leads closed-leads">

            <h2>Closed Leads</h2>

            <div class="graph campaignslist">

                <!-- Filters -->
                <!-- <div class="row">
                                                <div class="add-submanager">
                                                    <input type="search" id="global_filter" name="search" placeholder="search...">
                                                </div>

                                            </div> -->
                <style>
                    .filter-card {
                        background: #ffffff;
                        border-radius: 12px;
                        border: 1px solid #e2e8f0;
                        padding: 24px;
                        margin-bottom: 25px;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
                    }

                    .filter-grid {
                        display: grid;
                        grid-template-columns: repeat(4, 1fr);
                        gap: 20px;
                        align-items: end;
                    }

                    @media (max-width: 1200px) {
                        .filter-grid {
                            grid-template-columns: repeat(3, 1fr);
                        }
                    }

                    @media (max-width: 900px) {
                        .filter-grid {
                            grid-template-columns: repeat(2, 1fr);
                        }
                    }

                    @media (max-width: 600px) {
                        .filter-grid {
                            grid-template-columns: 1fr;
                        }
                    }

                    .filter-group {
                        display: flex;
                        flex-direction: column;
                        gap: 8px;
                    }

                    .filter-group label {
                        font-weight: 600;
                        font-size: 13px;
                        color: #475569;
                        margin-bottom: 0px;
                        font-family: 'Poppins', sans-serif;
                    }

                    .filter-select,
                    .filter-input {
                        width: 100% !important;
                        min-width: unset !important;
                        height: 42px;
                        border-radius: 8px;
                        border: 1px solid #cbd5e1;
                        padding: 0 14px;
                        font-size: 13px;
                        color: #1e293b;
                        background-color: #f8fafc;
                        box-sizing: border-box;
                        transition: all 0.2s ease;
                        font-family: 'Poppins', sans-serif;
                    }

                    .table-container {
                        width: 100% !important;
                        overflow-x: auto !important;
                    }

                    .filter-select:focus,
                    .filter-input:focus {
                        border-color: #4b3fb3;
                        background-color: #ffffff;
                        outline: none;
                        box-shadow: 0 0 0 3px rgba(75, 63, 179, 0.15);
                    }

                    .filter-actions {
                        display: flex;
                        gap: 12px;
                        justify-content: flex-end;
                        align-items: center;
                        height: 42px;
                    }

                    .btn-reset {
                        background-color: #f1f5f9;
                        color: #475569;
                        border: 1px solid #cbd5e1;
                        padding: 0 20px;
                        border-radius: 8px;
                        font-weight: 600;
                        font-family: 'Poppins', sans-serif;
                        height: 42px;
                        font-size: 13px;
                        cursor: pointer;
                        transition: all 0.2s;
                        box-sizing: border-box;
                    }

                    .btn-reset:hover {
                        background-color: #e2e8f0;
                        color: #0f172a;
                        border-color: #94a3b8;
                    }

                    .btn-export {
                        background-color: #1e3a8a;
                        color: white;
                        border: none;
                        padding: 0 20px;
                        border-radius: 8px;
                        font-weight: 600;
                        font-family: 'Poppins', sans-serif;
                        height: 42px;
                        font-size: 13px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                        cursor: pointer;
                        transition: all 0.2s;
                        box-sizing: border-box;
                    }

                    .btn-export:hover {
                        background-color: #172554;
                        transform: translateY(-1px);
                        box-shadow: 0 4px 6px -1px rgba(30, 58, 138, 0.2);
                    }
                </style>

                <div class="filter-card">
                    <div class="filter-grid">
                        <!-- Date Range Filter -->
                        <div class="filter-group daterange-group">
                            <label for="daterange">Date Range</label>
                            <input type="text" id="daterange" class="filter-input" placeholder="Select date range" readonly
                                style="cursor: pointer;">
                        </div>

                        <!-- Campaign Filter -->
                        <div class="filter-group">
                            <label for="campaign_name">Campaign</label>
                            <select id="campaign_name" class="filter-select">
                                <option value="">Select Campaign</option>
                                @foreach($sourceNames as $s)
                                    <option value="{{ $s['source_name'] }}">{{ $s['source_name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Company Filter -->
                        <div class="filter-group">
                            <label for="company_s">Company</label>
                            <select id="company_s" class="filter-select">
                                <option value="">Select Company</option>
                                @foreach($comapnyName as $c)
                                    <option value="{{ $c['company_name'] }}">{{ $c['company_name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Timezone -->
                        <div class="filter-group">
                            <label for="company_time">Time Zone</label>
                            <select id="company_time" class="filter-select">
                                <option value="">Select Time Zone</option>
                                @foreach($timeZone as $t)
                                    <option value="{{ $t['timezone'] }}">{{ $t['timezone'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Closed On -->
                        <div class="filter-group">
                            <label for="closedon">Closed On</label>
                            <select id="closedon" class="filter-select">
                                <option value="">Select Date</option>
                                @foreach($closedon as $d)
                                    <option value="{{ $d['date'] }}">{{ date('d/m/Y', strtotime($d['date'])) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Invitation Date Filter -->
                        <div class="filter-group">
                            <label for="filter_invitation_date">Invitation Date</label>
                            <input type="date" id="filter_invitation_date" class="filter-input" placeholder="Invitation Date">
                        </div>

                        <!-- Confirmation Status Filter -->
                        <div class="filter-group">
                            <label for="filter_confirmation_status">Confirmation Status</label>
                            <select id="filter_confirmation_status" class="filter-select">
                                <option value="">Select Status</option>
                                <option value="sent">Confirmation Sent</option>
                                <option value="waiting">Waiting for Confirmation</option>
                            </select>
                        </div>

                        <!-- Actions Group -->
                        <div class="filter-actions">
                            <button id="reset_filters" class="btn btn-reset">
                                Reset
                            </button>
                            <button id="export_csv" class="btn btn-export">
                                <i class="fa fa-file-csv"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>



                <!-- Datatable -->
                <div class="table">
                    <div class="table-container">
                        <table id="employee-table">
                            <thead class="thead-main">
                                <tr>
                                    <th>Campaign Name</th>
                                    <th>Sub Campaign Name</th>
                                    <th>Company Name</th>
                                    <th>Closed By</th>
                                    <th>Prospect Name</th>
                                    <th>Time Zone</th>
                                    <th>Designation</th>
                                    <th>Email Id</th>
                                    <th>Phone Number</th>
                                    <th>Closed On</th>
                                    <th>Send LHS</th>
                                    <th>Confirmation Status</th>
                                    <th>Reminder Status</th>
                                    <th>Invitation Date</th>
                                    <th>Actions</th>
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
                        <label>Reminder Date</label>
                        <input type="date" id="min-date" class="form-control">

                        <label>Reminder Time</label>
                        <input type="time" id="reminder_time" class="form-control">

                        <label>Conversation Type</label>
                        <select id="reminder_for" class="form-control">
                            <option value="">Choose Option</option>
                            <option value="Declined">Declined</option>
                            <option value="DNC">DNC</option>
                            <option value="Follow-up Call">Follow-up Call</option>
                            <option value="Follow-up Email/Info Requested">Follow-up Email/Info Requested</option>
                            <option value="Meeting Set-up">Meeting Set-up</option>
                            <option value="Not Interested">Not Interested</option>
                            <option value="Not Right Party">Not Right Party</option>
                            <option value="Reference Shared">Reference Shared</option>
                        </select>

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



    <!-- =================================================================== -->
    <!--                        QUICK NOTE MODAL                             -->
    <!-- =================================================================== -->
    <div id="reminder-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <meta name="csrf-token" content="{{ csrf_token() }}" />
                <input type="hidden" name="lead_id" id="reminder_lead_id">

                <div class="modal-header">
                    <h4 class="modal-title">Reminder Status</h4>
                    <button type="button" class="close modal-close" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">

                    <!-- Reminder Fields -->
                    <div class="form-group">
                        <label>Note(optional)</label>
                        <textarea id="reminder_note" class="form-control" style="min-height:130px;"></textarea>

                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" id="lead_id_quick_note">
                    <button class="btn btn-default modal-close" data-dismiss="modal">Close</button>
                    <button class="btn btn-info" id="update-reminder-status">Send Reminder</button>
                </div>

            </div>
        </div>
    </div>


    <!-- =================================================================== -->
    <!--                        INVITATION DATE MODAL                           -->
    <!-- =================================================================== -->
    <div id="invitation-date-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <meta name="csrf-token" content="{{ csrf_token() }}" />
                <input type="hidden" id="invitation_lead_id">
                <div class="modal-header">
                    <h4 class="modal-title">Add Invitation Date & Time</h4>
                    <button type="button" class="close modal-close" data-dismiss="modal" data-bs-dismiss="modal"
                        onclick="closeInvitationModal()">×</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Invitation Date</label>
                        <input type="date" id="invitation_date_input" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Invitation Time</label>
                        <input type="time" id="invitation_time_input" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default modal-close" data-dismiss="modal" data-bs-dismiss="modal"
                        onclick="closeInvitationModal()">Close</button>
                    <button class="btn btn-info" id="save-invitation-date">Save Date & Time</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="numberModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #192e62; color: #fff; padding: 10px 15px;">
                    <h6 class="modal-title">Contact Numbers</h6>
                    <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 1;"
                        onclick="closeNumberModal();">
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
@endsection




@push('scripts')
    <!-- Moment.js and DateRangePicker CSS/JS -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>

        $(document).ready(function () {

            $('#spinner-overlay').show(); // show on first load

            var url = "{{ url('leads/employeeclosedleads') }}/";

            var table = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                searching: true,
                ordering: true,
                order: [[9, 'desc']],
                searchDelay: 500, // smoother search
                ajax: {
                    url: url,
                    data: function (d) {
                        d.campaign_name = $('#campaign_name').val();
                        d.cName = $('#company_s').val();
                        d.timeZone = $('#company_time').val();
                        d.closedon = $('#closedon').val();
                        d.invitation_date = $('#filter_invitation_date').val();
                        d.confirmation_status = $('#filter_confirmation_status').val();

                        // Parse date range
                        var drp = $('#daterange').data('daterangepicker');
                        if (drp && $('#daterange').val() !== '') {
                            d.date_from = drp.startDate.format('YYYY-MM-DD');
                            d.date_to = drp.endDate.format('YYYY-MM-DD');
                        } else {
                            d.date_from = '';
                            d.date_to = '';
                        }
                    }
                },
                columns: [
                    { data: 'source_name', name: 'sources.source_name', orderable: true, searchable: false },
                    { data: 'description', name: 'sources.description', orderable: true }, // FIX HERE
                    { data: 'company_name', name: 'leads.company_name', orderable: true },
                    { data: 'closed_by', orderable: false },
                    { data: 'prospect_first_name_new', name: 'prospect_first_name', orderable: false }, // FIX HERE
                    { data: 'timezone', orderable: false },
                    { data: 'designation', orderable: false },
                    { data: 'prospect_email', orderable: false },
                    { data: 'contact_number_1', orderable: false },
                    { data: 'updated_at_new', orderable: true },
                    { data: 'send_lhs', orderable: false },
                    { data: 'confirmation_status', orderable: false },
                    { data: 'reminder_status', orderable: false },
                    { data: 'invitation_date', orderable: false },

                    { data: 'action', orderable: false, searchable: false }
                ],
                drawCallback: function () {
                    // Initialize Tippy tooltips for the action icons on hover
                    tippy('[data-tippy-content]', {
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                },
                initComplete: function () {
                    $('div.dataTables_filter input')
                        .attr('placeholder', 'Search by company, prospect, designation')
                        .css({ 'width': '250px' });

                    $('#spinner-overlay').hide();
                }
            });

            // Initialize DateRangePicker
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD/MM/YYYY'
                }
            });

            $('#daterange').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                $('#spinner-overlay').show();
                table.ajax.reload();
            });

            $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                $('#spinner-overlay').show();
                table.ajax.reload();
            });

            // 🔥 SHOW loader before every AJAX request
            table.on('preXhr.dt', function () {
                $('#spinner-overlay').show();
            });

            // 🔥 HIDE loader after AJAX response
            table.on('xhr.dt', function () {
                $('#spinner-overlay').hide();
            });

            // 🔥 EXTRA: Show loader immediately when typing
            $(document).on('keyup', 'div.dataTables_filter input', function () {
                $('#spinner-overlay').show();
            });

            // Filters trigger reload + loader
            $('#campaign_name, #company_s, #company_time, #closedon, #filter_invitation_date, #filter_confirmation_status')
                .on('keyup change', function () {
                    $('#spinner-overlay').show();
                    table.ajax.reload();
                });

            // Reset filters click handler
            $('#reset_filters').on('click', function () {
                $('#campaign_name').val('');
                $('#company_s').val('');
                $('#company_time').val('');
                $('#closedon').val('');
                $('#filter_invitation_date').val('');
                $('#filter_confirmation_status').val('');
                $('#daterange').val('');

                var drp = $('#daterange').data('daterangepicker');
                if (drp) {
                    drp.setStartDate(moment());
                    drp.setEndDate(moment());
                }

                $('#spinner-overlay').show();
                table.search('').draw();
                table.ajax.reload();
            });

            // Export CSV handler
            $('#export_csv').on('click', function () {
                var campaign_name = $('#campaign_name').val() || '';
                var cName = $('#company_s').val() || '';
                var timeZone = $('#company_time').val() || '';
                var closedon = $('#closedon').val() || '';
                var invitation_date = $('#filter_invitation_date').val() || '';
                var confirmation_status = $('#filter_confirmation_status').val() || '';

                var date_from = '';
                var date_to = '';
                var drp = $('#daterange').data('daterangepicker');
                if (drp && $('#daterange').val() !== '') {
                    date_from = drp.startDate.format('YYYY-MM-DD');
                    date_to = drp.endDate.format('YYYY-MM-DD');
                }

                var search = table.search() || '';

                var exportUrl = "{{ route('employeeclosedleads.export_csv') }}" +
                    "?campaign_name=" + encodeURIComponent(campaign_name) +
                    "&cName=" + encodeURIComponent(cName) +
                    "&timeZone=" + encodeURIComponent(timeZone) +
                    "&closedon=" + encodeURIComponent(closedon) +
                    "&invitation_date=" + encodeURIComponent(invitation_date) +
                    "&confirmation_status=" + encodeURIComponent(confirmation_status) +
                    "&date_from=" + encodeURIComponent(date_from) +
                    "&date_to=" + encodeURIComponent(date_to) +
                    "&search=" + encodeURIComponent(search);

                window.location.href = exportUrl;
            });

        });


        // ======================================================
        //  OPEN QUICK NOTE MODAL
        // ======================================================
        function showaddmodal(id) {
            $('#lead_id_quick_note').val(id);
            $('#status-modal-quicknote').modal('show');
        }


        // ======================================================
        //  SAVE QUICK NOTE
        // ======================================================
        $('#save-data-quick-note').click(function () {

            let lead_id = $('#lead_id_quick_note').val();
            let reminder_date = $('#min-date').val();
            let reminder_time = $('#reminder_time').val();
            let reminder_for = $('#reminder_for').val();
            let feedback = $('#feedback').val();
            let type = $('input[name=conversation_type]:checked').val();

            let _token = $('meta[name="csrf-token"]').attr('content');

            $.post("{{ url('leads/add_note') }}", {
                lead_id, reminder_date, reminder_time, reminder_for, feedback, type, _token
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
        //  OPEN STATUS CHANGE MODAL
        // ======================================================
        function showstatusmodal(id) {
            $('#lead_id').val(id);
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
                    $('#employee-table').DataTable().ajax.reload(null, false);
                } else {
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
            }).fail(function() {
                toastr.error('Something went wrong', 'Error!');
            }).always(function() {
                $('#spinner-overlay').hide();
            });
        }

        function closemodal() {
            $('#largeModal').modal('hide');

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
                            $('#employee-table').DataTable().ajax.reload(null, false);
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

        function showAllNumbers(numbers) {
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
            
            numList.forEach(function(num) {
                let dialNum = num.replace(/[^0-9+]/g, '');
                rowHtml += '  <tr>';
                rowHtml += '    <td>';
                rowHtml += '      <a href="tel:' + dialNum + '" class="btn btn-xs btn-success" style="border-radius: 50%; padding: 5px 8px; background-color: #28a745; border-color: #28a745;">';
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



        function closeNumberModal() {
            $('#numberModal').modal('hide');

        }


        $(document).on('click', '.send-reminder', function () {
            let id = $(this).data('id');
            $('#reminder_lead_id').val(id);
            $('#reminder-modal').modal('show');
        });


        $(document).on('click', '.send-lhs', function () {
            let id = $(this).data('id');
            let _token = $('meta[name="csrf-token"]').attr('content');

            Swal.fire({
                title: 'Are you sure?',
                text: 'Mark LHS as sent?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Send!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ url('leads/send_lhs') }}", { id, _token }, function (res) {
                        if (res.success) {
                            toastr.success(res.success);
                            $('#employee-table').DataTable().ajax.reload(null, false);
                        } else {
                            toastr.error("Something went wrong");
                        }
                    });
                }
            });
        });

        $(document).on('click', '.send-lhs-reminder', function () {
            let id = $(this).data('id');
            let _token = $('meta[name="csrf-token"]').attr('content');

            Swal.fire({
                title: 'Are you sure?',
                text: 'Send LHS Reminder?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Send Reminder!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ url('leads/update_lhs_reminder_status') }}", { id, _token }, function (res) {
                        if (res.success) {
                            toastr.success(res.success);
                            $('#employee-table').DataTable().ajax.reload(null, false);
                        } else {
                            toastr.error("Something went wrong");
                        }
                    });
                }
            });
        });
        $(document).on('click', '.add-invitation-date', function () {
            let id = $(this).data('id');
            $('#invitation_lead_id').val(id);
            $('#invitation-date-modal').modal('show');
        });

        $('#save-invitation-date').click(function () {
            let id = $('#invitation_lead_id').val();
            let invitation_date = $('#invitation_date_input').val();
            let invitation_time = $('#invitation_time_input').val();
            let _token = $('meta[name="csrf-token"]').attr('content');

            if (!invitation_date || !invitation_time) {
                toastr.error("Please select both date and time");
                return;
            }

            $.post("{{ url('leads/update_invitation_date') }}", { id, invitation_date, invitation_time, _token }, function (res) {
                if (res.success) {
                    toastr.success(res.success);
                    closeInvitationModal();
                    $('#employee-table').DataTable().ajax.reload(null, false);
                } else {
                    toastr.error("Something went wrong");
                }
            });
        });

        function closeInvitationModal() {
            $('#invitation-date-modal').modal('hide');
            $('#invitation_date_input').val('');
            $('#invitation_time_input').val('');
        }

    </script>

@endpush