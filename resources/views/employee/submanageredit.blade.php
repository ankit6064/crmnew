@extends('layouts.admin')
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('employees') }}">Employees</a></li>
                <li class="breadcrumb-item active">Update Employee</li>
            </ol>
        </div>
        <div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-outline-info">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0 text-white">Update Employee</h4>
                        <!-- Back Button on the Right -->
                        <a href="{{ route('employee.submanagerlisting') }}" class="btn btn-light d-flex align-items-center">
                            <span class="material-symbols-outlined mr-2">
                                arrow_back
                            </span>
                            Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form method='post' action="{{ route('employee.update', [$employee->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-body add_custom_table">
                                <div class="row p-t-20">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">First Name</label>
                                            <input type="readonly" id="first_name" name='first_name' class="form-control"
                                                placeholder="Enter First Name"
                                                value="{{ old('first_name', $employee->first_name) }}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Last Name</label>
                                            <input type="readonly" id="last_name" name='last_name'
                                                class="form-control form-control-danger" placeholder="Enter Last Name"
                                                value="{{ old('last_name', $employee->last_name) }}" disabled>
                                        </div>
                                    </div>
                                </div>




                        </form>
                    </div>
                </div>
            </div>
            <button type="button" href="#" class="btn btn-success addButton addEmployee" onclick="addemployee();"><span
                    class="material-symbols-outlined">
                    person_add
                </span>
            </button>
            <table class="table table-bordered">
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
                                <td><input type="reset" class="btn btn-inverse" value="Remove"
                                        onclick="removeemp('{{ $emp->id }}')" /></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" style="text-align:center; color:#999;">No employees available.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>


        <div class="modal fade" id="assignempmanager" tabindex="-1" role="dialog" aria-labelledby="totalLeadsLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="totalLeadsLabel">Assign Employees to Manager</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="closemodalemp()">
                            <span aria-hidden="true" style="color: black;">&times;</span>
                        </button>
                    </div>
                    <!-- <form action="" method="post" id="submanagerform"> -->

                    <div id="assignempmanagerbody" class="modal-body">
                        <input type="hidden" name="submanagerid" id="manageremployeeid" value="{{ $employee->id }}">
                        <div class="container">

                            @if(isset($unassignedemployees) && !empty($unassignedemployees))
                                <div class="row">
                                    @foreach($unassignedemployees as $emp)
                                        <div class="col-md-4">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="employees[]"
                                                            value="{{ $emp->id }}" id="emp{{ $emp->id }}">
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
                    <!-- </form> -->
                    <div class="modal-footer">
                        <button style="background-color:#192e62;color:#fff;border-radius:3px"
                            onclick="assignemployees();">Assign Employees</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal"
                            onclick="closemodalemp()">Close</button>
                    </div>
                </div>

            </div>
            </form>
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