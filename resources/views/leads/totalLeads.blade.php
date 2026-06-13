@extends('layouts.admin')
@section('content')
    <style>
        .responseconversrespo {
            float: left;
            width: 50%;
            margin-bottom: 10px;
        }

        i.fa {
            color: black;
        }
        .label-new {
            padding: 6px;
            border-radius: 5px;
            color: #000;
        }

        .shake-note i {
            animation: shake 1s infinite;
        }

        @keyframes shake {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-2px);
            }

            50% {
                transform: translateX(2px);
            }

            75% {
                transform: translateX(-2px);
            }

            100% {
                transform: translateX(0);
            }
        }
        .form-control {
            min-width: 100%;

        }
    </style>
    <?php date_default_timezone_set('Asia/Kolkata'); ?>

    <div class="main-right">
        <div class="right-side submanager">
        <div class="row">
                <div class="row align-items-center mb-3">
                    <div class="col-md-8">
                        <h2 class="mb-0">Total Leads Listing</h2>
                    </div>

                    <div class="col-md-4 text-end">
                        <button type="button" class="btn return-btn"
                            onclick="window.history.back() || (window.location.href='{{ route('employeedashboard') }}');">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </button>
                    </div>
                </div>

            </div>
            <div class="graph campaignslist logstable lead-listing total-leads">
                <div class="table">
                    <div class="table-container">
                        <table class="table table-striped table-hover" id="employee-table">
                            <thead class="thead-main">
                                <tr>
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
        </div>
    </div>


    <!-- Quick Notes Add -->
    <form id="form">
        <div id="status-modal-quicknote" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <meta name="csrf-token" content="{{ csrf_token() }}" />

                    <div class="modal-header">
                        <h4 class="modal-title">Add Quick Note</h4>
                        <button type="button" id="modelclose" class="close modal-close" data-dismiss="modal">
                            ×
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group responseconvers">

                            <div class="responseconversrespo">
                                <input type="radio" class="conversation_type" id="NoResponse" name="conversation_type"
                                    value="NoResponse" checked>
                                <label for="NoResponse">VM/No Response</label>
                            </div>

                            <div class="responseconversrespo">
                                <input type="radio" class="conversation_type" id="Conversation" name="conversation_type"
                                    value="Conversation">
                                <label for="Conversation">Conversation</label>
                            </div>

                        </div>


                        <div class="NoResponseData">

                            <div class="form-group" id="status" name="status">

                                <label class="control-label">Conversation Type</label>

                                <select id="reminder_for" class="form-control required" name="reminder_for"
                                    onchange="checktype();">

                                    <option value="">Choose Conversation Type</option>
                                    <option value="Callback">Callback</option>
                                    <option value="Declined">Declined</option>
                                    <option value="DNC">DNC</option>
                                    <option value="Follow-up Call">Follow-up Call</option>
                                    <option value="Follow-up Email/Info Requested">Follow-up Email/Info Requested</option>
                                    <option value="Meeting Set-up">Meeting Set-up</option>
                                    <option value="Not Interested">Not Interested</option>
                                    <option value="Not Right Party">Not Right Party</option>
                                    <option value="Reference Shared">Reference Shared</option>

                                </select>

                                <div id="reminderdatetime">

                                    <label class="control-label">Reminder Date</label>
                                    <input type="date" class="form-control" name="reminder_date" id="min-date">

                                    <label class="control-label">Reminder Time</label>
                                    <input type="time" class="form-control" id="reminder_time" name="reminder_time">

                                </div>


                                <div id="callbackdatetime" style="display:none;">

                                    <label class="control-label">Callback Date</label>
                                    <input type="date" class="form-control" name="callback_date" id="callback_date">

                                    <label class="control-label">Callback Time</label>
                                    <input type="time" class="form-control" id="callback_time" name="callback_time">

                                </div>


                                <div class="alert alert-danger print-error-msg-1" style="display:none">
                                    <ul class="custom_text-1"></ul>
                                </div>


                                @if(Auth::user()->is_admin == 1)

                                    <label class="control-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone_number" id="phone_number"
                                        pattern="[0-9]{10}">

                                @else

                                    <input type="hidden" name="phone_number" id="phone_number">

                                @endif


                                <label class="control-label">Note</label>

                                <textarea required class="form-control required" name="feedback" id="feedback"
                                    placeholder="Enter Note" style="min-height:130px;"></textarea>


                                <div class="alert alert-danger print-error-msg" style="display:none">
                                    <ul class="custom_text"></ul>
                                </div>

                            </div>
                        </div>

                    </div>


                    <div class="modal-footer">

                        <input type="hidden" id="lead_id_quick_note" name="lead_id_quick_note">

                        <button type="button" class="btn btn-default modal-close" data-dismiss="modal">
                            Close
                        </button>

                        <button id="save-data-quick-note" type="button" class="btn btn-info">
                            Add Note
                        </button>

                    </div>

                </div>
            </div>
        </div>
    </form>



    <!-- View Notes Modal -->
    <div class="modal fade" id="largeModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">View Note</h4>

                    <button type="button" class="close largemodal-close" data-dismiss="modal">
                        &times;
                    </button>
                </div>


                <div class="modal-body">

                    <div class="form-body">
                        <input type="hidden" name="view_lead_id">
                    </div>

                    <div class="table-responsive m-t-40" id="notes_data"></div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn-success largemodal-close" data-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="numberModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #192e62; color: #fff; padding: 10px 15px;">
                    <h6 class="modal-title">Contact Numbers</h6>
                    <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 1;"
                        onclick="closemodal();">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="numberRow"
                        style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; align-items: center;">
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
    @push('scripts')
        <script src="{{url('vendor/moment/moment.js')}}"></script>

        <script src="{{url('vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}">
        </script>
        <script>
            $(document).ready(function () {
                $('#spinner-overlay').show(); // Show full-page spinner

                var table = $('#employee-table').DataTable({
                    processing: false,
                    serverSide: true,
                    searching: true,
                    ajax: "{{ url('leads/totalLeads') }}",
                    columns: [
                        { data: 'source_name', name: 'sources.source_name', orderable: true, searchable: true },
                        { data: 'description', name: 'sources.description', orderable: true, searchable: true },
                        { data: 'company_name', name: 'company_name', orderable: false },
                        { data: 'prospect_first_name', name: 'prospect_first_name', orderable: false },
                        { data: 'timezone', name: 'timezone', orderable: false, searchable: true },
                        { data: 'designation', name: 'designation', orderable: false },
                        { data: 'contact_number_1', name: 'contact_number_1', orderable: false },
                        { data: 'updated_at', name: 'updated_at', orderable: true },
                        { data: 'last_updated_note', name: 'last_updated_note', orderable: false, searchable: false },

                        { data: 'options', name: 'options', orderable: false }
                    ],
                    drawCallback: function () {
                        // Initialize Switchery for each checkbox
                        $('.switchery').each(function () {
                            if (!$(this).data('switchery')) {
                                new Switchery(this, {
                                    color: '#192e62',
                                    secondaryColor: '#f9f9f9',
                                    jackColor: '#d3da44',
                                    size: 'small'
                                });
                            }
                        });
                        tippy('.downloadmom', {
                            content: 'Download Mom',
                            placement: 'top',
                            arrow: true,
                            animation: 'scale'
                        });
                        tippy('.editlhs', {
                            content: 'Edit LHS',
                            placement: 'top',
                            arrow: true,
                            animation: 'scale'
                        });
                        tippy('.viewnotes', {
                            content: 'View Notes',
                            placement: 'top',
                            arrow: true,
                            animation: 'scale'
                        });
                        tippy('.addnotes', {
                            content: 'Add Notes',
                            placement: 'top',
                            arrow: true,
                            animation: 'scale'
                        });
                        tippy('.createmom', {
                            content: 'Create Mom',
                            placement: 'top',
                            arrow: true,
                            animation: 'scale'
                        });
                    }
                });

                // Show spinner overlay on processing start
                table.on('preXhr.dt', function (e, settings, data) {
                    $('#spinner-overlay').show(); // Show full-page spinner
                });

                // Hide spinner overlay when data is loaded
                table.on('xhr.dt', function (e, settings, json, xhr) {
                    $('#spinner-overlay').hide(); // Hide full-page spinner
                });
                table.on('init.dt', function () {
                    $('div.dataTables_filter input')
                        .attr('placeholder', 'Search by campaign,subcampaign,company')
                        .css({ 'width': '250px', 'display': 'inline-block' });
                });
            });
        </script>

        <script>
            /* ============================ */
            $("#save-data-quick-note").click(function (event) {

                $("#save-data-quick-note").attr('disabled', true);
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

                    let callback_date = $("[name=callback_date]").val();

                    let callback_time = $("[name=callback_time]").val();

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
                            callback_date: callback_date,
                            callback_time: callback_time,
                            phone_number: $('#phone_number').val(),
                            _token: _token
                        },
                        success: function (response) {
                            $("#save-data-quick-note").attr('disabled', false);

                            if ($.isEmptyObject(response.error)) {
                                console.log(response);
                                toastr.success(response.success, 'Success!');
                                if (selectedVal != 'NoResponse') {
                                    $('#feedback').val('');

                                } else {
                                    $('#feedback').val('');
                                    $('#feedback').val('VM/No Response');
                                }
                                $('#phone_number').val('');
                                $('#min-date').val("");
                                $('#reminder_for').val("");
                                $('#reminder_time').val("");
                                $('#callback_date').val("");
                                $('#callback_time').val("");

                                $('.alert.alert-danger.print-error-msg-1').hide();
                                $('.alert.alert-danger.print-error-msg').hide();
                                $("#NoResponse").trigger("click");
                                $("#modelclose").trigger("click");
                            } else {
                                toastr.error(response.error, 'Error!');
                            }
                        },
                        error: function (xhr) {
                            $("#save-data-quick-note").attr('disabled', false);

                            // ERROR from backend (15-second restriction)
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


            function checktype() {
                var remindfor = $('#reminder_for').val();
                if (remindfor == 'Callback') {
                    $('#reminderdatetime').css('display', 'none');
                    $('#callbackdatetime').css('display', 'block');
                } else {
                    $('#reminderdatetime').css('display', 'block');
                    $('#callbackdatetime').css('display', 'none');

                }
            }

        </script>

        <script>
            function showAllNumbers(numbers) {
                if (!numbers) return;
                let numList = [];
                if (numbers.indexOf('/-') !== -1) {
                    numList = numbers.split('/-');
                } else {
                    numList = numbers.split(/[,\s;]+/);
                }
                numList = numList.map(n => n.trim()).filter(n => n.length > 0);
                
                let rowHtml = '<table class="table table-bordered table-striped text-center" style="margin-top: 10px; width: 100%;">';
                rowHtml += '<thead>';
                rowHtml += '  <tr>';
                rowHtml += '    <th style="text-align: center; width: 80px;">Dial</th>';
                rowHtml += '    <th style="text-align: center;">Phone Number</th>';
                rowHtml += '  </tr>';
                rowHtml += '</thead>';
                rowHtml += '<tbody>';
                
                numList.forEach(function(num) {
                    let dialNum = num.replace(/[^0-9+]/g, '');
                    rowHtml += '  <tr>';
                    rowHtml += '    <td>';
                    rowHtml += '      <a href="tel:' + dialNum + '" class="btn btn-xs btn-success" style="border-radius: 50%; padding: 5px 8px; background-color: #28a745; border-color: #28a745;">';
                    rowHtml += '        <i class="fa fa-phone" style="color: white;"></i>';
                    rowHtml += '      </a>';
                    rowHtml += '    </td>';
                    rowHtml += '    <td style="font-size: 15px; font-weight: 500; vertical-align: middle; text-align: left; padding-left: 15px;">' + num + '</td>';
                    rowHtml += '  </tr>';
                });
                rowHtml += '</tbody>';
                rowHtml += '</table>';

                $('#numberRow').css('display', 'block').html(rowHtml);
                $('#numberModal').modal('show');
            }
            function closemodal() {
                $('#numberModal').modal('hide');

            }

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