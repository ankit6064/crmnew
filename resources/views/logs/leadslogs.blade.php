@extends('layouts.admin')
@section('content')
    <style>
        .message-box {
            padding: 15px 20px;
            margin: 15px 0;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            background-color: #007bff;
            /* Blue info message */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .message-box.success {
            background-color: #28a745;
        }

        .message-box.error {
            background-color: #dc3545;
        }

        table.employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-family: Arial, sans-serif;
        }

        .employee-table th,
        .employee-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .employee-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            cursor: pointer;
        }

        .employee-table th.sortable:hover {
            background-color: #e0e0e0;
        }

        .employee-table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .employee-table tr:hover {
            background-color: #f1f1f1;
        }

        .employee-table input[type="checkbox"] {
            transform: scale(1.2);
            cursor: pointer;
        }

        .view_emp {
            cursor: pointer;
            color: #007bff;
            text-decoration: none;
        }

        .view_emp:hover {
            text-decoration: underline;
        }

        .filter-box {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .filter-box select,
        .filter-box input {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .filter-box button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }

        .filter-box button:hover {
            background-color: #0056b3;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination a {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #007bff;
        }

        .pagination a.active {
            background-color: #007bff;
            color: #fff;
        }

        .pagination a:hover {
            background-color: #e0e0e0;
        }
        
    </style>

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Leads Logs</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline-info">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0 text-white">Leads Logs</h4>
                    </div>

                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="filter-box">
                            <!-- <select name="filter_status" id="employee_id">
                                <option value="">Select Employee</option>
                                @if(isset($employee) && !empty($employee))
                                    @foreach($employee as $employeelisting)
                                        <option value="{{ $employeelisting->id }}">
                                            {{ $employeelisting->first_name . ' ' . $employeelisting->last_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select> -->

                            <select name="filter_status" id="source_id" style="width: 400px;">
                                <option value="">Select Campaign</option>
                                @if(isset($sources) && !empty($sources))
                                    @foreach($sources as $sourcelisting)
                                        <option value="{{ $sourcelisting->id }}">
                                            {{ $sourcelisting->source_name . '-' . $sourcelisting->description }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>

                            <!-- <select name="filter_status" id="type">
                                <option value="">Select Type</option>
                                    <option value="1">Note Added</option>
                                    <option value="2">Lead Status Updated</option>
                                    <option value="3">LHS Report Created</option>
                                    <option value="6">LHS Report Updated</option>
                                    <option value="4">MOM Report Generated</option>
                                    <option value="5">New Lead Added</option>

                            </select> -->
                            <!-- <input type="text" name="daterange" id="daterange" class="form-control" /> -->
                            <button type="button" id="filterLogs" style="background-color:#192e62 !important;border:1px solid #192e62">Filter</button>
                            <button type="button" id="reset" style="background-color:red">Reset</button>
                        </div>

                        <!-- Messages -->
                        @if (session('success'))
                            <div class="message-box success">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="message-box error">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Logs Table -->
                        <table id="employeelogs" class="display nowrap table table-hover table-striped table-bordered"
                            cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <!-- <th>Lead Id</th> -->
                                    <th>Campaign Name</th>
                                    <th>Company Name</th>
                                    <th>Lead Name</th>
                                    <th>View Logs</th>
                                 
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#spinner-overlay').show(); // Show full-page spinne
            var employeeid = $('#employee_id').val();
            var sourceid = $('#source_id').val();
            var date = $('#date').val();
            const url = "{{ route('filterleadslogs') }}";
            var table = $('#employeelogs').DataTable({
                processing: false,
                serverSide: true,
                searching: false, // Enable search globally
                ordering: false, // Disable sorting

                ajax: {
                    url: url,
                    data: function (d) {
                        d.employeeid = $('#employee_id').val();
                        d.sourceid = $('#source_id').val();
                        d.date = $('#daterange').val(); // ✅ use daterange input
                            d.type = $('#type').val();

                    },
                    error: function (xhr, error, thrown) {
                        console.error("AJAX Error:", xhr.responseText);
                    }
                },
                columns: [
                    // { data: 'lead_id', name: 'lead_id', orderable: true, searchable: false },
                    { data: 'campaign_name', name: 'campaign_name', orderable: false, searchable: false },
                    { data: 'company_name', name: 'company_name', orderable: false, searchable: false },
                    { data: 'lead_name', name: 'lead_name', orderable: false, searchable: false },
                    { data: 'viewlogs', name: 'viewlogs', orderable: false, searchable: false },

                ],
                // order: [[0, 'desc']] // index 5 is 'created_at' column, descending



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
            $('#filterLogs').on('click', function () {
                console.log('Filter changed! Reloading table...');
                table.ajax.reload(); // Reload DataTable with new filter values
            });

            $('#reset').on('click', function () {
            $('#employee_id').val('');
            $('#source_id').val('');
            $('#daterange').val(''); // ✅ clear the date range input
            $('#type').val('');    
             table.ajax.reload(); // Reload DataTable with new filter values
            });

        });

    </script>
    <script>
$(function () {
    $('#daterange').daterangepicker({
        autoUpdateInput: false, // ❗Prevents default date display
        opens: 'left',
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
        }
    });
        // Apply selected date range to the input
        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(
            picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD')
        );
    });

    // Clear input on cancel
    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
});

</script>
@endpush