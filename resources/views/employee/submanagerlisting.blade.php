@extends('layouts.admin')
@section('content')

    <style>
        .message-box {
            padding: 15px 20px;
            margin: 15px 0;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            background-color: red;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        /* --- New Card Button Styles --- */
        .stat-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card.active-card {
            border-color: #192e62;
            background-color: #f8f9ff;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(25, 46, 98, 0.2);
        }

        /* ------------------------------ */

        table.employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-family: Arial, sans-serif;
        }

        .employee-table th,
        .employee-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .employee-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .employee-table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .employee-table tr:hover {
            background-color: #f1f1f1;
        }

        .employee-table input[type="checkbox"] {
            transform: scale(1.2);
            cursor: pointer;
        }

        .view_emp {
            cursor: pointer;
            color: blue;
        }

        .menu-title.active {
            background-color: transparent;
            font-weight: bold;
        }

        .graph tbody tr.odd td:last-child {
            display: flex;
        }
    </style>

    <div class="main-right">
        <div class="right-side">
            <h2>Submanager Listing</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card filter-card active-card" data-filter="total">
                        <div class="card-header">
                            <h4>Total</h4>
                            <div class="card-icon">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{$totalsubmanagers}}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card filter-card" data-filter="active">
                        <div class="card-header">
                            <h4>Active</h4>
                            <div class="card-icon acti">
                                <i class="fa-regular fa-user"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{$active}}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card filter-card" data-filter="inactive">
                        <div class="card-header">
                            <h4>Inactive</h4>
                            <div class="card-icon inactive">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{$inactive}}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="graph custom-submanager">
                <div class="modal fade" id="employeelisting" tabindex="-1" role="dialog" aria-labelledby="totalLeadsLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="totalLeadsLabel">Employee Listing</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="closemodal()">
                                    <span aria-hidden="true" style="color: black;">&times;</span>
                                </button>
                            </div>
                            <div id="employeelistingbody" class="modal-body"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info" data-dismiss="modal"
                                    onclick="closemodal()">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="campaignlisting" tabindex="-1" role="dialog" aria-labelledby="totalLeadsLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="totalLeadsLabel">Campaign Listing</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="closemodal()">
                                    <span aria-hidden="true" style="color: black;">&times;</span>
                                </button>
                            </div>
                            <div id="campaignlistingbody" class="modal-body"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info" data-dismiss="modal"
                                    onclick="closemodal()">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table">
                    <div class="table-container">
                        <table class="table table-striped table-hover" id="employee-table">
                            <thead class="thead-main">
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                    <th>Phone</th>
                                    <th>Assigned Emp</th>
                                    <th>Assigned Cmp</th>
                                    <th>Actions </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="popupcenter" id="successModal">
        <div class="popupp">
            <div class="success-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <p>Employee has been <br> added successfully.</p>
        </div>
    </div>
@endsection

@push('scripts')

    @if (session('swalError'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('swalError') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        </script>
    @endif

    <script>
        $(document).ready(function () {
            $('#spinner-overlay').show();

            // Variable to hold current filter status
            var currentStatus = 'total';

            var table = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                // Updated AJAX to send the filter status to the backend
                ajax: {
                    url: '{{ route('employee.submanagerlistingdata') }}',
                    data: function (d) {
                        d.status_filter = currentStatus;
                    }
                },
                pageLength: 10,
                columns: [
                    { data: 'first_name', name: 'first_name', render: data => data || "N/A" },
                    { data: 'last_name', name: 'last_name', render: data => data || "N/A" },
                    { data: 'email', name: 'email', orderable: false, render: data => data || "N/A" },
                    { data: 'orignal_password', name: 'orignal_password', orderable: false, render: data => data || "N/A" },
                    { data: 'phone_no', name: 'phone_no', orderable: false, render: data => data || "N/A" },
                    { data: 'totalemployees', name: 'totalemployees', orderable: false, searchable: false, render: data => data || "0" },
                    { data: 'totalcampaigns', name: 'totalcampaigns', orderable: false, searchable: false, render: data => data || "0" },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                drawCallback: function () {
                    $('.switchery').each(function () {
                        if (!$(this).data('switchery')) {
                            var switchery = new Switchery(this, {
                                color: '#192e62',
                                secondaryColor: '#f9f9f9',
                                jackColor: '#d3da44',
                                size: 'small'
                            });
                            let switchElement = $(this).next('.switchery')[0];
                            tippy(switchElement, { content: 'Change Status', placement: 'top' });
                        }
                    });

                    tippy('.viewEmployee', { content: 'View Employees', placement: 'top' });
                    tippy('.editEmployee', { content: 'Edit Manager', placement: 'top' });
                    tippy('.deleteEmployee', { content: 'Delete Employee', placement: 'top' });
                }
            });

            // --- Card Click Handler ---
            $('.filter-card').on('click', function () {
                // 1. UI Update
                $('.filter-card').removeClass('active-card');
                $(this).addClass('active-card');

                // 2. Update status and reload Table
                currentStatus = $(this).data('filter');
                table.draw();
            });

            // Trigger "Total" card by default on load (though it's visually marked in HTML)
            // $('.filter-card[data-filter="total"]').trigger('click');

            table.on('init.dt', function () {
                $('div.dataTables_filter input').attr('placeholder', 'Search by name,email').css({ 'width': '250px' });
            });

            table.on('preXhr.dt', () => $('#spinner-overlay').show());
            table.on('xhr.dt', () => $('#spinner-overlay').hide());

            // Delete Handler
            $(document).on('click', '.deleteEmployee', function (event) {
                event.preventDefault();
                const employeeId = $(this).parent().data('id');
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
                        $.ajax({
                            url: `/employee/${employeeId}`,
                            type: 'POST',
                            success: function () {
                                Swal.fire('Deleted!', 'The employee has been deleted.', 'success');
                                table.draw();
                            },
                            error: function () {
                                Swal.fire('Error!', 'There was an issue deleting the employee.', 'error');
                            }
                        });
                    }
                });
            });
        });

        // Status Toggle
        $(document).on('change', '#togglebtn', function () {
            let id = $(this).data('id');
            $.ajax({
                url: "{{ route('employee.statusUpdate') }}",
                method: 'post',
                data: { id: id, _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Status ' + (response.data == 'disabled' ? 'disabled' : 'enabled') + ' successfully!',
                        icon: 'success',
                        confirmButtonColor: '#192e62',
                    });
                    // Refresh table to reflect new counts if needed
                    $('#employee-table').DataTable().draw(false);
                }
            });
        });

        function viewemployees(managerid) {
            $.ajax({
                url: "{{ route('employee.viewemployees') }}",
                method: 'get',
                data: { managerid: managerid },
                success: function (response) {
                    if (response.status == 200) {
                        let modal = new bootstrap.Modal(document.getElementById('employeelisting'));
                        modal.show();
                        $('#employeelistingbody').html(response.html);
                    }
                }
            });
        }

        function viewCampaigns(managerid) {
            $.ajax({
                url: "{{ route('employee.viewcampaigns') }}",
                method: 'get',
                data: { managerid: managerid },
                success: function (response) {
                    if (response.status == 200) {
                        let modal = new bootstrap.Modal(document.getElementById('campaignlisting'));
                        modal.show();
                        $('#campaignlistingbody').html(response.html);
                    }
                }
            });
        }

        function closemodal() {
            $('.modal').modal('hide');
        }

        $(document).on("click", ".editEmployee", function () {
            const url = $(this).data("url");
            if (url) window.location.href = url;
        });
    </script>
@endpush