


@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        div#employee-table_wrapper {
    padding: 0px !important;
    background:white;
}
   
        .search-lead-page h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 22px;
            color: #1e293b;
            margin-bottom: 20px;
        }

        /* Reset global .graph box styles so custom card styling takes effect */
        .search-lead-page .graph.campaignslist {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

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

        .filter-select {
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

        .filter-select:focus {
            border-color: #4b3fb3;
            background-color: #ffffff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(75, 63, 179, 0.15);
        }

        .filter-actions {
            display: flex;
            gap: 12px;
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
            width: 100%;
        }

        .btn-reset:hover {
            background-color: #e2e8f0;
            color: #0f172a;
            border-color: #94a3b8;
        }

        /* DataTables Card Container */
        .table-card {
            background: #ffffff !important;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            padding: 16px 20px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01) !important;
        }

        .table-container {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px !important;
        }

        /* Scoped styling for Search Lead table */
        #employee-table {
            width: 100% !important;
            margin: 0 !important;
            border-collapse: collapse !important;
        }

        #employee-table th {
            background-color: #192e62 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            padding: 12px 10px !important;
            text-align: left !important;
            border: none !important;
            white-space: nowrap !important;
            font-family: 'Poppins', sans-serif !important;
            vertical-align: middle !important;
        }

        #employee-table td {
            padding: 10px 10px !important;
            font-size: 12px !important;
            color: #334155 !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #e2e8f0 !important;
            font-family: 'Poppins', sans-serif !important;
            line-height: 1.4 !important;
        }

        #employee-table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
        }

        #employee-table tbody tr:nth-child(odd) {
            background-color: #ffffff !important;
        }

        #employee-table tbody tr:hover {
            background-color: #f1f5f9 !important;
        }

        /* First Column (Actions) */
        #employee-table th:nth-child(1),
        #employee-table td:nth-child(1) {
            text-align: center !important;
            white-space: nowrap !important;
            padding: 10px 6px !important;
            width: 60px !important;
        }

        #employee-table td:nth-child(1) a.notes_id {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-decoration: none !important;
            color: #192e62 !important;
            transition: all 0.2s ease !important;
            padding: 4px 6px !important;
            border-radius: 4px !important;
            vertical-align: middle !important;
        }

        #employee-table td:nth-child(1) a.notes_id:hover {
            background-color: #e2e8f0 !important;
            transform: translateY(-1px) !important;
        }

        #employee-table td:nth-child(1) a.notes_id i {
            font-size: 14px !important;
            color: #192e62 !important;
        }

        /* Campaign Name & Subcampaign */
        #employee-table td:nth-child(2) {
            min-width: 150px !important;
            max-width: 220px !important;
            word-break: break-word !important;
            white-space: normal !important;
        }

        /* Company Name */
        #employee-table td:nth-child(3) {
            min-width: 130px !important;
            max-width: 180px !important;
            word-break: break-word !important;
            white-space: normal !important;
        }

        /* Prospect Column & LinkedIn Icon */
        #employee-table td:nth-child(4) {
            white-space: nowrap !important;
        }

        #employee-table td a {
            display: inline-block !important;
            vertical-align: middle !important;
            margin-bottom: 0 !important;
            margin-top: 0 !important;
        }

        #employee-table td:nth-child(4) a {
            color: #192e62 !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            font-family: 'Poppins', sans-serif !important;
        }

        #employee-table td:nth-child(4) a:hover {
            text-decoration: underline !important;
        }

        #employee-table td:nth-child(4) a.ms-1 {
            margin-left: 4px !important;
        }

        #employee-table td:nth-child(4) i.fa-linkedin {
            color: #0A66C2 !important;
            font-size: 15px !important;
            vertical-align: middle !important;
        }

        /* Designation wrapping */
        #employee-table td:nth-child(5) {
            min-width: 160px !important;
            max-width: 220px !important;
            word-break: break-word !important;
            white-space: normal !important;
        }

        /* Status */
        #employee-table th:nth-child(6),
        #employee-table td:nth-child(6) {
            white-space: nowrap !important;
            text-align: center !important;
        }

        /* Modified At / Updated At */
        #employee-table th:nth-child(7),
        #employee-table td:nth-child(7) {
            white-space: nowrap !important;
            text-align: center !important;
            min-width: 140px !important;
        }

        /* Created By */
        #employee-table th:nth-child(8),
        #employee-table td:nth-child(8) {
            white-space: nowrap !important;
            min-width: 120px !important;
        }

        /* Assigned To - Header */
        #employee-table th:nth-child(9) {
            white-space: nowrap !important;
            min-width: 170px !important;
            color: #ffffff !important;
        }

        /* Assigned To - Data Cells */
        #employee-table td:nth-child(9) {
            white-space: nowrap !important;
            min-width: 170px !important;
            font-weight: 600 !important;
            color: #192e62 !important;
        }

        /* Badges */
        #employee-table span.badge {
            padding: 4px 8px !important;
            font-weight: 600 !important;
            font-size: 11px !important;
            border-radius: 6px !important;
            display: inline-block !important;
            text-transform: capitalize !important;
            font-family: 'Poppins', sans-serif !important;
            line-height: 1.2 !important;
        }

        /* DataTables Header Controls */
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 16px !important;
            float: right !important;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 16px !important;
            float: left !important;
        }

        .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
            color: #475569 !important;
            font-weight: 600 !important;
            margin-bottom: 0 !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-size: 13px !important;
            color: #1e293b !important;
            outline: none !important;
            font-family: 'Poppins', sans-serif !important;
            transition: all 0.2s ease !important;
            background-color: #f8fafc !important;
            box-shadow: none !important;
            width: 320px !important;
            height: 40px !important;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #192e62 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(25, 46, 98, 0.12) !important;
        }

        .dataTables_wrapper .dataTables_length label {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
            color: #475569 !important;
            font-weight: 500 !important;
            margin-bottom: 0 !important;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 13px !important;
            color: #1e293b !important;
            outline: none !important;
            height: 40px !important;
            background-color: #f8fafc !important;
        }

        /* DataTables Pagination */
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 16px !important;
            float: right !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid transparent !important;
            border-radius: 6px !important;
            padding: 6px 12px !important;
            margin: 0 2px !important;
            font-size: 13px !important;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 500 !important;
            color: #475569 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #192e62 !important;
            color: #ffffff !important;
            border-color: #192e62 !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 4px rgba(25, 46, 98, 0.2) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            color: #94a3b8 !important;
            background: transparent !important;
            cursor: not-allowed !important;
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 13px !important;
            color: #64748b !important;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 500 !important;
            padding-top: 20px !important;
            float: left !important;
        }

        /* Modal styling */
        .modal-content {
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            overflow: hidden;
        }

        .modal-header {
            background-color: #192e62 !important;
            color: #ffffff !important;
            padding: 16px 24px !important;
            border-bottom: none !important;
        }

        .modal-header .modal-title {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 600 !important;
            font-size: 16px !important;
            color: #ffffff !important;
        }

        .modal-header .close {
            color: #ffffff !important;
            opacity: 0.8 !important;
            font-size: 24px !important;
            font-weight: 400 !important;
            text-shadow: none !important;
            transition: opacity 0.2s ease !important;
        }

        .modal-header .close:hover {
            opacity: 1 !important;
        }

        .modal-body {
            padding: 24px !important;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0 !important;
            padding: 14px 24px !important;
            background-color: #f8fafc !important;
        }

        .modal-footer .btn-success {
            background-color: #192e62 !important;
            border-color: #192e62 !important;
            border-radius: 8px !important;
            padding: 8px 20px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            font-family: 'Poppins', sans-serif !important;
        }

        .modal-footer .btn-success:hover {
            background-color: #0f1c3f !important;
            border-color: #0f1c3f !important;
        }
    </style>
