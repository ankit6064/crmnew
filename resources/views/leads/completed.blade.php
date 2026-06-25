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
        .filter-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            align-items: end;
        }

        @media (max-width: 1200px) {
            .filter-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-weight: 600;
            font-size: 13px;
            color: #475569;
            margin-bottom: 0px;
            font-family: 'Poppins', sans-serif;
        }

        .filter-select,
        .filter-input {
            width: 100% !important;
            min-width: unset !important;
            height: 42px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 0 14px;
            font-size: 13px;
            color: #1e293b;
            background-color: #f8fafc;
            box-sizing: border-box;
            transition: all 0.2s ease;
            font-family: 'Poppins', sans-serif;
        }

        .filter-select:focus,
        .filter-input:focus {
            border-color: #192e62;
            background-color: #ffffff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(25, 46, 98, 0.15);
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            align-items: center;
            height: 42px;
        }

        .btn-reset {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .btn-reset:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
    </style>
    <?php date_default_timezone_set('Asia/Kolkata'); ?>

    <div class="main-right">
        <div class="right-side submanager">
            <div class="row">
                <div class="row align-items-center mb-3">
                    <div class="col-md-8">
                        <h2 class="mb-0">Completed Leads Listing</h2>
                    </div>

                    <div class="col-md-4 text-end">
                        <button type="button" class="btn return-btn"
                            onclick="window.history.back() || (window.location.href='{{ route('employeedashboard') }}');">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </button>
                    </div>
                </div>

            </div>
            <div class="graph campaignslist logstable lead-listing completed-leads">
                <div class="filter-card">
                    <div class="filter-grid">
                        <!-- Campaign Filter -->
                        <div class="filter-group">
                            <label for="campaign_name">Campaign</label>
                            <select id="campaign_name" class="filter-select">
                                <option value="">Select Campaign</option>
                                @foreach($campaignNames as $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Company Filter -->
                        <div class="filter-group">
                            <label for="company_s">Company</label>
                            <select id="company_s" class="filter-select">
                                <option value="">Select Company</option>
                                @foreach($companyNames as $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Timezone Filter -->
                        <div class="filter-group">
                            <label for="company_time">Time Zone</label>
                            <select id="company_time" class="filter-select">
                                <option value="">Select Time Zone</option>
                                @foreach($timeZones as $tz)
                                    <option value="{{ $tz }}">{{ $tz }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Actions Group -->
                        <div class="filter-actions">
                            <button id="reset_filters" class="btn btn-reset">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

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

                                <div id="conversation_type_container" style="display:none;">
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
                                </div>

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
                    order: [],
                    ajax: {
                        url: "{{ url('leads/completed') }}",
                        data: function (d) {
                            d.campaign_name = $('#campaign_name').val();
                            d.cName = $('#company_s').val();
                            d.timeZone = $('#company_time').val();
                        }
                    },
                    columns: [
                        { data: 'source_name', name: 'sources.source_name', orderable: true, searchable: true },
                        { data: 'description', name: 'sources.description', orderable: true, searchable: true },
                        { data: 'company_name', name: 'leads.company_name', orderable: true },
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
                        tippy('.shownotes', {
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

                $('#campaign_name, #company_s, #company_time').on('change', function () {
                    table.draw();
                });

                $('#reset_filters').on('click', function () {
                    $('#campaign_name').val('');
                    $('#company_s').val('');
                    $('#company_time').val('');
                    table.draw();
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
                        $('#phone_number').val('');
                        $('#callback_date').val('');
                        $('#callback_time').val('');
                        $('.alert.alert-danger.print-error-msg-1').hide();
                        $('.alert.alert-danger.print-error-msg').hide();
                        $('#conversation_type_container').hide();
                        $('#reminderdatetime').show();
                        $('#callbackdatetime').hide();
                    } else if (this.value == 'Conversation') {
                        $('.alert.alert-danger.print-error-msg-1').hide();
                        $('.alert.alert-danger.print-error-msg').hide();
                        $('#feedback').val('');
                        $('#min-date').val("");
                        $('#reminder_for').val("");
                        $('#reminder_time').val("");
                        $('#phone_number').val('');
                        $('#callback_date').val('');
                        $('#callback_time').val('');
                        $('#conversation_type_container').show();
                        checktype();
                    }
                });
            });
        </script>

        <script>
            function showaddmodal(id) {
                $('#lead_id_quick_note').val(id);
                $('input[name="conversation_type"][value="NoResponse"]').prop('checked', true);
                $('#min-date').val('');
                $('#reminder_time').val('');
                $('#reminder_for').val('');
                $('#feedback').val('VM/No Response');
                $('#phone_number').val('');
                $('#callback_date').val('');
                $('#callback_time').val('');
                $('#conversation_type_container').hide();
                $('#reminderdatetime').show();
                $('#callbackdatetime').hide();
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

                numList.forEach(function (num) {
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
        </script>
    @endpush

@endsection