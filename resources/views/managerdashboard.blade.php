@extends('layouts.admin')
@section('content')
    <style>
        .stat-button.active {
            background: #51c1c8;
            color: #fff;
        }

        .chart-container {
            width: 70%;
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .filter-bar {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-bar label {
            font-weight: bold;
            margin-right: 5px;
        }

        #barChart {
            width: 100% !important;
            height: 400px !important;
        }

        .stat-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
    <div class="main-right managersubmanagerclass">
        <div class="right-side">
            <h2>Dashboard</h2>
            <div class="row first-card">
                <div class="col-md-3">
                    <a href="{{ route('employee.submanagerlisting') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Submanagers</h4>
                                <div class="card-icon">
                                    <i class="fa-regular fa-user"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $submangercount }}</h2>
                                <div class="arrow-icon">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('employee.manageremployeeindex') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Employees</h4>
                                <div class="card-icon icon2">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $employeecount }}</h2>
                                <div class="arrow-icon">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('sources.getMangerSource') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Campaigns</h4>
                                <div class="card-icon icon3">
                                    <i class="fa-regular fa-calendar-check"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $campaigncount }}</h2>
                                <div class="arrow-icon">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('allleadview') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Leads</h4>
                                <div class="card-icon icon4">
                                    <i class="fa-regular fa-address-card"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totalleads }}</h2>
                                <div class="arrow-icon">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
            <div class="row second-card">



                <div class="col-md-3">
                    <a href="{{ route('meeting_scheduled') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Meeting Setup</h4>
                                <div class="card-icon icon6">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totallhscount }}</h2>
                                <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('employeecompletedleads') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Completed Leads</h4>
                                <div class="card-icon icon7">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totalmomcount }}</h2>
                                <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('employeefailedleads') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Failed Leads</h4>
                                <div class="card-icon icon8">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totalfailedcount }}</h2>
                                <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('managercallbackleads') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Callbacks</h4>
                                <div class="card-icon icon5">
                                    <i class="fa-solid fa-phone-volume"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{$callbackleads}}</h2>
                                <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

            <div class="graph">
                <div class="filter-container">
                    <select class="filter-select" id="graphid">
                        <option value="0" disabled hidden>Graph Type</option>
                        <option value="1">Line Chart</option>
                        <option value="2" selected>Bar Chart</option>
                        <option value="3">Pie Chart</option>
                    </select>

                    <select class="filter-select" id="campagin_id">
                        <option value="" selected disabled hidden>Select Campaign</option>
                        @if(isset($sources) && !empty($sources))
                            @foreach ($sources as $sourcelisting)

                                <option value="{{ $sourcelisting->id }}">
                                    {{ $sourcelisting->source_name . '-' . $sourcelisting->description }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <select class="filter-select" id="employee_id">
                        <option value="" selected disabled hidden>Select Employee</option>
                        @if(isset($employee) && !empty($employee))
                            @foreach ($employee as $employeelisting)

                                <option value="{{ $employeelisting->id }}">
                                    {{ $employeelisting->first_name . ' ' . $employeelisting->last_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>



                    <input type="text" name="daterange" id="daterange" class="filter-date" style="width:300px" />
                    <button type="button" id="filterLogs" style="padding:10px 20px; font-size:15px; font-weight:600; border:none; border-radius:8px; 
                                               cursor:pointer; background-color: #0d3d72; color:#fff; 
                                               box-shadow:0px 3px 6px rgba(0,0,0,0.1); transition:all 0.3s ease;">
                        Filter
                    </button>
                    <button type="button" id="reset" style="padding:10px 20px; font-size:15px; font-weight:600; border:none; border-radius:8px; 
                                               cursor:pointer; background:#e74a3b; color:#fff; 
                                               box-shadow:0px 3px 6px rgba(0,0,0,0.1); transition:all 0.3s ease;">
                        Reset
                    </button>
                </div>
                <div class="stats-container">
                    <a href="#" class="stat-button active" data-filter="leads">Total Contacts</a>
                    <a href="#" class="stat-button active" data-filter="notes">Total Conversations</a>
                    <a href="#" class="stat-button active" data-filter="closed">Total Closed Leads</a>
                    <a href="#" class="stat-button active" data-filter="completed">Total Complete Leads</a>
                </div>

                <div class="chart">
                    <!-- Line Chart -->
                    <canvas id="lineChart" width="200" height="400  " style="display: none;"></canvas>

                    <!-- Bar Chart -->
                    <canvas id="barChart" width="200" height="400"></canvas>

                    <!-- Pie Chart -->
                    <canvas id="pieChart" width="700" height="600" style="display: none;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

        <script>
            $(function () {
                $('#daterange').daterangepicker({
                    autoUpdateInput: true,
                    opens: 'left',
                    locale: {
                        format: 'YYYY-MM-DD',
                        cancelLabel: 'Clear'
                    }
                });

                $('#daterange').on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                });

                $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });
            });
        </script>


        <script>
            $(document).ready(function () {
                let selectedFilters = ["leads", "notes", "closed", "completed"]; // default active
                let currentGraphType = "bar"; // default

                // Fixed colors per label
                const labelColors = {
                    "leads": "#36a2eb",
                    "notes": "#ffcd56",
                    "closed": "#ff6384",
                    "completed": "#4bc0c0"
                };

                function loadChart() {
                    $('#spinner-overlay').css('display', '');

                    $.ajax({
                        type: 'GET',
                        url: "{{ route('getmanagergraph') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "date": $('#daterange').val(),
                            "campaign_id": $('#campagin_id').val(),
                            "employee_id": $('#employee_id').val()
                        },
                        success: function (response) {
                            let labels = response.labels || [];
                            let values = response.values || [];

                            // Filter data
                            let filteredLabels = [];
                            let filteredValues = [];
                            let filteredColors = [];

                            labels.forEach((label, i) => {
                                let key = label.toLowerCase();
                                if (selectedFilters.includes(key)) {
                                    filteredLabels.push(label);
                                    filteredValues.push(values[i]);
                                    filteredColors.push(labelColors[key] || "#999");
                                }
                            });

                            $('#spinner-overlay').css('display', 'none');


                            if (filteredLabels.length === 0) {
                                filteredLabels = labels;
                                filteredValues = values;
                                filteredColors = labels.map(l => labelColors[l.toLowerCase()] || "#999");
                            }

                            // Destroy old chart
                            if (window.currentChart) {
                                window.currentChart.destroy();
                            }

                            // Hide all canvases first
                            $("#lineChart, #barChart, #pieChart").hide();

                            let chartId = currentGraphType + "Chart";
                            const ctx = document.getElementById(chartId).getContext('2d');
                            $("#" + chartId).show();

                            let chartConfig = {
                                type: currentGraphType,
                                data: {
                                    labels: filteredLabels,
                                    datasets: [{
                                        label: 'Count',
                                        data: filteredValues,
                                        backgroundColor: filteredColors,
                                        borderColor: '#333',
                                        borderWidth: 1,
                                        fill: currentGraphType === 'line' ? false : true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: !(currentGraphType === 'bar' || currentGraphType === 'line') },
                                        datalabels: {
                                            anchor: 'end',
                                            align: 'top',
                                            color: '#000',
                                            font: { weight: 'bold' },
                                            formatter: value => value
                                        }
                                    }
                                },
                                plugins: [ChartDataLabels]
                            };

                            window.currentChart = new Chart(ctx, chartConfig);
                        },
                        error: function (xhr) {
                            console.error('AJAX error:', xhr.responseText);
                        }
                    });
                }

                // Initial load
                loadChart();

                // Stat buttons toggle
                $(".stat-button").on("click", function (e) {
                    e.preventDefault();
                    let filterType = $(this).data("filter").toLowerCase();

                    if ($(this).hasClass("active")) {
                        $(this).removeClass("active");
                        selectedFilters = selectedFilters.filter(f => f !== filterType);
                    } else {
                        $(this).addClass("active");
                        selectedFilters.push(filterType);
                    }
                    loadChart();
                });

                // Apply filters
                $("#filterLogs").on("click", function () {
                    let val = $('#graphid').val();
                    if (val === "1") currentGraphType = "line";
                    else if (val === "2") currentGraphType = "bar";
                    else if (val === "3") currentGraphType = "pie";
                    // $('#spinner-overlay').css('display','');
                    loadChart();
                });

                // Reset filters
                $("#reset").on("click", function () {
                    $("#graphid").val("2"); // reset to bar
                    $("#campagin_id").val("");
                    $("#employee_id").val("");
                    let today = moment().format("YYYY-MM-DD");
                    $("#daterange").val(today + " - " + today);
                    $(".stat-button").addClass("active");
                    selectedFilters = ["leads", "notes", "closed", "completed"];
                    currentGraphType = "bar";
                    loadChart();
                });
            });
        </script>

    @endpush

@endsection