@extends('layouts.admin')
@section('content')
    <div class="main-right addsubmanager Update">
    <div class="right-side add-sub">
        <div class="graph">
            <!-- Header -->
            <div class="header-btn-container">
                <h2 class="section-heading">Update Employee</h2>
                <a href="{{ route('employee.submanagerlisting') }}" class="back-btn">Back</a>
            </div>

            <!-- Update Form -->
            <form method="POST" action="{{ route('employee.update', [$employee->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" 
                               value="{{ old('first_name', $employee->first_name) }}" 
                               placeholder="First Name" disabled>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" 
                               value="{{ old('last_name', $employee->last_name) }}" 
                               placeholder="Last Name" disabled>
                    </div>
                </div>

                <!-- Assign Employees Button -->
                <div class="header-btn-container employeetomanager">
                    <button type="button" class="back-btn" onclick="addemployee();">
                        Add Employee to Manager
                    </button>
                </div>

                <!-- Employee Table -->
                <div class="table-container employee-table-update">
                    <table>
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($employees) && $employees->isNotEmpty())
                                @foreach($employees as $emp)
                                    <tr>
                                        <td>{{ $emp->first_name . ' ' . $emp->last_name }}</td>
                                        <td>
                                            <button type="button" class="remove-btn" 
                                                    onclick="removeemp('{{ $emp->id }}','{{ $employee->id }}')">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" style="text-align:center; color:#999;">No employees available.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Employees Modal -->
<div class="modal fade" id="assignempmanager" tabindex="-1" role="dialog" aria-labelledby="assignEmployeesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="assignEmployeesLabel">Assign Employees to Manager</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closemodalemp()">
                    <span aria-hidden="true" style="color: black;">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="assignempmanagerbody">
                <input type="hidden" name="submanagerid" id="manageremployeeid" value="{{ $employee->id }}">

                <div class="container">
                    @if(isset($unassignedemployees) && !empty($unassignedemployees))
                        <div class="row">
                            @foreach($unassignedemployees as $emp)
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
                            No employees available.
                        </div>
                    @endif
                </div>
            </div>

            <div class="modal-footer">
                <button style="background-color:#192e62;color:#fff;border-radius:3px" onclick="assignemployees();">
                    Assign Employees
                </button>
                <button type="button" class="btn btn-info" data-dismiss="modal" onclick="closemodalemp()">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

    @push('scripts')
        <script>
            function removeemp(id) {
                $.ajax({
                    url: "{{ route('employee.removeemployee') }}", // Corrected route syntax
                    method: 'post',
                    data: {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                    },
                    dataType: "json",

                    success: function (response) {
                        if (response.status == 200) {
                            alert(response.message);
                            location.reload(true);
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

            function addemployee() {
                $('#assignempmanager').modal('show');
            }

            function closemodalemp() {
                $('#assignempmanager').modal('hide');

            }

            function assignemployees() {
                var submanagerid = $("#manageremployeeid").val();
                let selectedEmployees = [];
                $('input[name="employees[]"]:checked').each(function () {
                    selectedEmployees.push($(this).val());
                });
                if (selectedEmployees.length === 0) {
                    alert("Please select at least one employee.");
                    return; // Stop further action
                }
                $.ajax({
                    url: "{{ route('employee.assignemployees') }}", // Corrected route syntax
                    method: 'post',
                    data: {
                        submanagerid,
                        selectedEmployees,
                        _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                    },
                    dataType: "json",

                    success: function (response) {
                        location.reload(true);
                    },
                    error: function (error) {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        </script>
    @endpush
@endsection