@extends('layouts.admin')

@section('content')

    <style>
        .assign-controls {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 25px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            flex-wrap: wrap;
        }

        .assign-controls .control-group {
            flex: 1;
            min-width: 250px;
            max-width: 400px;
        }

        .assign-controls select {
            width: 100%;
            height: 50px;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0 15px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }

        .assign-controls select:focus {
            border-color: #0d3a6b;
        }

        .assign-controls .btn-save {
            background: #0d3a6b;
            color: #fff;
            height: 50px;
            padding: 0 40px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .assign-controls .btn-save:hover {
            background: #0d3a6b;
            transform: translateY(-2px);
        }

        .check-all-container {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: #0d3a6b;
            color: #fff;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        .check-all-container input {
            cursor: pointer;
            transform: scale(1.2);
        }

        .check-all-container label {
            margin-bottom: 0;
            cursor: pointer;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
        }

        .nav-tabs {
            border-bottom: 2px solid #f0f2f5;
            margin-bottom: 25px;
            display: flex;
            gap: 5px;
        }

        .nav-tabs .nav-item {
            margin-bottom: -2px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 12px 20px;
            transition: all 0.3s ease;
            background: none;
            font-size: 15px;
        }

        .nav-tabs .nav-link.active {
            color: #0d3a6b;
            border-bottom: 3px solid #0d3a6b;
            font-weight: bold;
        }

        .mannual_assign {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .mannual_assign .form-group {
            flex: 1;
            min-width: 200px;
            max-width: 300px;
            margin-bottom: 0;
        }

        .mannual_assign input {
            width: 100%;
            height: 50px;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0 15px;
            font-size: 15px;
            outline: none;
        }

        .table-container {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }

        .table-container-inner {
            overflow-x: auto;
        }

        table.table {
            width: 100%;
            border-collapse: collapse;
        }

        .basic_checkbox {
            transform: scale(1.3);
            cursor: pointer;
        }

        #employee-table td,
        #employee-table th {
            vertical-align: middle !important;
            white-space: nowrap !important;
        }
    </style>

    <div class="main-right">
        <div class="right-side">
            <div class="row align-items-center mb-4">
                <div class="col-md-8">
                    <h2 class="mb-0">Assign Leads</h2>
                </div>
                <div class="col-md-4 text-end">
                    @if(Auth::user()->is_admin == null)
                        <button type="button" class="btn return-btn"
                            onclick="window.history.back() || (window.location.href='{{ route('dashboard') }}');">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </button>
                    @else
                        <button type="button" class="btn return-btn"
                            onclick="window.history.back() || (window.location.href='{{ route('managerdashboard') }}');">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </button>
                    @endif
                </div>
            </div>

            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{Session::get('success')}}
                </div>
            @elseif (Session::has('error'))
                <div class="alert alert-danger" role="alert">
                    {{Session::get('error')}}
                </div>
            @endif

            @if($errors->has('lead_id'))
                <div class="alert alert-danger">{{ $errors->first('lead_id') }}</div>
            @endif

            @php
                $admin = App\Models\User::where(['is_admin' => Null, 'id' => auth()->user()->id])->first();
            @endphp

            @if(!empty($admin))
                <form class="form-horizontal" id="assignForm" method="post" action="{{ route('assignLeadsManager') }}">
            @else
                    <form class="form-horizontal" id="assignForm" method="post" action="{{ route('assignLeadsEmployee') }}">
                @endif
                    @csrf

                    <div class="graph">
                        @if(!empty($admin))
                            <div class="assign-controls">
                                <div class="control-group">
                                    <select class="form-control" name="employee_id" id="employee_id" required>
                                        <option value="">Select Manager</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee['id'] }}" {{ old('employee_id') == $employee['id'] ? 'selected' : '' }}>
                                                {{ $employee['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('employee_id'))
                                        <div class="text-danger mt-1">{{ $errors->first('employee_id') }}</div>
                                    @endif
                                </div>
                                <button type="submit" id="submitButton" class="btn-save">Assign</button>
                            </div>
                        @else
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="ByCheck" data-toggle="tab" href="#searchByCheck" role="tab"
                                        aria-controls="check" aria-selected="true">Assign by Checkbox</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="ByRange" data-toggle="tab" href="#searchByRange" role="tab"
                                        aria-controls="range" aria-selected="false">Assign by Range</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="searchByCheck" role="tabpanel"
                                    aria-labelledby="ByCheck">
                                    <div class="assign-controls">
                                        <div class="control-group">
                                            <select class="form-control" name="employee_id" id="employee_id">
                                                <option value="">Select Employee</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee['id'] }}" {{ old('employee_id') == $employee['id'] ? 'selected' : '' }}>
                                                        {{ $employee['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('employee_id'))
                                                <div class="text-danger mt-1">{{ $errors->first('employee_id') }}</div>
                                            @endif
                                        </div>
                                        <button type="submit" id="submitButton" class="btn-save">Assign</button>
                                        <div class="check-all-container">
                                            <input class="switch-input all_leads" id="all_leads" name="lead_id[]"
                                                type="checkbox" value="all_leads">
                                            <label for="all_leads">Check All Leads</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="searchByRange" role="tabpanel" aria-labelledby="ByRange">
                                    <div class="assign-controls">
                                        <div class="control-group">
                                            <select class="form-control" name="employee_id_range" id="employee_id_range">
                                                <option value="">Select Employee</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee['id'] }}">{{ $employee['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mannual_assign">
                                            <div class="form-group">
                                                <input type="text" class="form-control assign_start_id" name="assign_start_id"
                                                    placeholder="Enter Start Lead ID">
                                            </div>
                                            <div class="form-group">
                                                <input type="text" class="form-control assign_end_id" name="assign_end_id"
                                                    placeholder="Enter End Lead ID">
                                            </div>
                                        </div>
                                        <button type="submit" id="submitButtonRange" class="btn-save"
                                            onclick="submitRangeForm(event);">Assign</button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="table">
                            <div class="table-container">
                                <div class="table-container-inner">
                                    <table id="employee-table" class="table table-striped table-hover" cellspacing="0"
                                        width="100%">
                                        <thead class="thead-main">
                                            <tr>
                                                <th>Campaign Name</th>
                                                <th>Lead Name</th>
                                                <th>Lead ID</th>
                                                <th>Email</th>
                                                <th>Phone No.</th>
                                                <th>Date</th>
                                                <th style="text-align: center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $lead)
                                                @php
                                                    $campaign_name = '';
                                                    if (!empty($lead['source_id'])) {
                                                        $source_data = App\Models\Source::find($lead['source_id']);
                                                        if ($source_data) {
                                                            $campaign_name = $source_data->source_name;
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="wraping">{{ $campaign_name }}</td>
                                                    <td class="wraping">
                                                        <a href="{{ url('/leads', [$lead['id']]) }}" target="_blank"
                                                            style="color: black; font-weight: 500; text-decoration: none;">
                                                            {{ $lead['prospect_first_name'] . ' ' . $lead['prospect_last_name'] }}
                                                        </a>
                                                    </td>
                                                    <td class="wraping">{{ $lead['id'] }}</td>
                                                    <td class="wraping">{{ $lead['prospect_email'] }}</td>
                                                    <td>{{ $lead['contact_number_1'] }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($lead['created_at'])->format('d M, Y') }}</td>
                                                    <td style="text-align: center;">
                                                        <input type="checkbox" class="basic_checkbox" name="lead_id[]"
                                                            value="{{ $lead['id'] }}" id="basic_checkbox_{{ $lead['id'] }}"
                                                            aria-label="Select lead">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- jQuery (necessary for DataTables plugin) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable with client-side processing
            var table = $('#employee-table').DataTable({
                "processing": false,  // Turn off processing as it's client-side
                "serverSide": false,  // Turn off server-side processing
                "paging": true,       // Enable pagination
                "searching": true,    // Enable search functionality
                "ordering": true,     // Enable column sorting
                "order": [],          // Do not apply default sorting on load
                "info": true,         // Display information about the data
                "lengthChange": true, // Enable changing number of rows per page (Show 10 entries dropdown)
                "language": {
                    "searchPlaceholder": "Search by campaign, prospect, email..."
                }
            });

            // Apply placeholder and style directly since client-side init is synchronous
            $('div.dataTables_filter input')
                .attr('placeholder', 'Search by campaign, prospect, email...')
                .css({ 'width': '250px', 'display': 'inline-block' });

            // Check / Uncheck all leads
            $('input#all_leads').click(function () {
                $(".basic_checkbox").prop('checked', $(this).prop('checked'));
            });
        });

        function submitRangeForm(e) {
            e.preventDefault();
            // Copy the selected employee from By Range tab select to the main employee_id field before submitting
            var selectedEmp = $('#employee_id_range').val();
            if (!selectedEmp) {
                alert('Please select an employee');
                return;
            }
            // Temporarily set the employee_id and submit the form
            $('<input>').attr({
                type: 'hidden',
                name: 'employee_id',
                value: selectedEmp
            }).appendTo('#assignForm');

            $('#assignForm').submit();
        }
    </script>
@endpush