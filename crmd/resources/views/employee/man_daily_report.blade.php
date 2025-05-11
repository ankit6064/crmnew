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

@php
    $urls = '?employee_id=' . request()->get('employee_id') . '&campaign_id=' . request()->get('campaign_id') . '&date_from=' . request()->get('date_from') . '&date_to=' . request()->get('date_to') . '&filter_by=' . request()->get('filter_by') . '&reminder_for_conversation=' . request()->get('reminder_for_conversation');
@endphp

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

                <div class="card-body">
                    <div class="row p-t-20">
                        @if(Auth::user()->is_admin !=1)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Employee Name</label>
                                <select class="form-control" name="employee_id" id="employee_id">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employees)
                                        @if ($employee_id == $employees['id'])
                                            <option value="{{ $employees['id'] }}" selected>{{ $employees['name'] }}</option>
                                        @else
                                            <option value="{{ $employees['id'] }}">{{ $employees['name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Campaign Name</label>
                                <?php echo $campaignsHtml; ?>
                            </div>
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

                        <div class="col-md-3" style="display: none;" id='reminder_for_conversation_div'>
                            <div class="form-group">
                                <label class="control-label">Conversation Type</label>
                                <select id="reminder_for_conversation" class="form-control required"
                                    name="reminder_for_conversation">
                                    <option value="">Choose Conversation Type</option>
                                    @foreach($conversationTypes as $types)
                                        <option value="{{ $types['type'] }}">{{ $types['type'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


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
                        <a type="button" href="{{ url('/employee/man_daily_report' . $urls) }}"
                            class="btn btn-success addButton"> Export Report </a>

                    </div>
                    <!-- sample modal content -->
                    <div id="status-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                        aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                    <meta name="csrf-token" content="{{ csrf_token() }}" />

                                    <div>
                                        <ul></ul>
                                    </div>

                                    <div class="modal-header">
                                        <h4 class="modal-title">Change Status</h4>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-hidden="true">×</button>
                                    </div>
                                    <div class="alert alert-danger print-error-msg" style="display:none">
                                        <ul></ul>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label">Select Status: </label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="">Select Status</option>
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
                                        <button type="button" class="btn btn-default waves-effect"
                                            data-dismiss="modal">Close</button>
                                        <button id="save-data" type="button"
                                            class="btn btn-info waves-effect waves-light ">Save changes</button>
                                    </div>
                            </div>
                        </div>
                    </div>


                    <!--<h4 class="card-title">Data Export</h4>
                                <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>-->

                    <div class="table-responsive m-t-40" id="table_data">

                        <table id="leadsTable" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Lead Name</th>
                                    <th>Conversation Type</th>
                                    <th>Note</th>
                                    <th>Note Date & Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table> <?php
//$cookie_name = "last_url";
// $cookie_value =  route('employe.view_camp', [$data['source_id']]) ;
// setcookie($cookie_name, $cookie_value,time()+3600,'/');
                                           
                                            ?>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script type="text/javascript">
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const camp_id = urlParams.get('camp_id');
    const emp_id = urlParams.get('emp_id');
    const date_from = urlParams.get('date_from');
    const date_to = urlParams.get('date_to');
    const filter_by = urlParams.get('filter_by');
    const reminder_for_conversation = urlParams.get('reminder_for_conversation');

    $(document).ready(function () {
    $('#spinner-overlay').show(); // Show full-page spinner

    $('#leadsTable').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: "{{ route('getLeadsData') }}",
            data: function (d) {
                d.employee_id = $('#employee_id').val();
                d.campaign_id = $('#campaign_id').val();
                d.date_from = $('#date_from_new').val();
                d.date_to = $('#date_to_new').val();
                d.change_status = $('#change_status').val();
            }
        },
        
        columns: [
            { data: 'lead_name', name: 'lead_name' },
            { data: 'conversation_type', name: 'conversation_type' },
            { data: 'note', name: 'note' },
            { data: 'note_date_time', name: 'note_date_time' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
        ],
        initComplete: function () {
            $('#spinner-overlay').hide(); // Hide spinner after table is fully initialized
        },
        drawCallback: function () {
            $('#spinner-overlay').hide(); // Ensure spinner is hidden after each redraw
        }
    });


    });


    $(document).on("click", "#sub_cmap", function () {
        $('#leadsTable').DataTable().ajax.reload();

    });
</script>
<script>
    $(document).ready(function () {

        if (filter_by != '') {
            $('#filter_by').val(filter_by);
            $('#filter_by').trigger('change');

            if (filter_by == 2) {
                $("#reminder_for_conversation_div").show();
                $('#reminder_for_conversation').val(reminder_for_conversation);
                $('#reminder_for_conversation').trigger('change');
            } else {
                $("#reminder_for_conversation_div").hide();
            }
        }


        $("#filter_by").change(function () {
            var selectedVal = $(this).val();
            if (selectedVal == 2) {
                $("#reminder_for_conversation_div").show();
            } else {
                $("#reminder_for_conversation_div").hide();
            }
        });


        $("#status").change(function () {
            var selected_val = $('option:selected', this).val();
            lead_id = $("input[name=lead_id]").val();
            if (selected_val == 3) {
                $("#save-data").attr("disabled", true);
                var create_notes_count_id = "notes_count_" + lead_id;
                var total_notes_count = $("#" + create_notes_count_id).val();
                if (total_notes_count > 0) {
                    var base_url = $('meta[name="base_url"]').attr('content');
                    var Current_url = base_url + "/employee/lhs_report/" + lead_id + "?status=" + selected_val;
                    window.location.href = Current_url;
                } else {
                    $('.alert.alert-danger.print-error-msg').show();
                    $('ul.custom_text').html('<li class="error_list"><span class="tab">Please add a notes first.</span></li>');
                    alert('jjdjdjj');

                }

            } else {
                $("#save-data").attr("disabled", false);
            }
        });


        $(".set_camp_id").click(function () {
            var get_url = $(this).attr('href');
            console.log(get_url);
            $.cookie("get_last_url", get_url, { path: '/' });
        });


        $("#employee_id").change(function () {
            var selected_val1 = $('option:selected', this).val();
            var parentUrl = window.location.href;
            var querys;

            if (typeof getUrlVars()['campaign_id'] !== "undefined") {
                querys = getUrlVars()['campaign_id'];
            } else {
                querys = "";
            }


            let _token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{route("allocatedCompaign")}}',
                type: "POST",
                data: {
                    employee_id: selected_val1,
                    querys_: querys,
                    _token: _token
                },
                success: function (response) {

                    $('#campaign_id').html(response);

                },
            });
        });

        function getUrlVars() {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }

    });

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
</script>



@endsection