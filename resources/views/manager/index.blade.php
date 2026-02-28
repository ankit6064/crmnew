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
            /* Default: blue info message */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

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
    </style>



<div class="main-right">
  <div class="right-side">
    <h2>Manager Listing</h2>
    <div class="row">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="card-header">
            <h4>Active</h4>
            <div class="card-icon acti">
              <i class="fa-regular fa-user"></i>
            </div>
          </div>
          <div class="card-body">
            <h2>{{ $active }}</h2>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="card-header">
            <h4>Inactive</h4>
            <div class="card-icon inactive">
              <i class="fa-solid fa-user-tie"></i>
            </div>
          </div>
          <div class="card-body">
            <h2>{{ $deactive }}</h2>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Create Sub Manager -->
    <div class="modal fade" id="assignsubmanager" tabindex="-1" role="dialog" aria-labelledby="totalLeadsLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="totalLeadsLabel">Create Sub Manager</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closemodal()">
              <span aria-hidden="true" style="color: black;">&times;</span>
            </button>
          </div>
          <div id="submanagerbody" class="modal-body">
          </div>
          <div class="modal-footer">
            <button style="background-color:#192e62;color:#fff;border-radius:3px" onclick="transfer();">Assign Campaign</button>
            <button style="background-color:#192e62;color:#fff;border-radius:3px" onclick="skip();">Skip</button>
            <button type="button" class="btn btn-info" data-dismiss="modal" onclick="closemodal()">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Assign Employees to Manager -->
    <div class="modal fade" id="assignempmanager" tabindex="-1" role="dialog" aria-labelledby="totalLeadsLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="totalLeadsLabel">Assign Employees to Manager</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closemodalemp()">
              <span aria-hidden="true" style="color: black;">&times;</span>
            </button>
          </div>
          <div id="assignempmanagerbody" class="modal-body">
            <input type="hidden" name="submanagerid" id="manageremployeeid">
            <div class="container">
              @if(isset($employees) && !empty($employees))
              <div class="row">
                @foreach($employees as $emp)
                <div class="col-md-4">
                  <div class="card mb-3">
                    <div class="card-body">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="employees[]" value="{{ $emp->id }}" id="emp{{ $emp->id }}">
                        <label class="form-check-label" for="emp{{ $emp->id }}">
                          {{ $emp->first_name . ' ' . $emp->last_name }}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              @else
              <div class="alert alert-info" role="alert">
                No sources available.
              </div>
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button style="background-color:#192e62;color:#fff;border-radius:3px" onclick="assignemployees();">Assign Employees</button>
            <button type="button" class="btn btn-info" data-dismiss="modal" onclick="closemodalemp()">Skip for Now</button>
          </div>
        </div>
      </div>
    </div>

    <div class="graph">
      <div class="row">
        <div class="add-submanager">
          <a href="{{ route('manager.create') }}">Add Manager</a>
        </div>
      </div>

      <!-- Modal: Employee Listing -->
      <div class="modal fade" id="employeelisting" tabindex="-1" role="dialog" aria-labelledby="totalLeadsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="totalLeadsLabel">Employee Listing</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closemodal()">
                <span aria-hidden="true" style="color: black;">&times;</span>
              </button>
            </div>
            <div id="employeelistingbody" class="modal-body">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-info" data-dismiss="modal" onclick="closemodal()">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Employee Table -->
      <div class="table">
        <div class="table-container">
          <table class="table table-striped table-hover" id="employee-table">
            <thead class="thead-main">
              <tr>
              <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
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
                ajax: '{{ route('manager.data') }}',
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
                    tippy('.deleteManager', {
                        content: 'Delete Manager',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.switchery', {
                        content: 'Manage Status',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    
                }
            });

            table.on('init.dt', function () {
        $('div.dataTables_filter input')
          .attr('placeholder', 'Search by name,email')
          .css({ 'width': '250px', 'display': 'inline-block' });
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
                // Check if the clicked element has the class 'delete-manager'
                if (event.target.matches('.deleteManager')) {
                    event.preventDefault();

                    // Get the manager ID from the data-id attribute of the clicked element
                    const managerId = event.target.parentElement.getAttribute('data-id');
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
                                url: `/manager/${managerId}`, // Adjust this URL to match your route
                                type: 'POST', // Use GET request for deletion
                                success: function(response) {
                                    // Handle successful response (e.g., show a success message)
                                    Swal.fire(
                                        'Deleted!',
                                        'The manager has been deleted.',
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
                                        'There was an issue deleting the manager.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                }
            });
            tippy('.addManager', {
                content: 'Add Manager',
                placement: 'top',
                arrow: true,
                animation: 'scale'
            });
        });

     

        function changestatus(id){
          // Example: Toggle a status via AJAX
          $.ajax({
                url: "{{ route('employee.statusUpdate') }}", // Corrected route syntax
                method: 'post',
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                },
                dataType: "json",

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
        }
    </script>
@endpush
