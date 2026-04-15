@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush


@section('content')

    <input type="hidden" id="source_id" value="{{ $id }}">

    <div class="main-right">
        <div class="right-side submanager completed-leads closed-leads">

            <h2>Closed Leads</h2>

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



                <!-- Datatable -->
                <div class="table">
                    <div class="table-container">
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
                                    <th>Confirmation Status</th>
                                    <th>Reminder Status</th>
                                    <th>Invitation Date</th>
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
                        <input type="radio" name="conversation_type" value="NoResponse" checked> VM / No Response
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
                            <option value="Follow-up Email/Info Requested">Follow-up Email/Info Requested</option>
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
                    <button class="close largemodal-close" data-dismiss="modal" onclick="closemodal()">×</button>
                </div>

                <div class="modal-body">
                    <div id="notes_data" class="table-responsive"></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success largemodal-close" data-dismiss="modal"
                        onclick="closemodal()">Close</button>
                </div>

            </div>
        </div>
    </div>



    <!-- =================================================================== -->
    <!--                        QUICK NOTE MODAL                             -->
    <!-- =================================================================== -->
    <div id="reminder-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <meta name="csrf-token" content="{{ csrf_token() }}" />
                <input type="hidden" name="lead_id" id="reminder_lead_id">

                <div class="modal-header">
                    <h4 class="modal-title">Reminder Status</h4>
                    <button type="button" class="close modal-close" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">

                    <!-- Reminder Fields -->
                    <div class="form-group">
                        <label>Note(optional)</label>
                        <textarea id="reminder_note" class="form-control" style="min-height:130px;"></textarea>

                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" id="lead_id_quick_note">
                    <button class="btn btn-default modal-close" data-dismiss="modal">Close</button>
                    <button class="btn btn-info" id="update-reminder-status">Send Reminder</button>
                </div>

            </div>
        </div>
    </div>


@endsection




@push('scripts')

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>

        $(document).ready(function () {

            $('#spinner-overlay').show(); // show on first load

            var url = "{{ url('leads/employeeclosedleads') }}/";

            var table = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                searching: true,
                ordering: false,
                searchDelay: 500, // smoother search
                ajax: {
                    url: url,
                    data: function (d) {
                        d.campaign_name = $('#campaign_name').val();
                        d.cName = $('#company_s').val();
                        d.timeZone = $('#company_time').val();
                        d.closedon = $('#closedon').val();
                    }
                },
                columns: [
                    { data: 'source_name', name: 'sources.source_name', searchable: false },
                    { data: 'description', name: 'sources.description' }, // FIX HERE
                    { data: 'company_name' },
                    { data: 'closed_by' },
                    { data: 'prospect_first_name_new', name: 'prospect_first_name' }, // FIX HERE
                    { data: 'timezone' },
                    { data: 'designation' },
                    { data: 'status' },
                    { data: 'prospect_email' },
                    { data: 'contact_number_1' },
                    { data: 'updated_at_new' },
                    { data: 'confirmation_status' },
                    { data: 'reminder_status' },
                    { data: 'invitation_date' },

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

            // Filters trigger reload + loader
            $('#campaign_name, #company_s, #company_time, #closedon')
                .on('keyup change', function () {
                    $('#spinner-overlay').show();
                    table.ajax.reload();
                });

        });


        // ======================================================
        //  OPEN QUICK NOTE MODAL
        // ======================================================
        function showaddmodal(id) {
            $('#lead_id_quick_note').val(id);
            $('#status-modal-quicknote').modal('show');
        }


        // ======================================================
        //  SAVE QUICK NOTE
        // ======================================================
        $('#save-data-quick-note').click(function () {

            let lead_id = $('#lead_id_quick_note').val();
            let reminder_date = $('#min-date').val();
            let reminder_time = $('#reminder_time').val();
            let reminder_for = $('#reminder_for').val();
            let feedback = $('#feedback').val();
            let type = $('input[name=conversation_type]:checked').val();

            let _token = $('meta[name="csrf-token"]').attr('content');

            $.post("{{ url('leads/add_note') }}", {
                lead_id, reminder_date, reminder_time, reminder_for, feedback, type, _token
            }, function (res) {

                if (res.success) {
                    toastr.success(res.success);
                    $('#status-modal-quicknote').modal('hide');
                } else {
                    toastr.error("Something went wrong");
                }

            });

        });


        // ======================================================
        //  OPEN STATUS CHANGE MODAL
        // ======================================================
        function showstatusmodal(id) {
            $('#lead_id').val(id);
            $('#status-modal').modal('show');
        }


        // ======================================================
        //  SAVE STATUS
        // ======================================================
        $('#save-data').click(function () {

            let lead_id = $('#lead_id').val();
            let status = $('#status').val();
            let _token = $('meta[name="csrf-token"]').attr('content');

            $.post("{{ url('changeStatus') }}", { lead_id, status, _token }, function (res) {

                if (res.success) {
                    toastr.success(res.success);
                    $('#status-modal').modal('hide');
                    location.reload();
                } else {
                    toastr.error(res.error);
                }

            });

        });


        // ======================================================
        //  VIEW NOTES
        // ======================================================
        function shownoteslist(id) {

            $.get("{{ url('leads/notes_view') }}/" + id, function (res) {

                $('#notes_data').html(res.table);
                $('#largeModal').modal('show');

            });

        }

        function closemodal() {
            $('#largeModal').modal('hide');

        }

        function deleteLead(id) {
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

        function showAllNumbers(numbers) {
            let rowHtml = '';

            rowHtml += '<p>' + numbers + '</p>';


            $('#numberRow').html(rowHtml);
            $('#numberModal').modal('show');
        }



        function closemodal() {
            $('#numberModal').modal('hide');

        }


        $(document).on('click', '.send-reminder', function () {
            let id = $(this).data('id');
            $('#reminder_lead_id').val(id);
            $('#reminder-modal').modal('show');
        });


        // ======================================================
        //  UPDATE REMINDER STATUS
        // ======================================================
        $('#update-reminder-status').click(function () {

            let lead_id = $('#reminder_lead_id').val();
            let reminder_note = $('#reminder_note').val();
            let _token = $('meta[name="csrf-token"]').attr('content');

            $.post("{{ url('leads/update_reminder_status') }}", {
                lead_id, reminder_note, _token
            }, function (res) {

                if (res.success) {
                    toastr.success(res.success);
                    $('#reminder-modal').modal('hide');
                    location.reload(true);
                } else {
                    toastr.error("Something went wrong");
                }

            });

        });

    </script>

@endpush