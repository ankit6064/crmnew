@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .lead-summary-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        }

        .lead-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        @media (max-width: 992px) {
            .lead-summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .lead-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .summary-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-family: 'Poppins', sans-serif;
        }

        .summary-value {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            font-family: 'Poppins', sans-serif;
        }

        .filter-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 20px 24px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            align-items: end;
        }

        @media (max-width: 900px) {
            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-weight: 600;
            font-size: 13px;
            color: #475569;
            margin-bottom: 0px;
            font-family: 'Poppins', sans-serif;
        }

        .filter-control {
            width: 100% !important;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 0 14px;
            font-size: 13px;
            color: #1e293b;
            background-color: #f8fafc;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .filter-control:focus {
            border-color: #192e62;
            background-color: #ffffff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(25, 46, 98, 0.15);
        }

        .btn-filter-action {
            height: 40px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-apply-filter {
            background-color: #192e62;
            color: #ffffff;
        }

        .btn-apply-filter:hover {
            background-color: #0f1d40;
            color: #ffffff;
        }

        .btn-reset-filter {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-reset-filter:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        #employeelogs thead tr th {
            background-color: #192e62 !important;
            color: #ffffff !important;
            font-weight: 600;
            padding: 12px 15px;
            font-size: 13px;
            white-space: nowrap;
        }

        #employeelogs tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            font-size: 13px;
            color: #334155;
        }

        .return-btn {
            background-color: #192e62;
            color: #ffffff;
            border-radius: 8px;
            padding: 8px 18px;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .return-btn:hover {
            background-color: #0f1d40;
            color: #ffffff;
        }
    </style>
@endpush

@section('content')

    <div class="main-right">
        <div class="right-side submanager">

            <!-- Page Header -->
            <div class="row align-items-center mb-3">
                <div class="col-md-8">
                    <h2 class="mb-0">Lead Activity Logs</h2>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn return-btn"
                        onclick="window.history.back() || (window.location.href='{{ route('leads.search_lead') }}');">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </button>
                </div>
            </div>

            <!-- Lead Info Summary Card -->
            <div class="lead-summary-card">
                <div class="lead-summary-grid">
                    <div class="summary-item">
                        <span class="summary-label">Prospect Name</span>
                        <span class="summary-value" style="color: #192e62;">
                            {{ trim(($leadDetails->prospect_first_name ?? '') . ' ' . ($leadDetails->prospect_last_name ?? '')) ?: 'N/A' }}
                        </span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Company / Campaign</span>
                        <span class="summary-value">
                            {{ $leadDetails->company_name ?? 'N/A' }}
                            <small class="text-muted d-block" style="font-weight:400;">
                                {{ $leadDetails->source->source_name ?? 'N/A' }}
                            </small>
                        </span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Who Created Lead</span>
                        <span class="summary-value">
                            {{ $leadDetails->leadManager ? ($leadDetails->leadManager->first_name . ' ' . $leadDetails->leadManager->last_name) : 'N/A' }}
                            <small class="text-muted d-block" style="font-weight:400;">
                                {{ $leadDetails->created_at ? $leadDetails->created_at->format('d M, Y h:i A') : '' }}
                            </small>
                        </span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Assigned To & Status</span>
                        <span class="summary-value">
                            {{ $leadDetails->user ? ($leadDetails->user->first_name . ' ' . $leadDetails->user->last_name) : 'Unassigned' }}
                            <div class="mt-1">
                                @switch($leadDetails->status)
                                    @case(1)
                                        <span class="badge" style="background:#ffc107; color:#000; padding:4px 8px; border-radius:4px;">Pending</span>
                                        @break
                                    @case(2)
                                        <span class="badge" style="background:#dc3545; color:#fff; padding:4px 8px; border-radius:4px;">Failed</span>
                                        @break
                                    @case(3)
                                        <span class="badge" style="background:#28a745; color:#fff; padding:4px 8px; border-radius:4px;">Closed</span>
                                        @break
                                    @case(4)
                                        <span class="badge" style="background:#17a2b8; color:#fff; padding:4px 8px; border-radius:4px;">In Progress</span>
                                        @break
                                    @case(5)
                                        <span class="badge" style="background:#007bff; color:#fff; padding:4px 8px; border-radius:4px;">Completed</span>
                                        @break
                                    @default
                                        <span class="badge" style="background:#6c757d; color:#fff; padding:4px 8px; border-radius:4px;">N/A</span>
                                @endswitch
                            </div>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="filter-card">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="daterange">Date Range</label>
                        <input type="text" id="daterange" class="filter-control" placeholder="Select Date Range" readonly style="cursor: pointer;">
                    </div>

                    <div class="filter-group">
                        <label for="type">Activity Type</label>
                        <select id="type" class="filter-control">
                            <option value="">All Activities</option>
                            <option value="5">Lead Added</option>
                            <option value="16">Employee Assigned</option>
                            <option value="12">Transferred</option>
                            <option value="1">Note Added</option>
                            <option value="2">Status Updated</option>
                            <option value="3">LHS Created</option>
                            <option value="4">MOM Report</option>
                            <option value="17">LHS Sent</option>
                            <option value="18">LHS Reminder</option>
                            <option value="19">Lead Confirmed</option>
                            <option value="20">Meeting Status</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="employee_id">Performed By</label>
                        <select id="employee_id" class="filter-control">
                            <option value="">All Users</option>
                            @foreach($employee as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name . ' ' . $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group" style="flex-direction: row; gap: 10px;">
                        <button type="button" id="filterLogs" class="btn btn-filter-action btn-apply-filter w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <button type="button" id="resetLogs" class="btn btn-filter-action btn-reset-filter w-100">
                            <i class="fas fa-rotate-left me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <input type="hidden" id="lead_id" value="{{ $id }}">

            <!-- Activity Logs Table -->
            <div class="graph campaignslist logstable lead-listing">
                <div class="table">
                    <div class="table-container">
                        <table id="employeelogs" class="table table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>Event Type</th>
                                    <th>Action Details</th>
                                    <th>Performed By</th>
                                    <th>Date & Time</th>
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

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#spinner-overlay').show();

            var logTable = $('#employeelogs').DataTable({
                processing: false,
                serverSide: true,
                searching: true,
                ordering: true,
                order: [[3, 'desc']],
                ajax: {
                    url: "{{ route('viewleadlogstable') }}",
                    data: function (d) {
                        d.lead_id = $('#lead_id').val();
                        d.employeeid = $('#employee_id').val();
                        d.date = $('#daterange').val();
                        d.type = $('#type').val();
                    }
                },
                columns: [
                    { data: 'type_badge', name: 'type', orderable: false, searchable: false },
                    { data: 'description', name: 'description', orderable: false },
                    { data: 'employeename', name: 'users.first_name', orderable: false },
                    { data: 'created_at', name: 'created_at', orderable: true }
                ]
            });

            logTable.on('preXhr.dt', function () {
                $('#spinner-overlay').show();
            });

            logTable.on('xhr.dt', function () {
                $('#spinner-overlay').hide();
            });

            $('#filterLogs').on('click', function () {
                logTable.ajax.reload();
            });

            $('#resetLogs').on('click', function () {
                $('#employee_id').val('');
                $('#daterange').val('');
                $('#type').val('');
                logTable.ajax.reload();
            });

            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                opens: 'left',
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                }
            });

            $('#daterange').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endpush