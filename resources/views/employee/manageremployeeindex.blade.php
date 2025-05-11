@extends('layouts.admin')
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Employees</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline-info">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Manager Name and Employees -->
                        <h4 class="m-b-0 text-white">Employees</h4>
                        <!-- Back Button on the Right -->
                        <a href="{{ url()->previous() }}" class="btn btn-light d-flex align-items-center">
                            <span class="material-symbols-outlined mr-2">
                                arrow_back
                            </span>
                            Back
                        </a>
                    </div>
                    <div class="card-body align-right">
                        <a type="button" href="{{ route('employee.createmanageremployees') }}" class="btn btn-success addButton addEmployee"><span
                            class="material-symbols-outlined">
                            person_add
                        </span>
                    </a>
                        <table class="table table-striped table-hover" id="employee-table">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Image</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                    <th>Address</th>
                                    <th>Phone No</th>
                                    <th>Manage Login Permission</th>
                                    <th>Change Status</th>
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

    @if (session('swalError'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('swalError') }}',
                toast: true, // Enable toast notification
                position: 'top-end', // Position the toast in the top right corner
                showConfirmButton: false, // Don't show a confirmation button
                timer: 3000, // Show the toast for 3 seconds
                timerProgressBar: true, // Show a progress bar as the toast disappears
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('#spinner-overlay').show(); // Show full-page spinner
            var table = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route('employee.manageremployeedata') }}',
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
                        data: 'image',
                        name: 'image'
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
                        data: 'disable_login',
                        name: 'disable_login',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
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
                        content: 'View Employees',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.editManager', {
                        content: 'Edit Manager',
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

            // Attach event listener to delete links
            document.addEventListener('click', function(event) {
                // Check if the clicked element has the class deleteEmployee
                if (event.target.matches('.deleteEmployee')) {
                    event.preventDefault();

                    // Get the employee ID from the data-id attribute of the clicked element
                    const employeeId = event.target.parentElement.getAttribute('data-id');
                    // Optionally, use SweetAlert to confirm the deletion
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
                                    $('#employee-table').DataTable()
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
            });
            tippy('.addEmployee', {
                content: 'Add Emoloyee',
                placement: 'top',
                arrow: true,
                animation: 'scale'
            });
        
            

        });


        

      
    </script>

<script>
    $(document).on('change', '#togglebtn', function () {
        // Get the data attribute from the button
        let id = $(this).data('id');
        // Perform your action here
        console.log('Button clicked with ID:', id);

        // Example: Toggle a status via AJAX
        $.ajax({
            url: "{{ route('employee.statusUpdate') }}", // Corrected route syntax
            method: 'post',
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
            },
            dataType:"json",
            
            success: function (response) {
                if (response.status == 200) {
                    alert('Status Changed successfully!');
                } else {
                    alert('Failed to toggle status.');
                }
            },
            error: function (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    });

    function disablelogin(employeeid,employeemail){
        $.ajax({
            url: "{{ route('employee.manageemployeelogin') }}", // Corrected route syntax
            method: 'post',
            data: {
                employeeid,
                employeemail,
                _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
            },
            dataType:"json",
            
            success: function (response) {
                if (response.status == 200) {
                    alert(response.message);
                } else {
                    alert('Failed to toggle status.');
                }
            },
            error: function (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    
    }
</script>
@endpush
