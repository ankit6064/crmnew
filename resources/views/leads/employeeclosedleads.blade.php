@extends('layouts.admin')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

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
            <li class="breadcrumb-item active">Failed Lead List </li>
        </ol>
    </div>
    <div>
    </div>
</div>
<input type="hidden" value="{{$id}}" id="source_id">
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
<input type="hidden" name="" id="status" value="<?php if(isset($_REQUEST['status']) && !empty($_REQUEST['status'])){ echo $_REQUEST['status'];} ?>">
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white">Leads</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive m-t-40" style="padding-bottom: 50px;">
                        <div class="">
                                                <label for="recipient-name" class="control-label">Search: </label>
                                                &nbsp; 
                                                <input type="text" placeholder="Global Search" class="global_filter newfilter" id="global_filter" name="search" value="">
                                                                                                <!-- <button class="btn btn-success"> Search </button> -->

                                            </div>
                                           
                        <table id="example23" class="display nowrap table table-hover table-striped table-bordered"
    cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Action</th>
            <th>Campaign Name
            <br>
                <select id="campaign_name" style="margin-top: 5px;" onchange="changecampaign();">
                    <option value="">Select Campaign</option>
                    <?php if (!empty($sourceNames)) {
                        foreach ($sourceNames as $sourceNames) {
                            if (!empty($sourceNames['source_name']) && $sourceNames['source_name'] != "0") { ?>
                                <option value="{{ $sourceNames['source_name'] }}" >
                                    {{ $sourceNames['source_name'] }}
                                </option>
                    <?php } } } ?>
                </select>
            </th>
            <th>Sub-Campaign Name
            </th>
            <th>Company Name <br>
                <select id="company_s" style="margin-top: 5px;">
                    <option value="">Select Company</option>
                    <?php if (!empty($comapnyName)) {
                        foreach ($comapnyName as $comapnyNameValue) {
                            if (!empty($comapnyNameValue['company_name']) && $comapnyNameValue['company_name'] != "0") { ?>
                                <option value="{{ $comapnyNameValue['company_name'] }}">
                                    {{ $comapnyNameValue['company_name'] }}
                                </option>
                    <?php } } } ?>
                </select>
            </th>
            <th>Prospect Name</th>
            <th>Time Zone <br>
                <select id="company_time" style="margin-top: 5px;">
                    <option value="">Select Time Zone</option>
                    <?php if (!empty($timeZone)) {
                        foreach ($timeZone as $timeZoneValue) {
                            if (!empty($timeZoneValue['timezone'])) { ?>
                                <option value="{{ $timeZoneValue['timezone'] }}">
                                    {{ $timeZoneValue['timezone'] }}
                                </option>
                    <?php } } } ?>
                </select>
            </th>
            <th>Designation</th>
            <th>Email Id</th>
            <th>Phone No.</th>
            <th>Status</th>
            <th>
                Closed On
                <br>

            <select id="closedon" style="margin-top: 5px;">
            <option value="">Select Date</option>

                    <?php if (!empty($closedon)) {
                        foreach ($closedon as $closeddates) {
                            if (!empty($closeddates['date'])) { ?>
                                <option value="{{ $closeddates['date'] }}">
                                    {{ date('d/m/Y', strtotime($closeddates['date'])) }}
                                </option>
                    <?php } } } ?>
                </select>
            </th>
        </tr>
    </thead>
    <tbody>
    </tbody>
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
                                </style>
                                <div class="modal-header">
                                    <h4 class="modal-title">Add Quick Note</h4>
                                    <button type="button" id="modelclose" class="close modal-close" data-dismiss="modal"
                                        aria-hidden="true" style="color:black">×</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group responseconvers">
                                        <div class="responseconversrespo">
                                            <input type="radio" class="conversation_type" id="NoResponse"
                                                name="conversation_type" value="NoResponse" checked="checked">
                                              <label for="NoResponse">VM/No Response</label><br>
                                        </div>
                                        <div class="responseconversrespo">
                                              <input type="radio" class="conversation_type" id="Conversation"
                                                name="conversation_type" value="Conversation">
                                              <label for="Conversation">Conversation</label><br>
                                        </div>
                                    </div>
                                    <div class="NoResponseData">
                                        <div class="form-group" id="status" name="status">
                                            <label class="control-label">Reminder Date</label>
                                            <input type="date" class="form-control" placeholder="Reminder Date"
                                                name="reminder_date" value="{{ old('reminder_date') }}" id="min-date"
                                                data-dtp="dtp_2827e">
                                            <label class="control-label">Reminder Time</label>
                                            <input type="time" class="form-control" id="reminder_time"
                                                name="reminder_time">
                                            <label class="control-label">Conversation Type</label>
                                            {{-- <input type="text" class="form-control required"
                                                placeholder="Reminder Type" id="reminder_for" name="reminder_for"
                                                value="{{ old('reminder_for') }}"> --}}
                                            <select id="reminder_for" class="form-control required" name="reminder_for">
                                                <option value="">Choose Conversation Type</option>
                                                <option value="Declined">Declined</option>
                                                <option value="DNC">DNC</option>
                                                <option value="Follow-up Call">Follow-up Call</option>
                                                <option value="Follow-up Email/Info Requested">Follow-up Email/Info
                                                    Requested</option>
                                                <option value="Meeting Set-up">Meeting Set-up</option>
                                                <option value="Not Interested">Not Interested</option>
                                                <option value="Not Right Party">Not Right Party</option>
                                                <option value="Reference Shared">Reference Shared</option>
                                            </select>
                                            <div class="alert alert-danger print-error-msg-1" style="display:none">
                                                <ul class="custom_text-1"></ul>
                                            </div>
                                            <label class="control-label">Note</label>
                                            <input type="hidden" class="form-control" name="lead_id"
                                                placeholder="Lead Id" value="{{isset($data['id'])}}">
                                            <textarea required type="text" class="form-control required" name="feedback"
                                                id="feedback" placeholder="Enter Note"
                                                style="min-height: 130px;">{{ old('note') }}</textarea>
                                            <div class="alert alert-danger print-error-msg" style="display:none">
                                                <ul class="custom_text"></ul>
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
                                        data-dismiss="modal">Close</button>
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
                <div class="alert alert-danger print-error-msg" style="display:none">
                    <ul></ul>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Select Status: </label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="4">In progress</option>
                            <option value="3">Closed</option>
                            <option value="2">Failed</option>
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
    $('#spinner-overlay').show(); // Show full-page spinner

    var sourceId = $('#source_id').val();
    var url = "{{ url('leads/employeeclosedleads') }}/";

    var table = $('#example23').DataTable({
        processing: false,
        serverSide: true,
        searching: true, // Enable search globally
        ordering: false , // Disable sorting

        ajax: {
            url: url,
            data: function (d) {
                d.timeZone = $('#company_time :selected').val();
                d.cName = $('#company_s :selected').val();
                d.orderBy = $('#note_time :selected').val();
                d.search = $('#global_filter').val();
                d.campaign_name = $('#campaign_name').val();
                d.status = $('#status').val();
                d.closedon = $('#closedon').val();

            },
            error: function (xhr, error, thrown) {
                console.error("AJAX Error:", xhr.responseText);
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'source_name', name: 'source_name', orderable: false },
            { data: 'source_description', name: 'source_description', orderable: false },
            { data: 'company_name', name: 'company_name', orderable: false },
            { data: 'prospect_first_name', name: 'prospect_first_name' },
            { data: 'timezone', name: 'timezone', orderable: false },
            { data: 'designation', name: 'designation' },
            { data: 'prospect_email', name: 'prospect_email' },
            { data: 'contact_number_1', name: 'contact_number_1' },
            { data: 'status', name: 'status' },
            { data: 'updated_at', name: 'updated_at' }
        ],
        rawColumns: ['action'] // Ensure HTML is rendered in the actions column
    });

    // Show spinner overlay on processing start
    table.on('preXhr.dt', function (e, settings, data) {
        $('#spinner-overlay').show(); // Show full-page spinner
    });

    // Hide spinner overlay when data is loaded
    table.on('xhr.dt', function (e, settings, json, xhr) {
        $('#spinner-overlay').hide(); // Hide full-page spinner
    });

    // Ensure filters work by putting event listener inside $(document).ready()
    $('#company_time').on('change', function() {
        console.log('Filter changed! Reloading table...');
        table.ajax.reload(); // Reload DataTable with new filter values
    });

    $('#company_s').on('change', function() {
        console.log('Filter changed! Reloading table...');
        table.ajax.reload(); // Reload DataTable with new filter values
    });
    
    $('#campaign_name').on('change', function() {
    console.log('Filter changed! Reloading table...');
    table.ajax.reload(); // Reload DataTable with new filter values
    });

    $('#closedon').on('change', function() {
        console.log('Filter changed! Reloading table...');
        table.ajax.reload(); // Reload DataTable with new filter values
    });
    
     $('#global_filter').on('keyup', function() {

        table.ajax.reload(); // Reload DataTable with new filter values
    });
});

    </script>

    <script>
        /* ============================ */
        $("#save-data-quick-note").click(function (event) {
            event.preventDefault();
            let feedback = $("[name=feedback]").val();
            var selectedVal = "";
            var selected = $("input[type=radio][name=conversation_type]:checked");
            if (selected.length > 0) {
                selectedVal = selected.val();
            }
            var selecteddata = ""
            if (selectedVal == 'Conversation') {
                selecteddata = $('#reminder_for').val();
            } else {
                selecteddata = 1;
            }

            if (selecteddata == 0) {
                $('.alert.alert-danger.print-error-msg-1').show();
                $('ul.custom_text-1').html('<li class="error_list"><span class="tab">Conversation Type Cannot Be Empty!</span></li>');
            } else if (feedback == 0) {
                $('.alert.alert-danger.print-error-msg-1').hide();
                $('.alert.alert-danger.print-error-msg').show();
                $('ul.custom_text').html('<li class="error_list"><span class="tab">Note Field Cannot Be Empty!</span></li>');
            } else {
                $('.alert.alert-danger.print-error-msg').hide();
                $('ul.custom_text').html('');
                let feedback = $("[name=feedback]").val();
                let reminder_date = $("[name=reminder_date]").val();
                let reminder_time = $("[name=reminder_time]").val();
                let source_id = $("[name=source_id]").val();
                console.log(reminder_time);
                let reminder_for = $("[name=reminder_for]").val();
                let lead_id = $("input[name=lead_id_quick_note]").val();
                let _token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{url("leads/add_note")}}',
                    type: "POST",
                    data: {
                        source_id: source_id,
                        reminder_date: reminder_date,
                        reminder_time: reminder_time,
                        reminder_for: reminder_for,
                        lead_id: lead_id,
                        feedback: feedback,
                        _token: _token
                    },
                    success: function (response) {
                        if ($.isEmptyObject(response.error)) {
                            console.log(response);
                            toastr.success(response.success, 'Success!');
                            $('#feedback').val('');
                            $('#min-date').val("");
                            $('#reminder_for').val("");
                            $('#reminder_time').val("");
                            $('.alert.alert-danger.print-error-msg-1').hide();
                            $('.alert.alert-danger.print-error-msg').hide();
                            $("#NoResponse").trigger("click");
                            $("#modelclose").trigger("click");
                        } else {
                            toastr.error(response.error, 'Error!');
                        }
                    },
                });
            }

            function printErrorMsg(msg) {
                console.log(msg);
                $(".print-error-msg").find("ul").html('');
                $(".print-error-msg").css('display', 'block');
                $(".print-error-msg").find("ul").append('<li>' + msg + '</li>');
            }
        });

        function shownoteslist(lead_id) {
            var url = '{{url("leads/notes_view")}}';
            var full_url = url + '/' + lead_id;
            $.ajax({
                url: full_url,
                type: "GET",
                data: {
                    lead_id: lead_id
                },
                success: function (response) {
                    $('#largeModal').modal('show');
                    if ($.isEmptyObject(response.error)) {
                        console.log(response.notes_data);
                        console.log(response.table);
                        $("#notes_data").html('');
                        $("#notes_data").html(response.table);
                    } else {
                        toastr.error(response.error, 'Error!');
                    }

                },
            });

        }




        $(document).ready(function () {
            $('#feedback').text('VM/No Response');
            $('input[type=radio][name=conversation_type]').change(function () {
                if (this.value == 'NoResponse') {
                    $('#feedback').val('VM/No Response');
                    $('#min-date').val("");
                    $('#reminder_for').val("");
                    $('#reminder_time').val("");
                    $('.alert.alert-danger.print-error-msg-1').hide();
                    $('.alert.alert-danger.print-error-msg').hide();
                } else if (this.value == 'Conversation') {
                    $('.alert.alert-danger.print-error-msg-1').hide();
                    $('.alert.alert-danger.print-error-msg').hide();
                    $('#feedback').val('');
                    $('#min-date').val("");
                    $('#reminder_for').val("");
                    $('#reminder_time').val("");
                }
            });
        });
    </script>

    <script>
        function showaddmodal(id) {
            $('#lead_id_quick_note').val(id);
            $('#status-modal-quicknote').modal('show');
        }

        $('.modal-close').on('click', function (event) {
            $('#status-modal-quicknote').modal('hide');
        });

        $('.largemodal-close').on('click', function (event) {
            $('#largeModal').modal('hide');
        });

        function showstatusmodal(id) {
            $('#lead_id').val(id);
            $('#status-modal').modal('show');

        }

        $('.close-status-modal').on('click', function (event) {
            $('#status-modal').modal('hide');
        });


        $(document).ready(function () {

            $("#save-data").click(function (event) {
                event.preventDefault();

                let status = $("select[name=status]").val();
                let lead_id = $("#lead_id").val();
                let _token = $('meta[name="csrf-token"]').attr('content');


                //alert(status+'--lead='+lead_id+'--token='+_token);

                $.ajax({
                    url: '{{url("changeStatus")}}',
                    type: "POST",
                    data: {
                        lead_id: lead_id,
                        status: status,
                        _token: _token
                    },
                    success: function (response) {

                        //console.log(response);

                        if ($.isEmptyObject(response.error)) {
                            console.log(response);
                            toastr.success(response.success, 'Success!')
                            if (response) {
                                $(".print-error-msg").css('display', 'none');
                                $('.success').text(response.success);
                                if (response.status == 'failed') {
                                    var Current_url = base_url + "/leads/failed";
                                    window.location.href = Current_url;
                                } else if (response.status == 'close') {
                                    var Current_url = base_url + "/leads/closed";
                                    window.location.href = Current_url;
                                } else {
                                    // var  Current_url = base_url+"/leads/closed";
                                    //  window.location.href = Current_url;
                                    location.reload(true); // inprogress
                                }
                                //location.reload(true);
                                $("#ajaxform")[0].reset();
                                //}else{
                                // printErrorMsg(response.error);
                            }

                        } else {
                            printErrorMsg(response.lhs_link);

                            //$('.error').text(response.error);
                            toastr.error(response.error, 'Error!');
                            // location.reload(true);
                            // toastr.error('errors messages');
                        }

                    },
                });


                function printErrorMsg(msg) {
                    console.log(msg);
                    $(".print-error-msg").find("ul").html('');
                    $(".print-error-msg").css('display', 'block');
                    //$.each( msg, function( key, value ) {
                    $(".print-error-msg").find("ul").append('<li>' + msg + '</li>');
                    // });
                }


            });

        });

       


    </script>


@endpush

@endsection