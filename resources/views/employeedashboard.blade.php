@extends('layouts.admin')
<style>
    .progress-bar.bg-progress {
        background: #108c36;
    }

    i.fa.fa-check.text-progress {
        color: #108c36;
    }
</style>
@section('content')

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Dashboard</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>
    <div>
        <!--<button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10"><i class="ti-settings text-white"></i></button>--->
    </div>
</div>

<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <!-- Row -->
    <div class="card-group">
        <!-- Column -->
        <!-- Column -->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <a href="{{ url('leads/closed') }}">
                        <div class="col-12">
                            <h2 class="m-b-0"><i class="fa fa-check text-progress"></i></h2>
                            <h3 class="">{{ $totalClosedLeads }}</h3>
                            <h6 class="card-subtitle">Campaign Total Closed Leads</h6>
                        </div>
                    </a>
                    <div class="col-12">
                        <div class="progress">
                            <div class="progress-bar bg-progress" role="progressbar" style="width: 100%; height: 6px;"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <a href="{{ url('leads/in_progress') }}">
                        <div class="col-12">
                            <h2 class="m-b-0"><i class="fa fa-check text-success"></i></h2>
                            <h3 class="">{{ $totalInprogressLeads }}</h3>
                            <h6 class="card-subtitle">Campaign Total Inprogress Leads</h6>
                        </div>
                    </a>
                    <div class="col-12">
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%; height: 6px;"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <a href="{{ url('leads/failed') }}">
                        <div class="col-12">
                            <h2 class="m-b-0"><i class="fa fa-exclamation-triangle text-danger"></i></h2>
                            <h3 class="">{{ $totalFailedLeads }}</h3>
                            <h6 class="card-subtitle">Campaign Total Failed Leads</h6>
                        </div>
                    </a>
                    <div class="col-12">
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%; height: 6px;"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <a href="{{ url('reminder/view') }}">
                        <div class="col-12">
                            <h2 class="m-b-0"><i class="fa fa-bell text-warning"></i></h2>
                            <h3 class="">{{ $todayReminders }}</h3>
                            <h6 class="card-subtitle">Today's Reminder</h6>
                        </div>
                    </a>
                    <div class="col-12">
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 100%; height: 6px;"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row -->
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">

                @if (Session::has('success'))
                    <div class="alert alert-success" role="alert">
                        {{Session::get('success')}}
                    </div>
                @elseif (Session::has('error'))
                    <div class="alert alert-danger" role="alert">
                        {{Session::get('error')}}
                    </div>
                @endif
                <div class="card card-outline-info">
                    <div class="card-header">
                        <h4 class="m-b-0 text-white">Campaigns</h4>

                    </div>

                    <div class="card-body">
                        <div class="table-responsive m-t-40" style="overflow-x: unset;">
                            <table id="campaign-table" class="display nowrap table table-hover table-striped table-bordered"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Campaign</th>
                                        <th>Sub-Campaign</th>
                                        <th>Leads</th>
                                        <th>Last Login</th>
                                        <th>Comments since last session</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#spinner-overlay').show();

    let table = $('#campaign-table').DataTable({
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
            { data: 'campaign_name', name: 'campaign_name', orderable: false, searchable: true },
            { data: 'description', name: 'description', orderable: false, searchable: true },
            { data: 'totalLeads', name: 'totalLeads', orderable: true, searchable: false },
            { data: 'last_login', name: 'last_login', orderable: true, searchable: false },
            { data: 'notes_count', name: 'notes_count', orderable: true, searchable: false },
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



</script>

@endsection