@extends('layouts.admin')
<style>
    .table td,
    .table th {
        padding: 5px 0 !important;
        font-size: 12.5px;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background: #f2f4f859 !important;
    }

    .form-control {
        padding: 2 !important;
    }

    ul.pagination {
        float: right;
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
            <li class="breadcrumb-item active">Daily Report </li>
        </ol>
    </div>
    <div>
        <!--<button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10"><i class="ti-settings text-white"></i></button>--->
    </div>
</div>
@php
    $urls = '?employee_id=' . request()->get('employee_id') . '&campaign_id=' . request()->get('campaign_id') . '&date_from=' . request()->get('date_from') . '&date_to=' . request()->get('date_to') . '&filter_by=' . request()->get('filter_by') . '&reminder_for_conversation=' . request()->get('reminder_for_conversation');
@endphp
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
                    <h4 class="m-b-0 text-white">Daily Report </h4>
                </div>
                <form method="get" id="searchform" action="{{ url('/employee/' . Auth::id() . '/daily_report') }}">

                <div class="card-body">
                    <div class="row p-t-20">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Campaign Name</label>
<select class="form-control"  name="campaign_id" id="campaign_id">
                                            <option value="" selected>Select Campaign</option>
                                                  @foreach($campaigns as $campaigns)
                                                      @if ($campaign_id == $campaigns['source']['id'])
                                                          <option class="optionselcted" value="{{ $campaigns['source']['id'] }}" selected>{{ $campaigns['source']['source_name'] }} ({{ $campaigns['source']['description'] }})</option>
                                                      @else 
                                                          <option class="optionselcted" value="{{ $campaigns['source']['id'] }}">{{ $campaigns['source']['source_name'] }}  ({{ $campaigns['source']['description'] }})</option>
                                                      @endif 
                                                  @endforeach
                                          </select>                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Date From</label>
                                <input type="datetime-local" class="form-control" placeholder="Date From"
                                    name="date_from" value="{{ old($date_from) }}" id="date_from_new">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Date To</label>
                                <input type="datetime-local" class="form-control" placeholder="Date To" name="date_to"
                                    value="{{ old($date_to) }}" id="date_to_new">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by">
                                    <option value="">Select Filter By</option>
                                    <option value="1">VM/No Response</option>
                                    <option value="2">Conversation</option>
                                </select>
                            </div>
                        </div>
                        </form>

                        <div class="col-md-3">
                            <div class="form-group">
                                <br>
                                <button type="button" id="sub_cmap" class="btn btn-success">Search</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="
                                    display: flex;
                                    justify-content: flex-end;
                                    width: 100%;">
                        <br>
                     <!-- <a type="button" href="{{ url('/employee/' . Auth::id() . '/daily_report' . $urls) }}" class="btn btn-success addButton"> Export Report </a> -->
                     <a type="button" href="#"
                            class="btn btn-success addButton" onclick="exportreport();"> Export Report </a>
                    </div>

                    <!--<h4 class="card-title">Data Export</h4>
                                <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>-->

                    <div class="table-responsive m-t-40" id="table_data">

                        <table id="leadsTable" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Sr. no</th>
                                    <th>Lead Name</th>
                                    <th>Conversation Type</th>
                                    <th>Note</th>
                                    <th>Note Date & Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table> 
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script type="text/javascript">
$(document).ready(function () {
    $('#spinner-overlay').show(); // Show full-page spinner initially

    var table = $('#leadsTable').DataTable({
        processing: false,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ url('empdailyreport') }}",
            data: function (d) {
                d.campaign_id = $('#campaign_id').val();
                d.date_from = $('#date_from_new').val();
                d.date_to = $('#date_to_new').val();
                d.filter_by = $('#filter_by').val();
            }
        },
        
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // Sr. No column
            { data: 'lead_name', name: 'lead_name' },
            { data: 'conversation_type', name: 'conversation_type' },
            { data: 'note', name: 'note' },
            { data: 'note_date_time', name: 'note_date_time' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']], // Ensure ordering does not affect Sr. No
        columnDefs: [
            { targets: 0, className: 'text-center' } // Center align Sr. No column
        ],
        
        preDrawCallback: function () {
            $('#spinner-overlay').show(); // Show loader before rendering new page
        },
        initComplete: function () {
            $('#spinner-overlay').hide(); // Hide spinner after table is fully initialized
        },
        drawCallback: function () {
            $('#spinner-overlay').hide(); // Hide spinner after each redraw
        }
    });

    // Show loader when page length or pagination is changed
    $('#leadsTable').on('page.dt length.dt', function () {
        $('#spinner-overlay').show();
    });
});


    $(document).on("click", "#sub_cmap", function () {
        $('#leadsTable').DataTable().ajax.reload();

    });

    function exportreport() {
        $('#searchform').submit(); 
    }
</script>
<!-- <script>
   

    $(document).ready(function () {
        var defaultDateTime = moment(new Date().toJSON().slice(0, 10)).add(6, 'hours').format('YYYY-MM-DDThh:mm');
        $("#date_from_new").val(defaultDateTime);
        $("#date_to_new").val(defaultDateTime);
        $(document).on('click', '.pagination a', function (event) {
            event.preventDefault();

            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            var url = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            console.log(page);
            fetch_data(page);
        });

     

    });
</script> -->



@endsection