@endpush

@section('content')

    <div class="main-right">
        <div class="right-side submanager search-lead-page">

            <h2>Search Lead</h2>

            <div class="graph campaignslist logstable lead-listing">

                <!-- Filter Controls -->
                <div class="filter-card">
                    <div class="filter-grid">
                        <!-- Campaign Filter -->
                        <div class="filter-group">
                            <label for="search_campaign_id">Campaign</label>
                            <select id="search_campaign_id" class="filter-select">
                                <option value="">Select Campaign</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}">
                                        {{ $source->source_name }}{{ !empty($source->description) ? ' - ' . $source->description : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Company Filter -->
                        <div class="filter-group">
                            <label for="search_company_name">Company</label>
                            <select id="search_company_name" class="filter-select" disabled>
                                <option value="">Select Company</option>
                            </select>
                        </div>

                        <!-- Action Group -->
                        <div class="filter-actions">
                            <button id="reset_search_lead" class="btn btn-reset">
                                <i class="fa fa-rotate-left me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Datatable -->
                <div class="table">
                    <div class="table-container">
                        <table id="employee-table" class="table table-striped table-hover">
                            <thead class="thead-main">
                                <tr>
                                    <th>Actions</th>
                                    <th>Campaign - Subcampaign</th>
                                    <th>Company Name</th>
                                    <th>Prospect Name</th>
                                    <th>Designation</th>
                                    <th>Status</th>
                                    <th>Modified At</th>
                                    <th>Created By</th>
                                    <th>Assigned To</th>
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

    <!-- View Notes Modal -->
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

    <!-- Contact Numbers Modal -->
    <div class="modal fade" id="numberModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #192e62; color: #fff; padding: 10px 15px;">
                    <h6 class="modal-title" style="color:#fff; font-weight:600;">Contact Numbers</h6>
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

    <!-- Add Note after Dial Modal -->
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

@endsection

@push('scripts')
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#spinner-overlay').show(); // Show loader when page opens

            var leadsTable = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                searching: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('leads.search_lead.leads_data') }}",
                    data: function (d) {
                        d.source_id = $('#search_campaign_id').val();
                        d.company_name = $('#search_company_name').val();
                    }
                },
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'campaign_name', name: 'sources.source_name', orderable: false },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'prospect_name', name: 'prospect_first_name' },
                    { data: 'designation', name: 'designation', orderable: false },
                    { data: 'status', name: 'status', orderable: false },
                    { data: 'updated_at', name: 'updated_at', orderable: true },
                    { data: 'created_by', name: 'leadManager.first_name', orderable: false },
                    { data: 'assigned_to', name: 'user.first_name', orderable: false }
                ],
                drawCallback: function () {
                    if (typeof tippy === 'function') {
                        tippy('[data-tippy-content]', {
                            placement: 'top',
                            arrow: true,
                            animation: 'scale'
                        });
                    }
                },
                initComplete: function () {
                    $('div.dataTables_filter input')
                        .attr('placeholder', 'Search by company, prospect, designation...')
                        .css({ 'width': '320px', 'display': 'inline-block' });

                    $('#spinner-overlay').hide();
                }
            });

            // Show spinner overlay on processing start
            leadsTable.on('preXhr.dt', function (e, settings, data) {
                $('#spinner-overlay').show();
            });

            // Hide spinner overlay when data is loaded
            leadsTable.on('xhr.dt', function (e, settings, json, xhr) {
                $('#spinner-overlay').hide();
            });

            // When Campaign Dropdown Changes
            $('#search_campaign_id').on('change', function () {
                var sourceId = $(this).val();
                var companySelect = $('#search_company_name');

                companySelect.empty().append('<option value="">Select Company</option>');

                if (sourceId) {
                    companySelect.prop('disabled', true);
                    $.ajax({
                        url: "{{ url('leads/search_lead/companies') }}/" + sourceId,
                        type: "GET",
                        dataType: "json",
                        success: function (res) {
                            if (res.status && res.companies.length > 0) {
                                $.each(res.companies, function (index, comp) {
                                    companySelect.append(
                                        $('<option>', {
                                            value: comp.company_name,
                                            text: comp.company_name + ' (' + comp.total_leads + ')'
                                        })
                                    );
                                });
                                companySelect.prop('disabled', false);
                            } else {
                                companySelect.prop('disabled', true);
                            }
                        },
                        error: function () {
                            companySelect.prop('disabled', true);
                        }
                    });
                } else {
                    companySelect.prop('disabled', true);
                }

                leadsTable.ajax.reload();
            });

            // When Company Dropdown Changes
            $('#search_company_name').on('change', function () {
                leadsTable.ajax.reload();
            });

            // Reset Filter
            $('#reset_search_lead').on('click', function () {
                $('#search_campaign_id').val('');
                $('#search_company_name').empty().append('<option value="">Select Company</option>').prop('disabled', true);
                leadsTable.ajax.reload();
            });
        });

        // View Notes function
        function shownoteslist(id) {
            $.ajax({
                url: "{{ url('leads/notes_view') }}/" + id,
                type: "GET",
                success: function (res) {
                    $('#notes_data').html(res.table);
                    $('#largeModal').modal('show');
                }
            });
        }

        function closemodal() {
            $('#largeModal').modal('hide');
        }

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
                rowHtml += '      <a href="javascript:void(0);" onclick="dialNumber(\'' + num.replace(/'/g, "\\'") + '\', ' + (leadId || 0) + ')" class="btn btn-xs btn-success" style="border-radius: 50%; padding: 5px 8px; background-color: #28a745; border-color: #28a745;" title="Dial via API">';
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

            localStorage.setItem('pending_dial_note', JSON.stringify({ lead_id: id, phone: phone }));

            $('#dial_conversation_type').val('NoResponse');
            $('#dial_min-date').val('');
            $('#dial_reminder_time').val('');
            $('#dial_parent_reminder_for').val('');
            $('#dial_reminder_for').empty().append('<option value="">Choose Option</option>');
            $('#dial_reminder_for_child_col').hide();
            $('#dial_feedback').val('VM/No Response');

            $('#dial_phone_number').val(phone || '');
            if (phone) {
                $('#dial_phone_number').prop('disabled', true);
            } else {
                $('#dial_phone_number').prop('disabled', false);
            }

            $('#dial_callback_date').val('');
            $('#dial_callback_time').val('');

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
            let pendingNote = localStorage.getItem('pending_dial_note');
            if (pendingNote) {
                let noteData = JSON.parse(pendingNote);
                showdialnoteaddmodal(noteData.lead_id, noteData.phone);
            }

            $(document).on('keydown', function (event) {
                if (event.key === "Escape" && ($('#status-modal-dialnote').hasClass('show') || $('#status-modal-dialnote').is(':visible'))) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });

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

                    $('#dial_conversation_type_container').show();
                    $('#dial_phone_number_container').show();
                    checkdialtype();
                }
            });
        });

        $('#save-data-dialnote').off('click').on('click', function (event) {
            event.preventDefault();
            let submitBtn = $('#save-data-dialnote');
            let originalHtml = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

            let lead_id = $('#lead_id_dial_note').val();
            let source_id = $('#search_campaign_id').val();
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
                    localStorage.removeItem('pending_dial_note');
                    window.dialnote_submitted = true;
                    $('#status-modal-dialnote').modal('hide');
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
    </script>
@endpush