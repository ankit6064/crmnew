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
    th[data-orderable="false"]::before,
th[data-orderable="false"]::after {
    display: none !important;
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
            <th data-orderable="false">Action</th>
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
                @if(isset($_REQUEST['status']) && $_REQUEST['status'] == 3)
                Closed On
                @else
                Updated On
                @endif
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
                                        aria-hidden="true">×</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label class="custom-control-inline mr-3" style="cursor:pointer; font-weight: 500;">
                                            <input type="radio" name="conversation_type" value="NoResponse" checked> VM / No Response
                                        </label>
                                        <label class="custom-control-inline" style="cursor:pointer; font-weight: 500;">
                                            <input type="radio" name="conversation_type" value="Conversation"> Conversation
                                        </label>
                                    </div>

                                    <div class="NoResponseData">
                                        <div class="form-group mb-0" id="status" name="status">
                                            <div id="conversation_type_container" style="display:none; margin-bottom: 12px;">
                                                <div style="display:flex; gap:12px; align-items:flex-start;">
                                                    <div style="flex:1;">
                                                        <label class="control-label" style="font-weight:600; margin-bottom: 5px;">Conversation Type</label>
                                                        <select id="reminder_for" class="form-control required" name="reminder_for"
                                                            onchange="checkQuickType(); updateSubConversationType();">
                                                            <option value="">Choose Type</option>
                                                            <option value="Follow up">Follow up</option>
                                                            <option value="Declined">Declined</option>
                                                            <option value="Meeting Setup">Meeting Setup</option>
                                                        </select>
                                                    </div>
                                                    <div style="flex:1; display:none;" id="sub_conversation_col">
                                                        <label class="control-label" style="font-weight:600; margin-bottom: 5px;">Sub Conversation Type</label>
                                                        <select id="sub_reminder_for" class="form-control" name="sub_reminder_for">
                                                            <option value="">Choose Sub Type</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="reminderdatetime" style="margin-bottom: 12px;">
                                                <div class="form-group mb-2">
                                                    <label class="control-label">Reminder Date</label>
                                                    <input type="date" class="form-control" name="reminder_date" id="min-date">
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label class="control-label">Reminder Time</label>
                                                    <input type="time" class="form-control" id="reminder_time" name="reminder_time">
                                                </div>
                                            </div>

                                            <div id="callbackdatetime" style="display:none; margin-bottom: 12px;">
                                                <div class="form-group mb-2">
                                                    <label class="control-label">Callback Date</label>
                                                    <input type="date" class="form-control" name="callback_date" id="callback_date">
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label class="control-label">Callback Time</label>
                                                    <input type="time" class="form-control" id="callback_time" name="callback_time">
                                                </div>
                                            </div>

                                            <div class="form-group mb-2" id="phone_number_container">
                                                <label class="control-label">Phone Number</label>
                                                <input type="tel" class="form-control" name="phone_number" id="phone_number" placeholder="Phone Number">
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="control-label">Note</label>
                                                <textarea required class="form-control required" name="feedback" id="feedback"
                                                    placeholder="Enter Note" style="min-height:130px;"></textarea>
                                            </div>

                                            <div class="alert alert-danger print-error-msg" style="display:none; margin-top: 10px;">
                                                <ul class="custom_text mb-0"></ul>
                                            </div>

                                            <div class="alert alert-danger print-error-msg-1" style="display:none; margin-top: 10px;">
                                                <ul class="custom_text-1 mb-0"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:10px; border-top: 1px solid #dee2e6; padding: 12px 20px;">
                                    <input type="hidden" id="lead_id_quick_note" name="lead_id_quick_note">
                                    <button type="button" class="btn btn-default waves-effect modal-close"
                                        data-dismiss="modal">Close</button>
                                    <button id="save-data-quick-note" type="button"
                                        class="btn btn-info waves-effect waves-light">Add Note</button>
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
                    <button type="button" class="close close-status-modal" data-dismiss="modal" aria-hidden="true">×</button>
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
                    <span aria-hidden="true">&times;</span>
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
    <script src="{{url('js/conversation-types.js')}}"></script>
    <script src="{{url('vendor/moment/moment.js')}}"></script>

    <script src="{{url('vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}">
    </script>
    <script>
        $(document).ready(function () {
    $('#spinner-overlay').show(); // Show full-page spinner

    var sourceId = $('#source_id').val();
    var url = "{{ url('leadslist') }}/" + sourceId;

    var table = $('#example23').DataTable({
        processing: false,
        serverSide: true,
        searching: true, // Enable search globally
        ordering: true , // Disable sorting

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
            { data: 'description', name: 'description', orderable: true },
            { data: 'company_name', name: 'company_name', orderable: false },
            { data: 'prospect_first_name', name: 'prospect_first_name',orderable:true },
            { data: 'timezone', name: 'timezone', orderable: false },
            { data: 'designation', name: 'designation',orderable:true },
            { data: 'prospect_email', name: 'prospect_email',orderable:false },
            { data: 'contact_number_1', name: 'contact_number_1',orderable:false },
            { data: 'status', name: 'status',orderable:false },
            { data: 'updated_at', name: 'leads.updated_at',orderable:true }
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
                var parentType = $('#reminder_for').val();
                    selecteddata = $('#sub_reminder_for').val() || parentType;
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
                let reminder_for = $('#sub_reminder_for').val() || $('[name=reminder_for]').val();
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
                        phone_number: $('#phone_number').val(),
                        callback_date: $('#callback_date').val(),
                        callback_time: $('#callback_time').val(),
                        _token: _token
                    },
                    success: function (response) {
                        if ($.isEmptyObject(response.error)) {
                            console.log(response);
                            toastr.success(response.success, 'Success!');
                            $('#feedback').val('');
                            $('#min-date').val("");
                            $('#reminder_for').val("");
                        $('#sub_reminder_for').empty().append('<option value="">Choose Sub Type</option>');
                        $('#sub_conversation_col').hide();
                            $('#reminder_time').val("");
                            $('.alert.alert-danger.print-error-msg-1').hide();
                            $('.alert.alert-danger.print-error-msg').hide();
                            $("#NoResponse").trigger("click");
                            $("#modelclose").trigger("click");
                        } else {
                            toastr.error(response.error, 'Error!');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error, 'Error!');
                        } else {
                            toastr.error("Something went wrong", "Error!");
                        }
                    }
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
            $('#spinner-overlay').show();
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
                error: function() {
                    toastr.error('Something went wrong', 'Error!');
                },
                complete: function() {
                    $('#spinner-overlay').hide();
                }
            });
        }




        $(document).ready(function () {
            $('#feedback').text('VM/No Response');
            $('input[type=radio][name=conversation_type]').change(function () {
                if (this.value == 'NoResponse') {
                    $('#feedback').val('VM/No Response');
                    $('#min-date').val("");
                    $('#reminder_for').val("");
                        $('#sub_reminder_for').empty().append('<option value="">Choose Sub Type</option>');
                        $('#sub_conversation_col').hide();
                    $('#reminder_time').val("");
                    $('.alert.alert-danger.print-error-msg-1').hide();
                    $('.alert.alert-danger.print-error-msg').hide();
                    $('#conversation_type_container').hide();
                } else if (this.value == 'Conversation') {
                    $('.alert.alert-danger.print-error-msg-1').hide();
                    $('.alert.alert-danger.print-error-msg').hide();
                    $('#feedback').val('');
                    $('#min-date').val("");
                    $('#reminder_for').val("Follow up");
                    $('#reminder_time').val("");
                    $('#phone_number').val('');
                    $('#callback_date').val('');
                    $('#callback_time').val('');
                    $('#conversation_type_container').show();
                    updateSubConversationType();
                    checkQuickType();
                }
            });
        });

        var subOptions = {
            'Follow up': [
                {value: 'Follow-up Call', label: 'Call'},
                {value: 'Follow-up Email', label: 'Email'},
                {value: 'Follow-up Text', label: 'Text'}
            ],
            'Declined': [
                {value: 'Declined - No Opportunity', label: 'No Opportunity'},
                {value: 'Declined - DNC', label: 'DNC'},
                {value: 'Declined - Not Right Party', label: 'Not Right Party'},
                {value: 'Declined - Left the Organization', label: 'Left the Organization'},
                {value: 'Declined - Reference Shared', label: 'Reference Shared'}
            ],
            'Meeting Setup': [
                {value: 'Meeting Setup', label: 'Meeting Setup'}
            ]
        };

        function updateSubConversationType() {
            var parent = $('#reminder_for').val();
            var $sub = $('#sub_reminder_for');
            $sub.empty().append('<option value="">Choose Sub Type</option>');
            if (parent && subOptions[parent]) {
                $.each(subOptions[parent], function(i, opt) {
                    $sub.append('<option value="' + opt.value + '">' + opt.label + '</option>');
                });
                if (subOptions[parent].length > 0) {
                    $sub.val(subOptions[parent][0].value);
                }
                $('#sub_conversation_col').show();
            } else {
                $('#sub_conversation_col').hide();
            }
        }
    </script>

    <script>
        function showaddmodal(id) {
            $('#lead_id_quick_note').val(id);
            $('input[name="conversation_type"][value="NoResponse"]').prop('checked', true);
            $('#min-date').val('');
            $('#reminder_time').val('');
            $('#reminder_for').val('');
                $('#sub_reminder_for').empty().append('<option value="">Choose Sub Type</option>');
                $('#sub_conversation_col').hide();
            $('#feedback').val('VM/No Response');
            $('#conversation_type_container').hide();
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
            $('#status-modal .print-error-msg').hide();
            $('#status-modal .print-error-msg ul').html('');
            $('#status').val('');
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
                            if (response.lhs_link) {
                                $('#status-modal .print-error-msg').show();
                                $('#status-modal .print-error-msg ul').html(response.lhs_link);
                            } else {
                                $('#status-modal .print-error-msg').show();
                                $('#status-modal .print-error-msg ul').html('<li>' + response.error + '</li>');
                            }

                            toastr.error(response.error, 'Error!');
                        }

                    },
                });

        });

       


    </script>


@endpush

@endsection