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
            background-color: red;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        /* --- Filter Card Styles --- */
        .stat-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card.active-card {
            border-color: #192e62;
            background-color: #f0f4ff;
        }

        /* --- Icon Cursor Styles --- */
        .fa,
        .fas,
        .fa-solid,
        .fa-regular {
            cursor: pointer !important;
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
            color: blue;
            font-weight: bold;
        }
    </style>
    <div class="main-right managersubmanagerclass">
        <div class="right-side">
            <div class="row">
                <div class="row align-items-center mb-3">
                    <div class="col-md-8">
                        <h2 class="mb-0">Campaign Listing</h2>
                    </div>

                    <div class="col-md-4 text-end">
                        <button type="button" class="btn return-btn"
                            onclick="window.history.back() || (window.location.href='{{ route('employeedashboard') }}');">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </button>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card filter-card active-card" data-filter="total">
                        <div class="card-header">
                            <h4>Total</h4>
                            <div class="card-icon">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{ $totalCampaign }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card filter-card" data-filter="active">
                        <div class="card-header">
                            <h4>Active</h4>
                            <div class="card-icon acti">
                                <i class="fa-regular fa-user"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{ $activeCampaign }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card filter-card" data-filter="inactive">
                        <div class="card-header">
                            <h4>Inactive</h4>
                            <div class="card-icon inactive">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{ $inactiveCampaign }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="graph campaignslist campaignlistingnew">

                <div class="table">
                    <div class="table-container">
                        <table id="employee-table">
                            <thead class="thead-main">
                                <tr>
                                    <th>Source Name</th>
                                    <th>Sub Campaign</th>
                                    <th>Total Leads</th>
                                    <th>Last Login</th>
                                    <th>Notes Count</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="new-table-body"></tbody>
                        </table>
                    </div>

                    {{-- CUSTOM PAGINATION --}}
                    <div class="pagination" id="pagination"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function () {

                $('#spinner-overlay').show();
                var currentStatus = 'total'; // default filter

                let table = $('#employee-table').DataTable({
                    processing: false,
                    serverSide: true,
                    searching: true,

                    ajax: {
                        url: '{{ route("geEmployeeDashboardData") }}',
                        type: 'GET',
                        data: function (d) {
                            d.status_filter = currentStatus;
                        },
                        error: function (xhr, error, thrown) {
                            console.error('Error:', xhr.responseText);
                            alert('An error occurred while loading data.');
                        }
                    },

                    columns: [
                        { data: 'campaign_name', orderable: false, searchable: false },
                        { data: 'description', orderable: false, searchable: false },
                        { data: 'totalLeads', orderable: false, searchable: false },
                        { data: 'last_login', orderable: false, searchable: false },
                        { data: 'notes_count', orderable: false, searchable: false },
                        { data: 'action', orderable: false, searchable: false },
                    ],

                    preDrawCallback: function () {
                        $('#spinner-overlay').show();
                    },

                    drawCallback: function () {
                        tippy('.viewleads', {
                            content: 'View Leads',
                            placement: 'top',
                            arrow: true,
                            animation: 'scale'
                        });
                        $('#spinner-overlay').hide();
                    },

                    initComplete: function () {
                        $('#spinner-overlay').hide();
                    }
                });

                // 🔹 Filter Card Click
                $('.filter-card').on('click', function () {
                    $('.filter-card').removeClass('active-card');
                    $(this).addClass('active-card');

                    currentStatus = $(this).data('filter');
                    table.draw();
                });

                // 🔹 Custom search placeholder
                table.on('init.dt', function () {
                    $('div.dataTables_filter input')
                        .attr('placeholder', 'Search by campaign,sub campaign')
                        .css({ 'width': '250px' });
                });

                // 🔹 Loader control
                table.on('preXhr.dt', function () {
                    $('#spinner-overlay').show();
                });

                table.on('xhr.dt', function () {
                    $('#spinner-overlay').hide();
                });

                // 🔹 Pagination loader
                $('#employee-table').on('page.dt', function () {
                    $('#spinner-overlay').show();
                });

            });
        </script>

        <script>
            function redirectcard(page) {
                window.location.assign(page);
            }

        </script>


    @endpush

@endsection