@extends('layouts.admin')
<style>
    .table td, .table th {
    padding:5px 0 !important;
    font-size: 12.5px;
    vertical-align: middle !important;
}
.table-striped tbody tr:nth-of-type(odd) {
    background: #f2f4f859 !important;
}
.table th {
    padding: 5px !important;
}
.wraping {
    height: auto !important;
}
 /* ankita 16-02-22 */
 .search_box {
    top: 200px !important;
    right: 20px !important;
}
.form-group.campaign_add {
    position: relative !important;
    top: 3% !important;
    right: 1%;
}
</style>

 
@section('content')

@php 


if (isset($_GET["status"]))
{

if(isset($_GET['status']) && '1' == $_GET['status']) {
    
    $title = "Pending Leads";
}else if($_GET['status'] == 3){
    
    $title = "Closed Leads";
}else if($_GET['status'] == 2){
    
    $title = "Failed Leads";
} else{
    $title = "Leads";
}

}  else{
    $title = "Leads";
}





@endphp


<div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-themecolor">Dashboard</h3>
                </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
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

                                <h4 class="m-b-0 text-white">{{ $title }}</h4>
                            </div>

                            <div class="card-body">
                                <!--<h4 class="card-title">Data Export</h4>
                                <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>-->

                                 <!-- <a type="button" href="{{ route('leads.create') }}" class="btn btn-success addButton"> + Add Lead</a> -->
                                 
                                    <form class="form-horizontal form-label-left input_mask" id="assignForm" method='post' action="">
                                            @csrf
                                            <div class="mian-dp">
                                                <div class="main-first">
                                                    <select  name="source_id" id="source_id" class="form-control custom-select" data-placeholder="Select Campaign" tabindex="1">
                                                        <option value="">Select Campaign</option>
                                                        @foreach($sources as $sources)
                                                            @if ($source_ids == $sources['id'])
                                                                <option value="{{ $sources['id'] }}" selected>{{ $sources['source_name'] }} ( {{ $sources['description'] }} )</option>
                                                            @else
                                                                <option value="{{ $sources['id'] }}">{{ $sources['source_name'] }} ( {{ $sources['description'] }} )</option>
                                                            @endif
                                                        @endforeach
                                                       
                                                    </select>
                                                    @if($errors->has('source_id'))
                                                        <div class="alert alert-danger">{{ $errors->first('source_id') }}</div>
                                                    @endif
                                                </div>
                                                <div class="main-second">
                                                    <button type="button" id="submitButton" class="btn btn-success" onclick="search();">Search</button>
                                                </div>
                                                
                                            </div>

                                            @if(Auth::user()->is_admin != 2)
                                            <div class="form-group campaign_add">
                                                <a type="button" href="{{ url('/add_leads').'/'.$source_ids }}"  class="btn btn-success addButton"> Import Leads + </a>
                                            </div>
                                            @endif
                                            

                                        </form>
                                       


                               
                                <div class="table-responsive m-t-40">
                                   
                                    <table  class="custom-seacrh-table" cellpadding="3" cellspacing="0" border="0" style="width: 67%; margin: 0 auto 2em auto;">
                                        <thead>
                                            <tr>
                                                <th>Target</th>
                                                <th>Search text</th>
                                            </tr>
                                        </thead>
                                     
                                    </table>
                                    <div class="table-responsive m-t-40">
                                    <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <!-- <th>Sr. No</th> -->
                                                <th>Campaign Name</th>
  
                                                <th>Company Name</th>
                                                <th>Prospect Name</th>
                                                <!-- <th>LinkedIn</th> -->
                                                <th>Time Zone</th>
                                                <th>Designation</th>
                                                <th>Phone No.</th>                                        
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th class="action_th"style="width: auto">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

               
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
var table; // Declare table globally

$(document).ready(function () {
    $('#spinner-overlay').show(); // Show spinner when processing starts

    // Initialize DataTable
    table = $('#example23').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: "{{ route('getsourceslead') }}",
            data: function (d) {
                d.source_id = $('#source_id').val(); // Pass source_id from filter
            }
        },
        columns: [
            { data: 'campaign_name', name: 'campaign_name' },
            { data: 'company_name', name: 'company_name' },
            { data: 'prospect_name', name: 'prospect_name', orderable: false, searchable: false },
            { data: 'timezone', name: 'timezone' },
            { data: 'designation', name: 'designation' },
            { data: 'contact_number_1', name: 'contact_number_1', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[6, 'desc']], // Default sort by Date column
        initComplete: function () {
            $('#spinner-overlay').hide(); // Hide spinner after table is fully initialized
        },
        preDrawCallback: function () {
            $('#spinner-overlay').show(); // Show spinner before table redraw
        },
        drawCallback: function () {
            $('#spinner-overlay').hide(); // Ensure spinner is hidden after each redraw
        }
    });

    // Listen for DataTable processing events
    $('#example23').on('processing.dt', function (e, settings, processing) {
        if (processing) {
            $('#spinner-overlay').show(); // Show spinner when processing starts
        } else {
            $('#spinner-overlay').hide(); // Hide spinner when processing ends
        }
    });
});

// Function to reload DataTable
function search() {
    table.ajax.reload(); // Reload DataTable with new data
}


    </script>
@endsection
