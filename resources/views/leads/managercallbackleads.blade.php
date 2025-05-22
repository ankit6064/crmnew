@extends('layouts.admin')
<style>
    .table td,
    .table th {
        padding: 5px 0 !important;
        font-size: 12.5px;
        vertical-align: middle !important;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background: #f2f4f859 !important;
    }

    .wraping {
        height: auto !important;
    }

    .table-responsive>.table-bordered {
        border: 1px solid #dee2e6 !important;
    }

    .label-new {
        display: inline-block;
        font-size: 15px !important;
        color: black !important;
        padding: 3px 10px;
        line-height: 13px;
    }

    div#example23_filter {
        display: block !important;
    }
</style>

@section('content')

    <style type="text/css">
        .icons {
            width: 40px;
        }
    </style>
    <?php date_default_timezone_set('Asia/Kolkata'); ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <?php
    if (@Request()->status == 'passed') {
        $name = 'Employee Passed Callback Lead List';
    } else {
        $name = 'Employee Callback Lead List';
    }
                    ?>
                <li class="breadcrumb-item active">{{ $name }}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <input type="hidden" name="passed_status" id="passed_status" value="{{ @Request()->status }}">

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
                        <h4 class="m-b-0 text-white">{{ $name }}</h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive m-t-40" style="padding-bottom: 50px;">
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>Lead Name</th>

                                        <th>
                                            Employee Name
                                            <br>
                                            <select id="employee_id" style="margin-top: 5px;">
                                                <option value="">Select Employee</option>
                                                <?php if (!empty($employee_list)) {
        foreach ($employee_list as $employee_details) {?>
                                                <option value="{{ $employee_details->id }}">
                                                    {{ $employee_details->first_name . ' ' . $employee_details->last_name}}
                                                </option>
                                                <?php    }
    } ?>
                                            </select>
                                        </th>

                                        <th>Campaign Name</th>
                                        <th>Campaign Description</th>
                                        <th>Callback Date</th>
                                        <th>Callback Time</th>
                                        <th>Note</th>

                                        <th>
                                            Status
                                            <br>
                                            <select id="callback_status" style="margin-top: 5px;">
                                                <option value="">Select Status</option>

                                                <option value="0">
                                                    Pending
                                                </option>
                                                <option value="1">
                                                    Completed
                                                </option>
                                                <option value="2">
                                                    Uncompleted
                                                </option>

                                            </select>
                                        </th>
                                    </tr>
                                </thead>

                            </table>

                        </div>
                    </div>
                </div>


                <!-- Quick Notes Add -->
                {{-- <form action="{{route('notes.store')}}" method="post"> --}}
                    {{-- @csrf --}}
                    <form id="form">
                        <div id="status-modal-quicknote" class="modal fade" tabindex="-1" role="dialog"
                            aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <meta name="csrf-token" content="{{ csrf_token() }}" />
                                    <div>
                                        <ul></ul>
                                    </div>
                                    <style type="text/css">
                                        .responseconversrespo {
                                            float: left;
                                            width: 50%;
                                            margin-bottom: 10px;
                                        }

                                        .modal-dialog {
                                            background: #fff;
                                            border-radius: 10px;
                                            background: none !important;
                                        }
                                    </style>
                                    <div class="modal-header">
                                        <h4 class="modal-title">Add Callback Note</h4>
                                        <button type="button" id="modelclose" class="close modal-close" data-dismiss="modal"
                                            aria-hidden="true" style="color:black" onclick="closemodal();">×</button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="NoResponseData">
                                            <div class="form-group" id="status" name="status">
                                                <input type="hidden" name="leadid" id="leadid">
                                                <input type="hidden" name="callbackid" id="callbackid">
                                                <input type="hidden" name="callbackstatus" id="callbackstatus">


                                                <textarea required type="text" class="form-control required" name="note"
                                                    id="note" placeholder="Enter Note"
                                                    style="min-height: 130px;">{{ old('note') }}</textarea>
                                                <div class="alert alert-danger print-error-msg" style="display:none">
                                                    <ul class="custom_text">Please Add Note First</ul>
                                                </div>
                                                @if($errors->has('status'))
                                                    <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                    </div>


                                    <div class="modal-footer">
                                        <input type="hidden" id="lead_id_quick_note" name="lead_id_quick_note">
                                        <button type="button" class="btn btn-default waves-effect modal-close"
                                            data-dismiss="modal" onclick="closemodal();">Close</button>
                                        {{-- <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i>
                                            Save</button> --}}
                                        <button id="save-data-quick-note" type="button"
                                            class="btn btn-info waves-effect waves-light ">Add Note</button>
                                    </div>
                                </div>
                    </form>
            </div>
        </div>



    </div>


    <div id="status-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="ajaxform">

                    <meta name="csrf-token" content="{{ csrf_token() }}" />

                    <div>
                        <ul></ul>
                    </div>

                    <div class="modal-header">
                        <h4 class="modal-title">Change Status</h4>
                        <button type="button" class="close close-status-modal" data-dismiss="modal" aria-hidden="true"
                            style="color:black">×</button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Select Status: </label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option style="display: none" value="4">In progress</option>
                                <option value="1">Pending</option>
                                <option style="display: none" value="3">Closed</option>
                                <option style="display: none" value="2">Failed</option>
                            </select>
                            @if($errors->has('status'))
                                <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="lead_id" name="lead_id">
                        <button type="button" class="btn btn-default waves-effect close-status-modal"
                            data-dismiss="modal">Close</button>
                        <button id="save-data" type="button" class="btn btn-info waves-effect waves-light ">Save
                            changes</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
    <!-- large modal -->
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">View Note</h4>
                    <button type="button" class="close largemodal-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color:black">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-body">
                        <input type="hidden" name="view_lead_id" value=<?php $lead_id = "";?>>
                    </div>
                    {{-- @else
                    <div> Empty data</div>
                    @endif --}}
                    <div class="table-responsive m-t-40" id="notes_data">



                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success largemodal-close" data-dismiss="modal">Close</button>
                <button type="button" style="display: none;" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
    </div>
    @push('scripts')
        <script src="{{url('vendor/moment/moment.js')}}"></script>

        <script src="{{url('vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}">
        </script>
        <script>
            $(document).ready(function () {
                $('#spinner-overlay').show(); // Show full-page spinner when document is ready

                var table = $('#example23').DataTable({
                    processing: false,
                    serverSide: true,
                    searching: false,
                    ordering: false,
                    ajax: {
                        url: "{{ url('leads/managercallbackleads') }}",
                        data: function (d) {
                            d.employee_id = $('#employee_id').val(); // Send extra parameter to server
                            d.callback_status = $('#callback_status').val();
                            d.passed_status = $('#passed_status').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                        { data: 'lead_name', name: 'lead_name' },
                        { data: 'employee_name', name: 'employee_name' },
                        { data: 'source_name', name: 'source_name' },
                        { data: 'description', name: 'description' },
                        { data: 'callback_date', name: 'callback_date' },
                        { data: 'callback_time', name: 'callback_time' },
                        { data: 'note', name: 'note' },
                        { data: 'status', name: 'status' }
                    ]
                });

                // Show spinner overlay on processing start
                table.on('preXhr.dt', function (e, settings, data) {
                    $('#spinner-overlay').show(); // Show full-page spinner when data is being requested
                });

                // Hide spinner overlay when data is loaded
                table.on('xhr.dt', function (e, settings, json, xhr) {
                    $('#spinner-overlay').hide(); // Hide full-page spinner when data has been loaded
                });

                // Show spinner on page change (redraw)
                table.on('draw.dt', function () {
                    $('#spinner-overlay').show(); // Show spinner on page change
                });

                // Hide spinner after each page draw is completed
                table.on('draw.dt', function () {
                    $('#spinner-overlay').hide(); // Hide spinner after data is rendered
                });
                $('#employee_id').on('change', function () {
                    table.ajax.reload(); // Reload DataTable with new filter values
                });
                $('#callback_status').on('change', function () {
                    table.ajax.reload(); // Reload DataTable with new filter values
                });
            });


        </script>


        <script>
            function changecallbackstatus(callbackid, leadid, status) {
                $('#leadid').val(leadid);
                $('#callbackid').val(callbackid);
                $('#callbackstatus').val(status);
                $('#status-modal-quicknote').modal('show');

            }

            function closemodal() {
                $('#status-modal-quicknote').modal('hide');
                $('#note').val('');
                $('.print-error-msg').css('display', 'none');
            }




        </script>


    @endpush

@endsection