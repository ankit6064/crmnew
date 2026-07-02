@extends('layouts.admin')

@section('content')

    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/css/bootstrap-select.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/bootstrap-select.min.js"></script>

    <style>
        .bootstrap-select>.dropdown-toggle {
            padding: 20px 15px;
        }

        /* 🔴 Error input border */
        input.error,
        textarea.error,
        select.error {
            border: 1px solid #dc3545 !important;
        }

        /* 🟢 Valid input border (optional) */
        /* input.valid,
                textarea.valid,
                select.valid {
                    border: 1px solid #28a745 !important;
                } */

        .text-danger {
            font-size: 13px;
            margin-top: 5px;
        }
    </style>

    <div class="main-right addsubmanager">
        <div class="right-side">
            <h2>Update Employee</h2>
            <div class="graph">
                <form method="POST" id="employeeForm" action="{{ route('employee.update', $employee->id) }}"
                    onsubmit="event.preventDefault(); submitform();">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="employee_id" value="{{ $employee->id }}">
                    <input type="hidden" name="manager" id="manager" value="{{ Auth::id() }}">
                    <input type="hidden" id="previous_url" value="{{ url()->previous() }}">

                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" id="first_name" name="first_name" class="form-control"
                                placeholder="Enter First Name" value="{{ old('first_name', $employee->first_name) }}">
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" id="last_name" name="last_name" class="form-control"
                                placeholder="Enter Last Name" value="{{ old('last_name', $employee->last_name) }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" id="phone_no" name="phone_no" class="form-control"
                                placeholder="Enter Phone No" value="{{ old('phone_no', $employee->phone_no) }}">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" id="email" name="email" class="form-control" placeholder="Enter Email"
                                value="{{ old('email', $employee->email) }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" id="address" name="address" class="form-control" placeholder="Enter Address"
                                value="{{ old('address', $employee->address) }}">
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="text" id="orignal_password" name="orignal_password" class="form-control"
                                placeholder="Enter New Password"
                                value="{{ old('orignal_password', $employee->orignal_password) }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Dialer ID</label>
                            <input type="text" id="dialer_id" name="dialer_id" class="form-control"
                                placeholder="Enter Dialer ID" value="{{ old('dialer_id', optional($employee->dialer)->dialer_id) }}">
                        </div>

                        <div class="form-group">
                            <label>Dialer Password</label>
                            <input type="text" id="dialer_password" name="dialer_password" class="form-control"
                                placeholder="Enter Dialer Password" value="{{ old('dialer_password', optional($employee->dialer)->dialer_password) }}">
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-save" onclick="submitform();">
                            Update
                        </button>
                        <button type="button" class="btn btn-cancel" onclick="goBack();">
                            Cancel
                        </button>
                    </div>

                </form>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>

            <script>
                $(document).ready(function () {
                    $('.selectpicker').selectpicker();
                });

                function submitform() {
                    if (!$('#employeeForm').valid()) return;

                    let form = $('#employeeForm')[0];
                    let formData = new FormData(form);
                    let employeeId = $('#employee_id').val();

                    $.ajax({
                        url: "{{ route('employee.update', $employee->id) }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 200) {
                                toastr.success('Employee has been updated successfully.');
                                setTimeout(function () {
                                    goBack();
                                }, 1000);
                            } else {
                                alert(response.message || 'Something went wrong.');
                            }
                        },
                        error: function () {
                            alert('An error occurred while submitting the form.');
                        }
                    });
                }

                function goBack() {
                    let prevUrl = $('#previous_url').val();
                    if (prevUrl && prevUrl !== window.location.href && !prevUrl.includes('/login') && !prevUrl.includes('/check-submanager')) {
                        window.location.href = prevUrl;
                    } else {
                        window.location.href = "{{ route('employee.manageremployeeindex') }}";
                    }
                }

                // ✅ jQuery Validation with red borders
                $('#employeeForm').validate({
                    rules: {
                        first_name: "required",
                        last_name: "required",
                        phone_no: "required",
                        email: {
                            required: true,
                            email: true
                        }
                    },
                    messages: {
                        first_name: "Please enter first name.",
                        last_name: "Please enter last name.",
                        phone_no: "Please enter phone number.",
                        email: {
                            required: "Please enter email.",
                            email: "Enter a valid email."
                        }
                    },
                    errorClass: 'error text-danger',
                    validClass: 'valid',
                    highlight: function (element) {
                        $(element).addClass('error').removeClass('valid');
                    },
                    unhighlight: function (element) {
                        $(element).removeClass('error').addClass('valid');
                    },
                    errorPlacement: function (error, element) {
                        error.insertAfter(element);
                    }
                });
            </script>
        @endpush
    </div>

@endsection