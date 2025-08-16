@extends('layouts.admin')
<!-- Include jQuery (Required) -->
@section('content')
    <!-- Bootstrap 4 CSS -->

    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/css/bootstrap-select.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/bootstrap-select.min.js"></script>

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('employees') }}">Employees</a></li>
                <li class="breadcrumb-item active">Add Employee</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-outline-info">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0 text-white">Add Sub Manager</h4>
                        <a href="{{ url()->previous() }}" class="btn btn-light d-flex align-items-center">
                            <span class="material-symbols-outlined mr-2">
                                arrow_back
                            </span>
                            Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form method='post' id="employeeForm" action="">
                            @csrf
                            <div class="form-body add_custom_table">
                                <div class="row p-t-20">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">First Name</label>
                                            <input type="text" id="first_name" name='first_name' class="form-control"
                                                placeholder="Enter First Name" value="{{ old('first_name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Last Name</label>
                                            <input type="text" id="last_name" name='last_name'
                                                class="form-control form-control-danger" placeholder="Enter Last Name"
                                                value="{{ old('last_name') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Phone No</label>
                                            <input type="text" id="phone_no" name='phone_no' class="form-control"
                                                placeholder="Enter Phone No" value="{{ old('phone_no') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Email</label>
                                            <input type="text" id="email" name='email' class="form-control"
                                                placeholder="Enter Email" value="{{ old('email') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Address</label>
                                            <input type="text" id="address" name='address' class="form-control"
                                                placeholder="Enter Address" value="{{ old('address') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Assign Employee</label>
                                            <select name="employees[]" class="form-control selectpicker" multiple
                                                data-live-search="true" title="Select Manager" required>
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

                                </div>
                            </div>
                        </form>
                        <div class="form-actions">
                            <button type="button" class="btn btn-success" onclick="submitform();"> <i
                                    class="fa fa-check"></i> Save</button>
                            <input type="reset" class="btn btn-inverse" value="Cancel" />
                        </div>

                    </div>
                </div>
            </div>
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
                    $('#email').css('border', '');
                }

                if (address === '') {
                    $('#address').css('border', '1px solid red');
                    is_valid = 0;
                } else {
                    $('#address').css('border', '');
                }

                if(is_valid == 1){
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
                    dataType:'json',
                    success: function (response) {
                        if(response.status == 200){
                       alert(response.message)
                        // Optionally redirect or reset form
                        window.location.href = "{{ route('employee.submanagerlisting') }}";
                        }else{
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