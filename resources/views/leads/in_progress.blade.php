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
            <li class="breadcrumb-item active">In-Progress Lead List </li>
        </ol>
    </div>
    <div>
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
                    <h4 class="m-b-0 text-white">In-Progress Lead List</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive m-t-40" style="padding-bottom: 50px;">
                        <table id="example23" class="display nowrap table table-hover table-striped table-bordered"
                            cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Campaign Name</th>
                                    <th>Sub-Campaign Name</th>
                                    <th>Company Name</th>
                                    <th>Prospect Name</th>
                                    <th>Time Zone</th>
                                    <th>Designation</th>
                                    <th>Phone No.</th>
                                    <th>Date</th>
                                    <th>Last Updated Note</th>
                                    <th>Action</th>
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
                                            @if(Auth::user()->is_admin == 1)
                                            <label class="control-label">Phone Number</label>
                                            <input type="tel" class="form-control" placeholder="Phone Number" name="phone_number" value="" id="phone_number" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number">
                                            @else
                                            <input type="tel" class="form-control" placeholder="Phone Number" name="phone_number" value="" id="phone_number" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" style="display:none">

                                            @endif
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
                                            <button type="button" class="close close-status-modal" data-dismiss="modal" aria-hidden="true" style="color:black">×</button>
                                        </div>
                                          <div class="alert alert-danger print-error-msg" style="display:none">
                                           <ul></ul>
                                            </div>
                                        <div class="modal-body">
                                            
                                                <div class="form-group">
                                                    <label for="recipient-name" class="control-label">Select Status: </label>
                                                    <select class="form-control" id="status" name="status" required>
                                                    <option value="">Select Status</option>
                                                    <option style="display: none" value="4">In progress</option>
                                                        <option value="1">Pending</option>
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
                                            <button type="button" class="btn btn-default waves-effect close-status-modal" data-dismiss="modal" >Close</button>
                                            <button id="save-data" type="button" class="btn btn-info waves-effect waves-light ">Save changes</button>
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
        $('#spinner-overlay').show(); // Show full-page spinner on page load

        var table = $('#example23').DataTable({
            processing: false,
            serverSide: true,
            ajax: "{{ url('leads/in_progress') }}",
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'source.source_name', name: 'source.source_name' },
                { data: 'source.description', name: 'source.description' },
                { data: 'company_name', name: 'company_name' },
                { data: 'prospect_first_name', name: 'prospect_first_name' },
                { data: 'timezone', name: 'timezone' },
                { data: 'designation', name: 'designation' },
                { data: 'contact_number_1', name: 'contact_number_1' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'last_updated_note', name: 'last_updated_note', orderable: false, searchable: false },
                { data: 'options', name: 'options' }
            ]
        });

        // Show spinner overlay on processing start (including page change, sorting, etc.)
        table.on('preXhr.dt', function(e, settings, data) {
            $('#spinner-overlay').show(); // Show full-page spinner
        });

        // Hide spinner overlay when data is loaded
        table.on('xhr.dt', function(e, settings, json, xhr) {
            $('#spinner-overlay').hide(); // Hide full-page spinner
        });

        // Show spinner overlay when page is changed (pagination)
        table.on('page.dt', function() {
            $('#spinner-overlay').show(); // Show spinner during pagination
        });

        // Hide spinner when the page is fully loaded
        table.on('draw.dt', function() {
            $('#spinner-overlay').hide(); // Hide spinner after drawing the table
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
                        phone_number : $('#phone_number').val(),
                        _token: _token
                    },
                    success: function (response) {
                        if ($.isEmptyObject(response.error)) {
                            console.log(response);
                            toastr.success(response.success, 'Success!');
                            if(selectedVal != 'NoResponse'){
                                $('#feedback').val('');

                            }else{
                                $('#feedback').val('');
                                $('#feedback').val('VM/No Response');
                            }
                            $('#phone_number').val('');
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


        $("#status_search").change(function () {
            if ($(this).val() == "0") {
                $('.filter_call').removeClass('show');
                $('#filter_col0').addClass('show');
            }
            else if ($(this).val() == "1") {
                $('.filter_call').removeClass('show');
                $('#filter_col1').addClass('show');
            } else if ($(this).val() == "2") {
                $('.filter_call').removeClass('show');
                $('#filter_col2').addClass('show');
            }
            else if ($(this).val() == "3") {
                $('.filter_call').removeClass('show');
                $('#filter_col3').addClass('show');
            }
            else if ($(this).val() == "5") {
                $('.filter_call').removeClass('show');
                $('#filter_col4').addClass('show');
            }
            else if ($(this).val() == "6") {
                $('.filter_call').removeClass('show');
                $('#filter_col6').addClass('show');
            }
            else if ($(this).val() == "7") {
                $('.filter_call').removeClass('show');
                $('#filter_col7').addClass('show');
            }
            else if ($(this).val() == "8") {
                $('.filter_call').removeClass('show');
                $('#filter_col8').addClass('show');
            }
            else if ($(this).val() == "9") {
                $('.filter_call').removeClass('show');
                $('#filter_col9').addClass('show');
            }
        });

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

        function showstatusmodal(id){
            $('#lead_id').val(id);
            $('#status-modal').modal('show');

        }

        $('.close-status-modal').on('click', function (event) {
            $('#status-modal').modal('hide');
        });


        $( document ).ready(function() {

$("#save-data").click(function(event){
  event.preventDefault();
 
  let status = $("select[name=status]").val(); 
  let lead_id = $("input[name=lead_id]").val(); 
  let _token   = $('meta[name="csrf-token"]').attr('content');


//alert(status+'--lead='+lead_id+'--token='+_token);

  $.ajax({
    url: '{{url("changeStatus")}}',
    type:"POST",
    data:{
      lead_id:lead_id,
      status:status,
      _token: _token
    },
    success:function(response){

        //console.log(response);

        if($.isEmptyObject(response.error)){
            console.log(response);
            toastr.success(response.success,'Success!')
              if(response) {
                 $(".print-error-msg").css('display','none');
                $('.success').text(response.success);
                if(response.status == 'failed'){
                    var  Current_url = base_url+"/leads/failed";
                    window.location.href = Current_url;
                }else if (response.status == 'close'){
                    var  Current_url = base_url+"/leads/closed";
                    window.location.href = Current_url;
                }else{
                // var  Current_url = base_url+"/leads/closed";
                //  window.location.href = Current_url;
                location.reload(true); // inprogress
                }
                //location.reload(true);
                $("#ajaxform")[0].reset();
              //}else{
               // printErrorMsg(response.error);
              }

        }else{
            //printErrorMsg(response.error);
            
             //$('.error').text(response.error);
              toastr.error(response.error,'Error!');
              // location.reload(true);
           // toastr.error('errors messages');
        }

    },
   });


  function printErrorMsg (msg) {
    console.log(msg);
        $(".print-error-msg").find("ul").html('');
        $(".print-error-msg").css('display','block');
        //$.each( msg, function( key, value ) {
            $(".print-error-msg").find("ul").append('<li>'+msg+'</li>');
       // });
    }


});

});


    </script>


@endpush

@endsection