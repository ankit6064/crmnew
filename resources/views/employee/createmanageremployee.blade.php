@extends('layouts.admin')

@section('content')

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
    .text-danger {
        font-size: 13px;
        margin-top: 3px;
        display: block;
    }
</style>

<div class="main-right addsubmanager">
    <div class="right-side">
        <div class="graph">
            <h2>Add Employee</h2>

            <form method='post' id="employeeForm">
                @csrf
                <input type="hidden" name="manager_id" value="{{ $managerId ?? '' }}">
                <input type="hidden" id="previous_url" value="{{ url()->previous() }}">

                <div class="form-row">

                    <!-- First Name -->
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control"
                               placeholder="Enter First Name" value="{{ old('first_name') }}">
                        <span class="text-danger error-text first_name_error"></span>
                    </div>

                    <!-- Last Name -->
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control"
                               placeholder="Enter Last Name" value="{{ old('last_name') }}">
                        <span class="text-danger error-text last_name_error"></span>
                    </div>

                </div>

                <div class="form-row">

                    <!-- Phone -->
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" id="phone_no" name="phone_no" class="form-control"
                               placeholder="Enter Phone No" value="{{ old('phone_no') }}">
                        <span class="text-danger error-text phone_no_error"></span>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" id="email" name="email" class="form-control"
                               placeholder="Enter Email" value="{{ old('email') }}">
                        <span class="text-danger error-text email_error"></span>
                    </div>

                </div>

                <div class="form-row">

                    <!-- Address -->
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" id="address" name="address" class="form-control"
                               placeholder="Enter Address" value="{{ old('address') }}">
                        <span class="text-danger error-text address_error"></span>
                    </div>

                </div>

                <div class="form-row">

                    <!-- Dialer ID -->
                    <div class="form-group">
                        <label>Dialer ID</label>
                        <input type="text" id="dialer_id" name="dialer_id" class="form-control"
                               placeholder="Enter Dialer ID" value="{{ old('dialer_id') }}">
                        <span class="text-danger error-text dialer_id_error"></span>
                    </div>

                    <!-- Dialer Password -->
                    <div class="form-group">
                        <label>Dialer Password</label>
                        <input type="text" id="dialer_password" name="dialer_password" class="form-control"
                               placeholder="Enter Dialer Password" value="{{ old('dialer_password') }}">
                        <span class="text-danger error-text dialer_password_error"></span>
                    </div>

                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-save" onclick="submitform();">Save</button>
                    <button type="button" class="btn btn-cancel" onclick="goBack();">Cancel</button>
                </div>

            </form>
        </div>
    </div>

    @push('scripts')

    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>

    <script>
        function clearErrors() {
            $(".error-text").text(""); // clear error text
            $("input").css("border", ""); // remove red border
        }

        function submitform() {

            clearErrors();

            if (!$("#employeeForm").valid()) {
                return;
            }

            let form = $('#employeeForm')[0];
            let formData = new FormData(form);

            $.ajax({
                url: "{{ route('employee.storemanageremployeedetail') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                dataType: 'json',

                success: function(response) {
                    if (response.status == 200) {
                        toastr.success('Employee has been added successfully.');
                        setTimeout(function() {
                            goBack();
                        }, 1000);
                    } else {
                        alert(response.message);
                    }
                },

                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $("." + key + "_error").text(value[0]);
                            $("#" + key).css("border", "1px solid red");
                        });
                    } else {
                        alert('An unexpected error occurred.');
                    }
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
    </script>

    @endpush

</div>

@endsection
