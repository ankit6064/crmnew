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

        /*-15-08-2026-*/
        .right-side.submanager.completed-leads input#daterange, select#campaign_name, select#company_s, select#closedon, select#company_time {
            min-width: 100% !important;
        }
        .right-side.submanager.completed-leads input#daterange {
            background: transparent;
            box-shadow: 0px -1px 9px #dddddd70;
        }
        .right-side.submanager.completed-leads .filter-actions button {
        width: 100%;
        }
        .right-side.submanager.completed-leads button#export_csv {
        background: #0d3a6b;
        }
        .right-side.submanager.completed-leads table tr td:first-child a {
            color: #000;
            margin-bottom: 0;
            padding-bottom: 0;
            padding-top: 0;
            padding-right: 0;
        }
        .right-side.submanager.completed-leads button#export_csv:hover {
            color: #fff;
        }
        .right-side.submanager.completed-leads table tr td:first-child {
            display:flex;
        }
        .right-side.submanager.completed-leads tr td:nth-child(5) {
            display: table-cell !important;
        }
        .right-side.submanager.completed-leads tr td:nth-child(6) {
           display: flex;
        }
        .right-side.submanager.completed-leads tr td:nth-child(6) a {
            color: #000;
            padding-left: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
        }
        .right-side.submanager.completed-leads tr td:nth-child(6) i.fa-brands.fa-linkedin {
            color: #0A66C2;
            font-size: 20px;
        }
        .right-side.submanager.completed-leads tr td:nth-child(4) {
            text-align: left;
        }
        .right-side.submanager.completed-leads tr td:nth-child(6) a:hover {
            background: none;
        }

        .right-side.submanager.completed-leads thead.thead-main tr th:nth-child(6) {
            text-align: left;
        }
        .right-side.submanager.completed-leads tr td:nth-child(8) {
            text-align: left;
        }
        .right-side.submanager.completed-leads span.badge {
            padding: 8px 10px;
        }
    </style>
@endpush

