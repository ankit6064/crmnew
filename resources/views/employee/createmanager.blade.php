@extends('layouts.admin')
<!-- Include jQuery (Required) -->
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
    </style>
    <div class="main-right addsubmanager">
        <div class="right-side">
            <h2>Add Submanager</h2>
            <div class="graph">
                <form method='post' id="employeeForm">
                    @csrf <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" id="first_name" name='first_name' class="form-control"
                                placeholder="Enter First Name" value="{{ old('first_name') }}">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" id="last_name" name='last_name' class="form-control form-control-danger"
                                placeholder="Enter Last Name" value="{{ old('last_name') }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" id="phone_no" name='phone_no' class="form-control"
                                placeholder="Enter Phone No" value="{{ old('phone_no') }}">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" id="email" name='email' class="form-control" placeholder="Enter Email"
                                value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" id="address" name='address' class="form-control" placeholder="Enter Address"
                                value="{{ old('address') }}">
                        </div>
                        <div class="form-group">
                            <label>Assign Employee</label>
                            <select name="employees[]" class="form-control selectpicker" multiple data-live-search="true"
                                data-selected-text-format="count > 3" title="Select Manager"
                                style="padding:20px 15px !important">
                                @if(isset($employees) && !empty($employees))
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->first_name . ' ' . $emp->last_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>

                        </div>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-save" onclick="submitform();">Save</button>
                        <button type="reset" class="btn btn-cancel">Cancel</button>
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
            </script>

            <script>
                function submitform() {
                    var firstname = $('#first_name').val();
                    var lastname = $('#last_name').val();
                    var phoneno = $('#phone_no').val();
                    var email = $('#email').val();
                    var address = $('#address').val();
                    var is_valid = 1;

                    if (firstname === '') {
                        $('#first_name').css('border', '1px solid red');
                        is_valid = 0;
                    } else {
                        $('#first_name').css('border', '');
                    }

                    if (lastname === '') {
                        $('#last_name').css('border', '1px solid red');
                        is_valid = 0;
                    } else {
                        $('#last_name').css('border', '');
                    }

                    if (phoneno === '') {
                        $('#phone_no').css('border', '1px solid red');
                        is_valid = 0;
                    } else {
                        $('#phone_no').css('border', '');
                    }

                    if (email === '') {
                        $('#email').css('border', '1px solid red');
                        is_valid = 0;
                    } else {
                        // simple email regex
                        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailPattern.test(email)) {
                            $('#email').css('border', '1px solid red');
                            is_valid = 0;
                        } else {
                            $('#email').css('border', '');
                        }
                    }


                    if (address === '') {
                        $('#address').css('border', '1px solid red');
                        is_valid = 0;
                    } else {
                        $('#address').css('border', '');
                    }

                    if (is_valid == 1) {
                        var form = $('#employeeForm')[0]; // get raw DOM element
                        var formData = new FormData(form); // create FormData object

                        $.ajax({
                            url: "{{ route('managerstore') }}",
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('input[name="_token"]').val()
                            },
                            dataType: 'json',
                            success: function (response) {
                                if (response.status == 200) {
                                    alert(response.message)
                                    // Optionally redirect or reset form
                                    window.location.href = "{{ route('employee.submanagerlisting') }}";
                                } else {
                                    alert(response.message)
                                    return;
                                }
                            },
                            error: function (xhr) {
                                console.log(xhr.responseText);
                                alert('An error occurred while submitting the form');
                            }
                        });
                    }

                }
            </script>




        @endpush
@endsection