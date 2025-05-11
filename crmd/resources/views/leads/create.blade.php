@extends('layouts.admin')
@section('content')

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Dashboard</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('leads') }}">Leads</a></li>
            <li class="breadcrumb-item active">Add Lead</li>
        </ol>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline-info add_custom_table">
                <div class="card-header">
                    <h4 class="m-b-0 text-white">Add Lead</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('leads.store') }}" id="leadForm">
                        @csrf
                        <div class="form-body add_custom_table">
                            <div class="row">
                                <!-- Select Campaign -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Select Campaign</label>
                                        <select name="source_id" class="form-control custom-select" id="source_id">
                                            <option value="">Select Campaign</option>
                                            @foreach($sources as $value)
                                                <option value="{{ $value->id }}" {{ old('source_id') == $value->id ? 'selected' : '' }}>
                                                    {{ $value->source_name }} {{$value->description}}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger error" id="source_id_error"></small>
                                    </div>
                                </div>

                                <!-- Company Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Company Name</label>
                                        <input type="text" id="company_name" name="company_name" class="form-control" placeholder="Enter Company Name" value="{{ old('company_name') }}">
                                        <small class="text-danger error" id="company_name_error"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Company Industry -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Company Industry</label>
                                        <input type="text" id="company_industry" name="company_industry" class="form-control" placeholder="Enter Company Industry" value="{{ old('company_industry') }}">
                                        <small class="text-danger error" id="company_industry_error"></small>
                                    </div>
                                </div>

                                <!-- Prospect First Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Prospect First Name</label>
                                        <input type="text" id="prospect_first_name" name="prospect_first_name" class="form-control" placeholder="Enter First Name" value="{{ old('prospect_first_name') }}">
                                        <small class="text-danger error" id="prospect_first_name_error"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Prospect Last Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Prospect Last Name</label>
                                        <input type="text" id="prospect_last_name" name="prospect_last_name" class="form-control" placeholder="Enter Last Name" value="{{ old('prospect_last_name') }}">
                                        <small class="text-danger error" id="prospect_last_name_error"></small>
                                    </div>
                                </div>

                                <!-- Designation -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Designation</label>
                                        <input type="text" id="designation" name="designation" class="form-control" placeholder="Enter Designation" value="{{ old('designation') }}">
                                        <small class="text-danger error" id="designation_error"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Designation Level -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Designation Level</label>
                                        <input type="text" id="designation_level" name="designation_level" class="form-control" placeholder="Enter Designation Level" value="{{ old('designation_level') }}">
                                        <small class="text-danger error" id="designation_level_error"></small>
                                    </div>
                                </div>

                                <!-- Contact Number 1 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Contact Number 1</label>
                                        <input type="text" id="contact_number_1" name="contact_number_1" class="form-control" placeholder="Enter Contact Number 1" value="{{ old('contact_number_1') }}">
                                        <small class="text-danger error" id="contact_number_1_error"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Contact Number 2 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Contact Number 2</label>
                                        <input type="text" id="contact_number_2" name="contact_number_2" class="form-control" placeholder="Enter Contact Number 2" value="{{ old('contact_number_2') }}">
                                        <small class="text-danger error" id="contact_number_2_error"></small>
                                    </div>
                                </div>

                                <!-- Prospect Email -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Prospect Email</label>
                                        <input type="text" id="prospect_email" name="prospect_email" class="form-control" placeholder="Enter Prospect Email" value="{{ old('prospect_email') }}">
                                        <small class="text-danger error" id="prospect_email_error"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Linkedin Address -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Linkedin Address</label>
                                        <input type="text" id="linkedin_address" name="linkedin_address" class="form-control" placeholder="Enter Linkedin Address" value="{{ old('linkedin_address') }}">
                                        <small class="text-danger error" id="linkedin_address_error"></small>
                                    </div>
                                </div>

                                <!-- Bussiness Function -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Business Function</label>
                                        <input type="text" id="bussiness_function" name="bussiness_function" class="form-control" placeholder="Enter Business Function" value="{{ old('bussiness_function') }}">
                                        <small class="text-danger error" id="bussiness_function_error"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Location -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Location</label>
                                        <input type="text" id="location" name="location" class="form-control" placeholder="Enter Location" value="{{ old('location') }}">
                                        <small class="text-danger error" id="location_error"></small>
                                    </div>
                                </div>

                                <!-- Time Zone -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Time Zone</label>
                                        <input type="text" id="timezone" name="timezone" class="form-control" placeholder="Enter Time Zone" value="{{ old('timezone') }}">
                                        <small class="text-danger error" id="timezone_error"></small>
                                    </div>
                                </div>
                            </div>
                            </form>

                            <div class="form-actions">
                                <button type="button" class="btn btn-success" id="saveButton"> <i class="fa fa-check"></i> Save</button>
                                <input type="reset" class="btn btn-inverse" value="Cancel" />
                                <a href="{{ url('leads') }}"><button type="button" class="btn btn-info">Back</button></a>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {

        // Function to validate individual fields
        function validateField(inputElement, errorElement, validationFunction, errorMessage) {
            inputElement.on('input', function () {
                const value = inputElement.val().trim();
                const isValid = validationFunction(value);

                if (isValid) {
                    inputElement.removeClass('is-invalid').addClass('is-valid');
                    errorElement.text('');
                } else {
                    inputElement.removeClass('is-valid').addClass('is-invalid');
                    errorElement.text(errorMessage); // Show the error message
                }
            });
        }

        // Validation function for required fields
        const validateRequired = (value) => value !== '';
        const validateEmail = (value) => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);

        // Define error message for each field
        const errorMessage = "This field is required.";
        const emailErrorMessage = "Please enter a valid email.";

        // Add validation for all fields
        validateField($('#source_id'), $('#source_id_error'), validateRequired, errorMessage);  // Campaign Name
        validateField($('#company_name'), $('#company_name_error'), validateRequired, errorMessage); // Company Name
        validateField($('#company_industry'), $('#company_industry_error'), validateRequired, errorMessage); // Industry
        validateField($('#prospect_first_name'), $('#prospect_first_name_error'), validateRequired, errorMessage); // First Name
        validateField($('#prospect_last_name'), $('#prospect_last_name_error'), validateRequired, errorMessage); // Last Name
        validateField($('#designation'), $('#designation_error'), validateRequired, errorMessage); // Designation
        validateField($('#designation_level'), $('#designation_level_error'), validateRequired, errorMessage); // Designation Level
        validateField($('#contact_number_1'), $('#contact_number_1_error'), validateRequired, errorMessage); // Contact 1
        validateField($('#contact_number_2'), $('#contact_number_2_error'), validateRequired, errorMessage); // Contact 2
        validateField($('#prospect_email'), $('#prospect_email_error'), validateEmail, emailErrorMessage); // Email
        validateField($('#linkedin_address'), $('#linkedin_address_error'), validateRequired, errorMessage); // LinkedIn
        validateField($('#bussiness_function'), $('#bussiness_function_error'), validateRequired, errorMessage); // Business Function
        validateField($('#location'), $('#location_error'), validateRequired, errorMessage); // Location
        validateField($('#timezone'), $('#timezone_error'), validateRequired, errorMessage); // Timezone

        // Handle form submission when save button is clicked
        $('#saveButton').on('click', function (e) {
            e.preventDefault();  // Prevent form submission

            let formHasErrors = false;

            // Hide the general error message initially
            $('#formErrorMessage').hide();

            // Validate all fields on submit
            $('input, select').each(function () {
                const inputElement = $(this);
                const errorElement = $('#' + inputElement.attr('id') + '_error');

                // Check if the field is invalid (either empty or has error class)
                const value = inputElement.val().trim();
                const isRequiredField = (inputElement.attr('id') === 'source_id' || inputElement.attr('id') === 'company_name');
                const isValid = (isRequiredField && value !== '') || (!isRequiredField && value !== '' || inputElement.val() !== '');

                // If the field is invalid, mark it as invalid and display error message
                if (!isValid) {
                    inputElement.removeClass('is-valid').addClass('is-invalid');
                    errorElement.text(inputElement.attr('id') === 'source_id' || inputElement.attr('id') === 'company_name' 
                        ? errorMessage
                        : 'This field is required');
                    formHasErrors = true;
                }
            });

            // If there are any validation errors, show the general error message
            if (formHasErrors) {
                $('#formErrorMessage').show();
            } else {
                $('#formErrorMessage').hide();
            }

            // If there are no validation errors, submit the form
            if (!formHasErrors) {
                $('#leadForm').submit();  // Submit the form if all fields are valid
            }
        });
    });
</script>





@endsection
