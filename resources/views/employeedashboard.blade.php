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
        .stat-card:hover{
            cursor: pointer;
        }
    </style>
    <div class="main-right managersubmanagerclass">
        <div class="right-side">
            <h2>Dashboard</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card" onclick="redirectcard('leads/closed');">
                        <div class="card-header">
                            <h4>Closed Leads</h4>
                            <div class="card-icon">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{ $totalClosedLeads }}</h2>
                            <div class="arrow-icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card" onclick="redirectcard('leads/completed');">
                        <div class="card-header">
                            <h4>Completed Leads</h4>
                            <div class="card-icon icon2">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{ $totalCompletedLeads }}</h2>
                            <div class="arrow-icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card" onclick="redirectcard('leads/in_progress');">
                        <div class="card-header">
                            <h4>Inprogress Leads</h4>
                            <div class="card-icon icon3">
                                <i class="fa-solid fa-spinner"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{ $totalInprogressLeads }}</h2>
                            <div class="arrow-icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="row second-card">
                <div class="col-md-4">
                    <div class="stat-card" onclick="redirectcard('reminder/view');">
                        <div class="card-header">
                            <h4>Today's Reminder</h4>
                            <div class="card-icon icon5">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{ $todayReminders }}</h2>
                            <div class="arrow-icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" onclick="redirectcard('leads/failed');">
                        <div class="card-header">
                            <h4>Failed Leads</h4>
                            <div class="card-icon icon4">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{ $totalFailedLeads }}</h2>
                            <div class="arrow-icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="graph campaignslist">

                <div class="table">
                    <div class="table-container">
                        <table id="employee-table">
                            <thead class="thead-main">
                                <tr>
                                    <th>Source Name</th>
                                    <th>Description</th>
                                    <th>Total Leads</th>
                                    <th>Last Login</th>
                                    <th>Notes Count</th>
                                </tr>
                            </thead>
                            <tbody id="new-table-body"></tbody>
                        </table>
                    </div>

                    {{-- CUSTOM PAGINATION --}}
                    <div class="pagination" id="pagination"></div>
                </div>
            </div> -->
        </div>
    </div>

    <!-- jQuery -->
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

        <!-- <script>
            $(document).ready(function () {
                $('#spinner-overlay').show();

                let table = $('#employee-table').DataTable({
                    processing: false,
                    serverSide: true,
                    searching: false,
                    ajax: {
                        url: '{{ route("geEmployeeDashboardData") }}',
                        type: 'GET',
                        error: function (xhr, error, thrown) {
                            console.error('Error:', xhr.responseText);
                            alert('An error occurred while loading data.');
                        }
                    },
                    columns: [
                        { data: 'campaign_name', name: 'campaign_name', orderable: false, searchable: false },
                        { data: 'description', name: 'description', orderable: false, searchable: false },
                        { data: 'totalLeads', name: 'totalLeads', orderable: false, searchable: false },
                        { data: 'last_login', name: 'last_login', orderable: false, searchable: false },
                        { data: 'notes_count', name: 'notes_count', orderable: false, searchable: false },
                    ],

                    preDrawCallback: function () {
                        $('#spinner-overlay').show(); // Show loader before table redraws (page change, search, etc.)
                    },

                    drawCallback: function () {
                        $('#spinner-overlay').hide(); // Hide loader after table redraws
                    },

                    initComplete: function () {
                        $('#spinner-overlay').hide(); // Hide spinner after table is fully initialized
                    }
                });

                // Show loader when page is changed manually (pagination)
                $('#campaign-table').on('page.dt', function () {
                    $('#spinner-overlay').show();
                });
            });



        </script> -->

        <script>
            function redirectcard(page) {
                window.location.assign(page);
            }

        </script>


    @endpush

@endsection