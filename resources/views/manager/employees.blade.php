@extends('layouts.admin')
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('manager') }}">Managers</a></li>
                <li class="breadcrumb-item active">Employee</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline-info">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Manager Name and Employees -->
                        <h4 class="m-b-0 text-white">
                            {{ $managerName->full_name ?? 'Manager Not Found' }} - Employees
                        </h4>

                        <!-- Back Button on the Right -->
                        <a href="{{ url()->previous() }}" class="btn btn-light d-flex align-items-center">
                            <span class="material-symbols-outlined mr-2">
                                arrow_back
                            </span>
                            Back
                        </a>
                    </div>
                    <div class="card-body align-right">
                    <a type="button" href="{{ route('employee.createmanageremployees', ['managerid' => $managerID]) }}" class="btn btn-success addButton"> + Add Employee</a>
                        <table class="table table-striped table-hover" id="manager-table">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Image</th>
                                    <th>Password</th>
                                    <th>Address</th>
                                    <th>Phone No</th>
                                    <th>Manager Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#spinner-overlay').show(); // Show full-page spinner
            var table = $('#manager-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route('manager.employees.data', ['manager_id' => request()->route('manager_id')]) }}',
                pageLength: 10,
                columns: [{
                        data: 'first_name',
                        name: 'first_name'
                    },
                    {
                        data: 'last_name',
                        name: 'last_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'orignal_password',
                        name: 'orignal_password'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'phone_no',
                        name: 'phone_no'
                    },
                    {
                        data: 'manager_type',
                        name: 'manager_type'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function() {
                    // Initialize Switchery for each checkbox
                    $('.switchery').each(function() {
                        if (!$(this).data('switchery')) {
                            new Switchery(this, {
                                color: '#192e62',
                                secondaryColor: '#f9f9f9',
                                jackColor: '#d3da44',
                                size: 'small'
                            });
                        }
                    });
                    tippy('.viewEmployee', {
                        content: 'View Employee',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.editEmployee', {
                        content: 'Edit Employee',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.deleteEmployee', {
                        content: 'Delete Employee',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                }
            });

            // Show spinner overlay on processing start
            table.on('preXhr.dt', function(e, settings, data) {
                $('#spinner-overlay').show(); // Show full-page spinner
            });

            // Hide spinner overlay when data is loaded
            table.on('xhr.dt', function(e, settings, json, xhr) {
                $('#spinner-overlay').hide(); // Hide full-page spinner
            });

            document.addEventListener('change', function(event) {
                if (event.target.matches('.switchery')) {

                    var switchElement = event.target;

                    // Accessing the checked state
                    var originalState = switchElement.checked;

                    // Accessing custom data attributes
                    var switchId = switchElement.dataset.sid;
                    var newState = !originalState; // Toggle the state

                    Swal.fire({
                        title: 'Are you sure?',
                        text: newState ? 'Change campaign status to Activate.' :
                            'Change campaign status to Deactivate.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, proceed!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Fetch the content when the tooltip is about to show
                            updateSourceStatus(originalState, switchId);
                        } else {

                            switchElement.checked = originalState;
                            // Trigger a change event to update Switchery
                            var event = document.createEvent('HTMLEvents');
                            event.initEvent('change', true, true);
                            switchElement.dispatchEvent(event);
                            // switchElement.dispatchEvent(event);

                            // Refresh or reinitialize Switchery
                            // var switchery = switchElement.__switchery;
                            // console.log(switchery);
                        }
                    });
                }
            });

        });

        function deleteemployee(employeeId){
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
                                url: `/employee/${employeeId}`, // Adjust this URL to match your route
                                type: 'POST', // Use GET request for deletion
                                success: function(response) {
                                    // Handle successful response (e.g., show a success message)
                                    Swal.fire(
                                        'Deleted!',
                                        'The employee has been deleted.',
                                        'success'
                                    );

                                    // Redraw the DataTable to reflect the changes
                                    $('#manager-table').DataTable()
                                        .draw(); // Redraw the DataTable
                                },
                                error: function(xhr, status, error) {
                                    // Handle error (e.g., show an error message)
                                    Swal.fire(
                                        'Error!',
                                        'There was an issue deleting the employee.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });       
                 }

  
    </script>
@endpush
