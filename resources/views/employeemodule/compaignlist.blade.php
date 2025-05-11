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
            <li class="breadcrumb-item active">Campaigns</li>
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
                        <h4 class="m-b-0 text-white">Active Campaigns</h4>

                    </div>

                    <div class="card-body">
                        <div class="table-responsive m-t-40" style="overflow-x: unset;">
                            <table id="campaign-table" class="display nowrap table table-hover table-striped table-bordered"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Campaign Name</th>
                                        <th>Sub-Campaign Name</th>
                                        <th>Total Assigned Leads</th>
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
    // Show the loader initially
    $('#spinner-overlay').show();

    $('#campaign-table').DataTable({
        processing: false,
        serverSide: true,
        searching: false,
        ajax: {
            url: '{{ route("camp_assign_list") }}',
            type: 'GET',
            error: function (xhr, error, thrown) {
                console.error('Error:', xhr.responseText);
                alert('An error occurred while loading data.');
            }
        },
        columns: [
            { data: 'source_name', name: 'source_name', orderable: false, searchable: true },
            { data: 'description', name: 'description', orderable: false, searchable: true },
            { data: 'totalLeads', name: 'totalLeads', orderable: true, searchable: false },
        ],
        // Show spinner before each table redraw (pagination, sort, etc.)
        preDrawCallback: function () {
            $('#spinner-overlay').show();
        },
        // Hide spinner after table redraw is complete
        drawCallback: function () {
            $('#spinner-overlay').hide();
        },
        // Hide spinner after table is fully initialized
        initComplete: function () {
            $('#spinner-overlay').hide();
        }
    });
});



</script>

@endsection