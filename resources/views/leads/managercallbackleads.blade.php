@extends('layouts.admin')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<style>
    .filter-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .filter-select,
    #global_filter {
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .graph tbody tr.odd td:last-child {
        display: table-cell !important;
        gap: 11px;
        align-items: center;
    }
</style>

@section('content')



    <input type="hidden" id="passed_status" value="{{ @Request()->status }}">

    <div class="main-right">
        <div class="right-side submanager">

        <div class="row align-items-center mb-3">
                <div class="col-md-8">
                    <h2 class="mb-0">Callback Leads</h2>
                </div>

                <div class="col-md-4 text-end">
                    <button type="button" class="btn return-btn"
                        onclick="window.history.back() || (window.location.href='/managerdashboard');">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </button>
                </div>
            </div>

            <div class="graph campaignslist">

                <!-- 🔽 Filters -->
                <div class="filter-row">

                    <select id="employee_id" class="filter-select">
                        <option value="">All Employees</option>
                        @foreach($employee_list as $emp)
                            <option value="{{ $emp->id }}">
                                {{ $emp->first_name }} {{ $emp->last_name }}
                            </option>
                        @endforeach
                    </select>

                    <select id="callback_status" class="filter-select">
                        <option value="">All</option>
                        <option value="0">Pending</option>
                        <option value="1">Completed</option>
                        <option value="2">Uncompleted</option>
                    </select>

                    <!-- Reset Button -->
                    <button id="resetFilters" class="btn btn-primary" style="background-color: #e71d1d;">Reset</button>

                </div>

                <!-- 📊 Table -->
                <div class="table">
                    <div class="table-container">
                        <table id="employee-table" class="table table-striped table-hover">
                            <thead class="thead-main">
                                <tr>
                                    <th>Lead Name</th>
                                    <th>Employee</th>
                                    <th>Campaign</th>
                                    <th>Description</th>
                                    <th>Callback Date</th>
                                    <th>Time</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

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

    <!--  -->

    <form id="form">
        <div id="status-modal-quicknote" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" style="display: none;">
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


                                <textarea required type="text" class="form-control required" name="note" id="note"
                                    placeholder="Enter Note" style="min-height: 130px;">{{ old('note') }}</textarea>
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
                        <button type="button" class="btn btn-default waves-effect modal-close" data-dismiss="modal"
                            onclick="closemodal();">Close</button>
                        {{-- <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i>
                            Save</button> --}}
                        <button id="save-data-quick-note" type="button" class="btn btn-info waves-effect waves-light ">Add
                            Note</button>
                    </div>
                </div>
    </form>

    @push('scripts')
        <script src="{{url('vendor/moment/moment.js')}}"></script>

        <script src="{{url('vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}">
        </script>
        <script>
            $(document).ready(function () {
                $('#spinner-overlay').show(); // Show full-page spinner when document is ready

                var table = $('#employee-table').DataTable({
                    processing: false,
                    serverSide: true,
                    searching: false,
                    ordering: true,
                    ajax: {
                        url: "{{ url('leads/managercallbackleads') }}",
                        data: function (d) {
                            d.employee_id = $('#employee_id').val(); // Send extra parameter to server
                            d.callback_status = $('#callback_status').val();
                            d.passed_status = $('#passed_status').val();
                        }
                    },
                    columns: [
                        { data: 'lead_name', name: 'prospect_first_name', sortable: true },
                        { data: 'employee_name', name: 'users.first_name', sortable: false },
                        { data: 'source_name', name: 'source_name', sortable: false },
                        { data: 'description', name: 'description', sortable: false },
                        { data: 'callback_date', name: 'callback_date', sortable: false },
                        { data: 'callback_time', name: 'callback_time', sortable: false },
                        { data: 'note', name: 'note', sortable: false },
                        { data: 'status', name: 'status', sortable: false }
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
                $('#resetFilters').click(function () {
                    $('#employee_id').val('');
                    $('#callback_status').val('');

                    table.draw(); // reload data
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