@section('content')

    <input type="hidden" id="source_id" value="{{ $id }}">

    <div class="main-right">
        <div class="right-side submanager completed-leads closed-leads">

            <h2>Completed Leads</h2>

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
                        grid-template-columns: repeat(3, 1fr);
                        gap: 20px;
                        align-items: end;
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
                            <label for="closedon">Completed On</label>
                            <select id="closedon" class="filter-select">
                                <option value="">Select Date</option>
                                @foreach($closedon as $d)
                                    <option value="{{ $d['date'] }}">{{ date('d/m/Y', strtotime($d['date'])) }}</option>
                                @endforeach
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
                                    <input type="radio" name="conversation_type" value="NoResponse" checked> VM / No
                                    Response
                                    &nbsp;&nbsp;
                                    <input type="radio" name="conversation_type" value="Conversation"> Conversation
                                </div>

                                <!-- Reminder Fields -->
                                <div class="form-group">
                                    <label>Reminder Date</label>
                                    <input type="date" id="min-date" class="form-control">

                                    <label>Reminder Time</label>
                                    <input type="time" id="reminder_time" class="form-control">

                                    <div id="conversation_type_container" style="display:none;">
                                        <label>Conversation Type</label>
                                        <select id="reminder_for" class="form-control">
                                            <option value="">Choose Option</option>
                                            <option value="Declined">Declined</option>
                                            <option value="DNC">DNC</option>
                                            <option value="Follow-up Call">Follow-up Call</option>
                                            <option value="Follow-up Email/Info Requested">Follow-up Email/Info Requested
                                            </option>
                                            <option value="Meeting Set-up">Meeting Set-up</option>
                                            <option value="Not Interested">Not Interested</option>
                                            <option value="Not Right Party">Not Right Party</option>
                                            <option value="Reference Shared">Reference Shared</option>
                                        </select>
                                    </div>

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
                                <button class="close largemodal-close" data-dismiss="modal">×</button>
                            </div>

                            <div class="modal-body">
                                <div id="notes_data" class="table-responsive"></div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-success largemodal-close" data-dismiss="modal">Close</button>
                            </div>

                        </div>
                    </div>
                </div>


                <!-- Datatable -->
                <div class="table">
                    <div class="table-container">
                        <div class="table-container-inner">

                            <table id="employee-table">
                                <thead class="thead-main">
                                    <tr>
                                        <th>Actions</th>
                                        <th>Campaign Name</th>
                                        <th>Sub Campaign Name</th>
                                        <th>Company Name</th>
                                        <th>Closed By</th>
                                        <th>Prospect Name</th>
                                        <th>Time Zone</th>
                                        <th>Designation</th>
                                        <th>Email Id</th>
                                        <th>Phone Number</th>
                                        <th>Completed On</th>
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
    @push('scripts')
        <!-- Moment.js and DateRangePicker CSS/JS -->
        <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#spinner-overlay').show(); // Show full-page spinner

                var sourceId = $('#source_id').val();
                var url = "{{ url('leads/employeecompletedleads') }}/";

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
                            d.timeZone = $('#company_time').val();
                            d.cName = $('#company_s').val();
                            d.campaign_name = $('#campaign_name').val();
                            d.closedon = $('#closedon').val();

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
                        { data: 'action', orderable: false, searchable: false },
                        { data: 'source_name', name: 'sources.source_name', orderable: true, searchable: false },
                        { data: 'description', name: 'sources.description', orderable: true },
                        { data: 'company_name', name: 'leads.company_name', orderable: true },
                        { data: 'completed_by', orderable: false },
                        { data: 'prospect_first_name_new', name: 'prospect_first_name', orderable: false },
                        { data: 'timezone', orderable: false },
                        { data: 'designation', orderable: false },
                        { data: 'prospect_email', orderable: false },
                        { data: 'contact_number_1', orderable: false },
                        { data: 'updated_at_new', orderable: true }
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

                // Ensure filters work
                $('#company_time, #company_s, #campaign_name, #closedon').on('change keyup', function () {
                    $('#spinner-overlay').show();
                    table.ajax.reload();
                });

                // Reset filters click handler
                $('#reset_filters').on('click', function () {
                    $('#campaign_name').val('');
                    $('#company_s').val('');
                    $('#company_time').val('');
                    $('#closedon').val('');
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

                    var date_from = '';
                    var date_to = '';
                    var drp = $('#daterange').data('daterangepicker');
                    if (drp && $('#daterange').val() !== '') {
                        date_from = drp.startDate.format('YYYY-MM-DD');
                        date_to = drp.endDate.format('YYYY-MM-DD');
                    }

                    var search = table.search() || '';

                    var exportUrl = "{{ route('employeecompletedleads.export_csv') }}" +
                        "?campaign_name=" + encodeURIComponent(campaign_name) +
                        "&cName=" + encodeURIComponent(cName) +
                        "&timeZone=" + encodeURIComponent(timeZone) +
                        "&closedon=" + encodeURIComponent(closedon) +
                        "&date_from=" + encodeURIComponent(date_from) +
                        "&date_to=" + encodeURIComponent(date_to) +
                        "&search=" + encodeURIComponent(search);

                    window.location.href = exportUrl;
                });
            });

        </script>

        <script>
            /* ============================ */
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
                    $('ul.custom_text-1').html('<li class="error_list"><span class="tab">Conversation Type Cannot Be Empty!</span></li>');
                } else if (feedback == 0) {
                    $('.alert.alert-danger.print-error-msg-1').hide();
                    $('.alert.alert-danger.print-error-msg').show();
                    $('ul.custom_text').html('<li class="error_list"><span class="tab">Note Field Cannot Be Empty!</span></li>');
                } else {
                    $('.alert.alert-danger.print-error-msg').hide();
                    $('ul.custom_text').html('');
                    let feedback = $("[name=feedback]").val();
                    let reminder_date = $("[name=reminder_date]").val();
                    let reminder_time = $("[name=reminder_time]").val();
                    let source_id = $("[name=source_id]").val();
                    console.log(reminder_time);
                    let reminder_for = $("[name=reminder_for]").val();
                    let lead_id = $("input[name=lead_id_quick_note]").val();
                    let _token = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: '{{url("leads/add_note")}}',
                        type: "POST",
                        data: {
                            source_id: source_id,
                            reminder_date: reminder_date,
                            reminder_time: reminder_time,
                            reminder_for: reminder_for,
                            lead_id: lead_id,
                            feedback: feedback,
                            _token: _token
                        },
                        success: function (response) {
                            if ($.isEmptyObject(response.error)) {
                                console.log(response);
                                toastr.success(response.success, 'Success!');
                                $('#feedback').val('');
                                $('#min-date').val("");
                                $('#reminder_for').val("");
                                $('#reminder_time').val("");
                                $('.alert.alert-danger.print-error-msg-1').hide();
                                $('.alert.alert-danger.print-error-msg').hide();
                                $("#NoResponse").trigger("click");
                                $("#modelclose").trigger("click");
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

                function printErrorMsg(msg) {
                    console.log(msg);
                    $(".print-error-msg").find("ul").html('');
                    $(".print-error-msg").css('display', 'block');
                    $(".print-error-msg").find("ul").append('<li>' + msg + '</li>');
                }
            });

            function shownoteslist(lead_id) {
                var url = '{{url("leads/notes_view")}}';
                var full_url = url + '/' + lead_id;
                $('#spinner-overlay').show();
                $.ajax({
                    url: full_url,
                    type: "GET",
                    data: {
                        lead_id: lead_id
                    },
                    success: function (response) {
                        $('#largeModal').modal('show');
                        if ($.isEmptyObject(response.error)) {
                            console.log(response.notes_data);
                            console.log(response.table);
                            $("#notes_data").html('');
                            $("#notes_data").html(response.table);
                        } else {
                            toastr.error(response.error, 'Error!');
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong', 'Error!');
                    },
                    complete: function() {
                        $('#spinner-overlay').hide();
                    }
                });
            }




            $(document).ready(function () {
                $('#feedback').text('VM/No Response');
                $('input[type=radio][name=conversation_type]').change(function () {
                    if (this.value == 'NoResponse') {
                        $('#feedback').val('VM/No Response');
                        $('#min-date').val("");
                        $('#reminder_for').val("");
                        $('#reminder_time').val("");
                        $('.alert.alert-danger.print-error-msg-1').hide();
                        $('.alert.alert-danger.print-error-msg').hide();
                        $('#conversation_type_container').hide();
                    } else if (this.value == 'Conversation') {
                        $('.alert.alert-danger.print-error-msg-1').hide();
                        $('.alert.alert-danger.print-error-msg').hide();
                        $('#feedback').val('');
                        $('#min-date').val("");
                        $('#reminder_for').val("");
                        $('#reminder_time').val("");
                        $('#conversation_type_container').show();
                    }
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
                $('#conversation_type_container').hide();
                $('#status-modal-quicknote').modal('show');
            }

            $('.modal-close').on('click', function (event) {
                $('#status-modal-quicknote').modal('hide');
            });

            $('.largemodal-close').on('click', function (event) {
                $('#largeModal').modal('hide');
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


            $(document).ready(function () {

                $("#save-data").click(function (event) {
                    event.preventDefault();

                    let status = $("select[name=status]").val();
                    let lead_id = $("#lead_id").val();
                    let _token = $('meta[name="csrf-token"]').attr('content');


                    //alert(status+'--lead='+lead_id+'--token='+_token);

                    $.ajax({
                        url: '{{url("changeStatus")}}',
                        type: "POST",
                        data: {
                            lead_id: lead_id,
                            status: status,
                            _token: _token
                        },
                        success: function (response) {

                            //console.log(response);

                            if ($.isEmptyObject(response.error)) {
                                console.log(response);
                                toastr.success(response.success, 'Success!')
                                if (response) {
                                    $(".print-error-msg").css('display', 'none');
                                    $('.success').text(response.success);
                                    if (response.status == 'failed') {
                                        var Current_url = base_url + "/leads/failed";
                                        window.location.href = Current_url;
                                    } else if (response.status == 'close') {
                                        var Current_url = base_url + "/leads/closed";
                                        window.location.href = Current_url;
                                    } else {
                                        // var  Current_url = base_url+"/leads/closed";
                                        //  window.location.href = Current_url;
                                        location.reload(true); // inprogress
                                    }
                                    //location.reload(true);
                                    $("#ajaxform")[0].reset();
                                    //}else{
                                    // printErrorMsg(response.error);
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


                    function printErrorMsg(msg) {
                        console.log(msg);
                        $(".print-error-msg").find("ul").html('');
                        $(".print-error-msg").css('display', 'block');
                        //$.each( msg, function( key, value ) {
                        $(".print-error-msg").find("ul").append('<li>' + msg + '</li>');
                        // });
                    }


                });

            });




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

                numList.forEach(function (num) {
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

        </script>


    @endpush

@endsection