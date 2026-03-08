@extends('layouts.admin')
@section('content')
    <style>
     .card-icon{
    width:45px;
    height:45px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:18px;
}

/* Colors */
.icon-campaign{ background:#6f42c1 !important; }
.icon-leads{ background:#0d6efd !important; }
.icon-fresh{ background:#28a745 !important; }
.icon-closed{ background:#343a40 !important; }
.icon-completed{ background:#20c997 !important; }
.icon-progress{ background:#fd7e14 !important; }
.icon-failed{ background:#dc3545 !important; }
.icon-reminder{ background:#ffc107 !important; color:#000 !important; }  

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
            <div class="row">

<!-- Total Campaigns -->
<div class="col-md-3">
    <div class="stat-card" onclick="redirectcard('sources/employeecampaign');">
        <div class="card-header">
            <h4>Total Campaigns</h4>
            <div class="card-icon icon-campaign">
                <i class="fa-solid fa-bullseye"></i>
            </div>
        </div>
        <div class="card-body">
            <h2>{{ $totalCampaign }}</h2>
            <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
        </div>
    </div>
</div>

<!-- Total Leads -->
<div class="col-md-3">
    <div class="stat-card" onclick="redirectcard('leads/completed');">
        <div class="card-header">
            <h4>Total Leads</h4>
            <div class="card-icon icon-leads">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
        <div class="card-body">
            <h2>{{ $totalLeads }}</h2>
            <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
        </div>
    </div>
</div>

<!-- Fresh Leads -->
<div class="col-md-3">
    <div class="stat-card" onclick="redirectcard('leads/in_progress');">
        <div class="card-header">
            <h4>Fresh Leads</h4>
            <div class="card-icon icon-fresh">
                <i class="fa-solid fa-user-plus"></i>
            </div>
        </div>
        <div class="card-body">
            <h2>{{ $totalFreshLeads }}</h2>
            <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
        </div>
    </div>
</div>

<!-- Closed Leads -->

<div class="col-md-3">
    <div class="stat-card" onclick="redirectcard('leads/closed');">
        <div class="card-header">
            <h4>Closed Leads</h4>
            <div class="card-icon icon-closed">
                <i class="fa-solid fa-lock"></i>
            </div>
        </div>
        <div class="card-body">
            <h2>{{ $totalClosedLeads }}</h2>
            <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
        </div>
    </div>
</div>
</div>


<div class="row second-card">



<!-- Completed Leads -->
<div class="col-md-3">
    <div class="stat-card" onclick="redirectcard('leads/completed');">
        <div class="card-header">
            <h4>Completed Leads</h4>
            <div class="card-icon icon-completed">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="card-body">
            <h2>{{ $totalCompletedLeads }}</h2>
            <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
        </div>
    </div>
</div>

<!-- Inprogress Leads -->
<div class="col-md-3">
    <div class="stat-card" onclick="redirectcard('leads/in_progress');">
        <div class="card-header">
            <h4>Inprogress Leads</h4>
            <div class="card-icon icon-progress">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>
        <div class="card-body">
            <h2>{{ $totalInprogressLeads }}</h2>
            <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
        </div>
    </div>
</div>

<!-- Failed Leads -->
<div class="col-md-3">
    <div class="stat-card" onclick="redirectcard('leads/failed');">
        <div class="card-header">
            <h4>Failed Leads</h4>
            <div class="card-icon icon-failed">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>
        <div class="card-body">
            <h2>{{ $totalFailedLeads }}</h2>
            <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
        </div>
    </div>
</div>

<!-- Today's Reminder -->
<div class="col-md-3">
    <div class="stat-card" onclick="redirectcard('reminder/view');">
        <div class="card-header">
            <h4>Today's Reminder</h4>
            <div class="card-icon icon-reminder">
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>
        <div class="card-body">
            <h2>{{ $todayReminders }}</h2>
            <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
        </div>
    </div>
</div>

</div>




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