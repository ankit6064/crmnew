@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush


@section('content')
    <style>
        .label-new {
            background: #ffc107;
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

    <input type="hidden" id="source_id" value="{{ $id }}">

    <div class="main-right">
        <div class="right-side submanager completed-leads closed-leads">

            <h2>Leads Listing - {{ $source->source_name }}</h2>

            <div class="graph campaignslist">

                <!-- Filters -->
                <!-- <div class="row">
                                        <div class="add-submanager">
                                            <input type="search" id="global_filter" name="search" placeholder="search...">
                                        </div>

                                    </div> -->
                <div class="filter-row" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">

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

                    <button id="reset_filters" class="btn btn-secondary">
                        <i class="fa fa-rotate-left"></i> Reset
                    </button>

                </div>



                <!-- Datatable -->
                <div class="table">
                    <div class="table-container">
                        <table id="employee-table">
                            <thead class="thead-main">
                                <tr>
                                    <th>Company Name</th>
                                    <th>Prospect Name</th>
                                    <th>Time Zone </th>
                                    <th>Designation</th>
                                    <th>Phone No.</th>
                                    <th>Phone No. 2</th>
                                    <th>Last Updated Note</th>
                                    <th>Action</th>

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




@endsection




@push('scripts')

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#spinner-overlay').show(); // Show full-page spinner

            var sourceId = $('#source_id').val();
            var url = "{{ url('campaign/camp_assign_emp') }}/" + sourceId;

            var table = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                searching: true, // Enable search globally
                ajax: {
                    url: url,
                    data: function (d) {
                        d.timeZone = $('#company_time :selected').val();
                        d.cName = $('#company_s :selected').val();
                        d.orderBy = $('#note_time :selected').val();

                    },
                    error: function (xhr, error, thrown) {
                        console.error("AJAX Error:", xhr.responseText);
                    }
                },
                columns: [
                    { data: 'company_name', name: 'company_name', orderable: true, searchable: true },
                    { data: 'prospect_first_name', name: 'prospect_first_name' },
                    { data: 'timezone', name: 'timezone', orderable: false },
                    { data: 'designation', name: 'designation', orderable: false },
                    { data: 'contact_number_1', name: 'contact_number_1', orderable: false },
                    { data: 'contact_number_2', name: 'contact_number_2', orderable: false },
                    { data: 'update_note_date', name: 'update_note_date' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },

                ],
                drawCallback: function () {

                    tippy('.viewnotes', {
                        content: 'View Notes',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.addnotes', {
                        content: 'Add Note',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.changestatus', {
                        content: 'Change Status',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.editLeads', {
                        content: 'Edit Leads',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.deleteLeads', {
                        content: 'Delete Leads',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                },
                initComplete: function () {
                    $('div.dataTables_filter input')
                        .attr('placeholder', 'Search by company, prospect, designation')
                        .css({ 'width': '250px' });

                    $('#spinner-overlay').hide();
                },
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
            $('#company_time, #company_s, #note_time').on('change', function () {
                console.log('Filter changed! Reloading table...');
                table.ajax.reload(); // Reload DataTable with new filter values
            });

            $('#global_filter').on('keyup', function () {

                table.ajax.reload(); // Reload DataTable with new filter values
            });
        });

    </script>

    <script>



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

        $('#reset_filters').on('click', function () {

            // Reset dropdowns
            $('#company_s').val('');
            $('#company_time').val('');
            $('#note_time').val('');

            // Reset search
            $('#global_filter').val('');

            location.reload(true);
        });

        function showAllNumbers(numbers) {
            let rowHtml = '';

            rowHtml += '<p>' + numbers + '</p>';


            $('#numberRow').html(rowHtml);
            $('#numberModal').modal('show');
        }



        function closemodal() {
            $('#numberModal').modal('hide');

        }

        $('.modal-close').on('click', function (event) {
            $('#status-modal-quicknote').modal('hide');
        });

        $('.largemodal-close').on('click', function (event) {
            $('#largeModal').modal('hide');
        });

    </script>

@endpush