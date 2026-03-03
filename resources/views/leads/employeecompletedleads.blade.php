@extends('layouts.admin')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@section('content')

    <input type="hidden" id="source_id" value="{{ $id }}">

    <div class="main-right">
        <div class="right-side submanager completed-leads closed-leads">

            <h2>Completed Leads</h2>

            <div class="graph campaignslist">

                <!-- Filters -->
                <!-- <div class="row">
                    <div class="add-submanager">
                        <input type="search" id="global_filter" name="search" placeholder="search...">
                    </div>

                </div> -->
                <div class="filter-row" style="
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    ">

                    <!-- Campaign Filter -->
                    <select id="campaign_name" class="filter-select">
                        <option value="">Campaign</option>
                        @foreach($sourceNames as $s)
                            <option value="{{ $s['source_name'] }}">{{ $s['source_name'] }}</option>
                        @endforeach
                    </select>

                    <!-- Company Filter -->
                    <select id="company_s" class="filter-select">
                        <option value="">Company</option>
                        @foreach($comapnyName as $c)
                            <option value="{{ $c['company_name'] }}">{{ $c['company_name'] }}</option>
                        @endforeach
                    </select>

                    <!-- Timezone -->
                    <select id="company_time" class="filter-select">
                        <option value="">Time Zone</option>
                        @foreach($timeZone as $t)
                            <option value="{{ $t['timezone'] }}">{{ $t['timezone'] }}</option>
                        @endforeach
                    </select>

                    <!-- Closed On -->
                    <select id="closedon" class="filter-select">
                        <option value="">Closed On</option>
                        @foreach($closedon as $d)
                            <option value="{{ $d['date'] }}">{{ date('d/m/Y', strtotime($d['date'])) }}</option>
                        @endforeach
                    </select>



                </div>



                <!-- =================================================================== -->
                <!--                        QUICK NOTE MODAL                             -->
                <!-- =================================================================== -->
                <div id="status-modal-quicknote" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <meta name="csrf-token" content="{{ csrf_token() }}" />

                            <div class="modal-header">
                                <h4 class="modal-title">Add Quick Note</h4>
                                <button type="button" class="close modal-close" data-dismiss="modal">×</button>
                            </div>

                            <div class="modal-body">

                                <div class="form-group">
                                    <input type="radio" name="conversation_type" value="NoResponse" checked> VM / No
                                    Response
                                    &nbsp;&nbsp;
                                    <input type="radio" name="conversation_type" value="Conversation"> Conversation
                                </div>

                                <!-- Reminder Fields -->
                                <div class="form-group">
                                    <label>Reminder Date</label>
                                    <input type="date" id="min-date" class="form-control">

                                    <label>Reminder Time</label>
                                    <input type="time" id="reminder_time" class="form-control">

                                    <label>Conversation Type</label>
                                    <select id="reminder_for" class="form-control">
                                        <option value="">Choose Option</option>
                                        <option value="Declined">Declined</option>
                                        <option value="DNC">DNC</option>
                                        <option value="Follow-up Call">Follow-up Call</option>
                                        <option value="Follow-up Email/Info Requested">Follow-up Email/Info Requested
                                        </option>
                                        <option value="Meeting Set-up">Meeting Set-up</option>
                                        <option value="Not Interested">Not Interested</option>
                                        <option value="Not Right Party">Not Right Party</option>
                                        <option value="Reference Shared">Reference Shared</option>
                                    </select>

                                    <label>Note</label>
                                    <textarea id="feedback" class="form-control" style="min-height:130px;"></textarea>

                                    <div class="alert alert-danger print-error-msg" style="display:none">
                                        <ul></ul>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <input type="hidden" id="lead_id_quick_note">
                                <button class="btn btn-default modal-close" data-dismiss="modal">Close</button>
                                <button class="btn btn-info" id="save-data-quick-note">Add Note</button>
                            </div>

                        </div>
                    </div>
                </div>




                <!-- =================================================================== -->
                <!--                        STATUS CHANGE MODAL                           -->
                <!-- =================================================================== -->
                <div id="status-modal" class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <form id="ajaxform">

                                <meta name="csrf-token" content="{{ csrf_token() }}" />

                                <div class="modal-header">
                                    <h4 class="modal-title">Change Status</h4>
                                    <button type="button" class="close close-status-modal" data-dismiss="modal">×</button>
                                </div>

                                <div class="modal-body">

                                    <label>Select Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Select Status</option>
                                        <option value="4">In progress</option>
                                        <option value="3">Closed</option>
                                        <option value="2">Failed</option>
                                    </select>

                                    <div class="alert alert-danger print-error-msg" style="display:none">
                                        <ul></ul>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <input type="hidden" id="lead_id">
                                    <button class="btn btn-default close-status-modal" data-dismiss="modal">Close</button>
                                    <button id="save-data" type="button" class="btn btn-info">Save changes</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>




                <!-- =================================================================== -->
                <!--                       VIEW NOTES MODAL                              -->
                <!-- =================================================================== -->
                <div class="modal fade" id="largeModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title">View Notes</h4>
                                <button class="close largemodal-close" data-dismiss="modal">×</button>
                            </div>

                            <div class="modal-body">
                                <div id="notes_data" class="table-responsive"></div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-success largemodal-close" data-dismiss="modal">Close</button>
                            </div>

                        </div>
                    </div>
                </div>


                <!-- Datatable -->
                <div class="table">
                    <div class="table-container">
                        <div class="table-container-inner">

                            <table id="employee-table">
                                <thead class="thead-main">
                                    <tr>
                                        <th>Campaign Name</th>
                                        <th>Sub Campaign Name</th>
                                        <th>Company Name</th>
                                        <th>Closed By</th>
                                        <th>Prospect Name</th>
                                        <th>Time Zone</th>
                                        <th>Designation</th>
                                        <th>Status</th>
                                        <th>Email Id</th>
                                        <th>Phone Number</th>
                                        <th>Closed On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
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

                var sourceId = $('#source_id').val();
                var url = "{{ url(path: 'leads/employeecompletedleads') }}/";

                var table = $('#employee-table').DataTable({
                    processing: false,
                serverSide: true,
                searching: true,
                ordering: false,
                searchDelay: 500, // smoother search
                //     ajax: {
                //         url: url,
                //         data: function (d) {
                //             d.timeZone = $('#company_time :selected').val();
                //             d.cName = $('#company_s :selected').val();
                //             d.orderBy = $('#note_time :selected').val();
                //             d.search = $('#global_filter').val();
                //             d.campaign_name = $('#campaign_name').val();
                //             d.status = $('#status').val();
                //             d.closedon = $('#closedon').val();

                //         },
                //         error: function (xhr, error, thrown) {
                //             console.error("AJAX Error:", xhr.responseText);
                //         }
                //     },
                //     columns: [
                //         { data: 'source_name' },               // Campaign Name
                //         { data: 'description' },               // Sub Campaign Name
                //         { data: 'company_name' },              // Company Name
                //         { data: 'completed_by' },                 // Closed By
                //         { data: 'prospect_first_name_new' },   // Prospect Name
                //         { data: 'timezone' },                  // Time Zone
                //         { data: 'designation' },               // Designation
                //         { data: 'status' },                    // Status
                //         { data: 'prospect_email' },            // Email Id
                //         { data: 'contact_number_1' },          // Phone Number
                //         { data: 'updated_at_new' },            // Closed On
                //         { data: 'action', orderable: false, searchable: false }, // Actions
                //     ],
                //     rawColumns: ['action'], // Ensure HTML is rendered in the actions column
                //     drawCallback: function () {

                //         // 🔥 DESTROY OLD TIPPY (prevents duplicate)
                //         document.querySelectorAll('[data-tippy-content]').forEach(el => {
                //             if (el._tippy) {
                //                 el._tippy.destroy();
                //             }
                //         });

                //         // 🔥 INIT TIPPY AGAIN
                //         tippy('[data-tippy-content]', {
                //             theme: 'light-border',
                //             placement: 'top',
                //             arrow: true,
                //             animation: 'scale'
                //         });
                //     },
                // });

                // // Show spinner overlay on processing start
                // table.on('preXhr.dt', function (e, settings, data) {
                //     $('#spinner-overlay').show(); // Show full-page spinner
                // });

                // // Hide spinner overlay when data is loaded
                // table.on('xhr.dt', function (e, settings, json, xhr) {
                //     $('#spinner-overlay').hide(); // Hide full-page spinner
                // });

                ajax: {
                    url: url,
                    data: function (d) {
                        d.timeZone = $('#company_time :selected').val();
                            d.cName = $('#company_s :selected').val();
                            d.orderBy = $('#note_time :selected').val();
                            d.campaign_name = $('#campaign_name').val();
                            d.status = $('#status').val();
                            d.closedon = $('#closedon').val();
                    }
                },
                columns: [
                    { data: 'source_name', name: 'sources.source_name', searchable: false },
                    { data: 'description', name: 'sources.description' }, // FIX HERE
                    { data: 'company_name' },
                    { data: 'completed_by' },
                    { data: 'prospect_first_name_new', name: 'prospect_first_name' }, // FIX HERE
                    { data: 'timezone' },
                    { data: 'designation' },
                    { data: 'status' },
                    { data: 'prospect_email' },
                    { data: 'contact_number_1' },
                    { data: 'updated_at_new' },
                    { data: 'action', orderable: false, searchable: false }
                ],
                initComplete: function () {
                    $('div.dataTables_filter input')
                        .attr('placeholder', 'Search by company, prospect, designation')
                        .css({ 'width': '250px' });

                    $('#spinner-overlay').hide();
                }
            });

            // 🔥 SHOW loader before every AJAX request
            table.on('preXhr.dt', function () {
                $('#spinner-overlay').show();
            });

            // 🔥 HIDE loader after AJAX response
            table.on('xhr.dt', function () {
                $('#spinner-overlay').hide();
            });

            // 🔥 EXTRA: Show loader immediately when typing
            $(document).on('keyup', 'div.dataTables_filter input', function () {
                $('#spinner-overlay').show();
            });

                // Ensure filters work by putting event listener inside $(document).ready()
                $('#company_time').on('change', function () {
                    console.log('Filter changed! Reloading table...');
                    table.ajax.reload(); // Reload DataTable with new filter values
                });

                $('#company_s').on('change', function () {
                    console.log('Filter changed! Reloading table...');
                    table.ajax.reload(); // Reload DataTable with new filter values
                });

                $('#campaign_name').on('change', function () {
                    console.log('Filter changed! Reloading table...');
                    table.ajax.reload(); // Reload DataTable with new filter values
                });

                $('#closedon').on('change', function () {
                    console.log('Filter changed! Reloading table...');
                    table.ajax.reload(); // Reload DataTable with new filter values
                });

                $('#global_filter').on('keyup', function () {

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




        function deleteLead(id){
            Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            if (result.isConfirmed) {
              // Perform the AJAX request to delete the manager
              $.ajax({
                url: `delete/${id}`, // Adjust this URL to match your route
                type: 'GET', // Use GET request for deletion
                success: function (response) {
                  // Handle successful response (e.g., show a success message)
                  Swal.fire(
                    'Deleted!',
                    'The lead has been deleted.',
                    'success'
                  );

                  // Redraw the DataTable to reflect the changes
                  $('#employee-table').DataTable()
                    .draw(); // Redraw the DataTable
                },
                error: function (xhr, status, error) {
                  // Handle error (e.g., show an error message)
                  Swal.fire(
                    'Error!',
                    'There was an issue deleting the lead.',
                    'error'
                  );
                }
              });
            }
          });      
          }

        </script>


    @endpush

@endsection