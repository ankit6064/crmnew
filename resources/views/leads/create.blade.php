@extends('layouts.admin')
@section('content')

<style>
    /* Red border for invalid fields */
    .is-invalid {
        border: 1px solid #dc3545 !important;
        box-shadow: none !important;
    }


    /* Error text spacing */
    .error {
        margin-top: 4px;
        display: block;
    }

    /* Fix select styling */
    select.is-invalid {
        border: 1px solid #dc3545 !important;
    }
</style>

<div class="main-right addsubmanager addlead">
    <div class="right-side add-sub">
        <div class="graph">
            <h2>Add Lead</h2>

            <form method="post" action="{{ route('leads.store') }}" id="leadForm">
                @csrf

                <!-- Row 1 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Select Campaign</label>
                        <select name="source_id" id="source_id" style="padding:15px !important">
                            <option value="">Select Campaign</option>
                            @foreach($sources as $value)
                                <option value="{{ $value['id'] }}" {{ old('source_id') == $value['id'] ? 'selected' : '' }}>
                                    {{ $value['source_name'] }} {{ $value['description'] }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger error" id="source_id_error"></small>
                    </div>

                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" id="company_name" name="company_name"
                               placeholder="Enter Company Name" value="{{ old('company_name') }}">
                        <small class="text-danger error" id="company_name_error"></small>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Company Industry</label>
                        <input type="text" id="company_industry" name="company_industry"
                               placeholder="Enter Company Industry" value="{{ old('company_industry') }}">
                        <small class="text-danger error" id="company_industry_error"></small>
                    </div>

                    <div class="form-group">
                        <label>Prospect First Name</label>
                        <input type="text" id="prospect_first_name" name="prospect_first_name"
                               placeholder="Enter First Name" value="{{ old('prospect_first_name') }}">
                        <small class="text-danger error" id="prospect_first_name_error"></small>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Prospect Last Name</label>
                        <input type="text" id="prospect_last_name" name="prospect_last_name"
                               placeholder="Enter Last Name" value="{{ old('prospect_last_name') }}">
                        <small class="text-danger error" id="prospect_last_name_error"></small>
                    </div>

                    <div class="form-group">
                        <label>Designation</label>
                        <input type="text" id="designation" name="designation"
                               placeholder="Enter Designation" value="{{ old('designation') }}">
                        <small class="text-danger error" id="designation_error"></small>
                    </div>
                </div>

                <!-- Row 4 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Designation Level</label>
                        <input type="text" id="designation_level" name="designation_level"
                               placeholder="Enter Designation Level" value="{{ old('designation_level') }}">
                        <small class="text-danger error" id="designation_level_error"></small>
                    </div>

                    <div class="form-group">
                        <label>Contact Number 1</label>
                        <input type="text" id="contact_number_1" name="contact_number_1"
                               placeholder="Enter Contact Number 1" value="{{ old('contact_number_1') }}">
                        <small class="text-danger error" id="contact_number_1_error"></small>
                    </div>
                </div>

                <!-- Row 5 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Contact Number 2</label>
                        <input type="text" id="contact_number_2" name="contact_number_2"
                               placeholder="Enter Contact Number 2" value="{{ old('contact_number_2') }}">
                        <small class="text-danger error" id="contact_number_2_error"></small>
                    </div>

                    <div class="form-group">
                        <label>Prospect Email</label>
                        <input type="text" id="prospect_email" name="prospect_email"
                               placeholder="Enter Prospect Email" value="{{ old('prospect_email') }}">
                        <small class="text-danger error" id="prospect_email_error"></small>
                    </div>
                </div>

                <!-- Row 6 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>LinkedIn Address</label>
                        <input type="text" id="linkedin_address" name="linkedin_address"
                               placeholder="Enter LinkedIn Address" value="{{ old('linkedin_address') }}">
                        <small class="text-danger error" id="linkedin_address_error"></small>
                    </div>

                    <div class="form-group">
                        <label>Business Function</label>
                        <input type="text" id="bussiness_function" name="bussiness_function"
                               placeholder="Enter Business Function" value="{{ old('bussiness_function') }}">
                        <small class="text-danger error" id="bussiness_function_error"></small>
                    </div>
                </div>

                <!-- Row 7 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" id="location" name="location"
                               placeholder="Enter Location" value="{{ old('location') }}">
                        <small class="text-danger error" id="location_error"></small>
                    </div>

                    <div class="form-group">
                        <label>Time Zone</label>
                        <input type="text" id="timezone" name="timezone"
                               placeholder="Enter Time Zone" value="{{ old('timezone') }}">
                        <small class="text-danger error" id="timezone_error"></small>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="btn-group">
                    <button type="submit" class="btn btn-save" id="saveButton">Save</button>
                    @if(Auth::user()->is_admin == 2)
                    <button type="button" class="btn btn-cancel"
                            onclick="window.location.href='{{ route('managerdashboard') }}'">Cancel</button>
                            @else
                            <button type="button" class="btn btn-cancel"
                            onclick="window.location.href='{{ route('employeedashboard') }}'">Cancel</button>
                    @endif
                    </div>
                  
                    <!-- <button type="reset" class="btn btn-cancel">Cancel</button>

                    <a href="{{ url('leads') }}">
                        <button type="button" class="btn btn-back">Back</button>
                    </a> -->
                </div>

            </form>
        </div>
    </div>
</div>


<!-- SCRIPT -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {

// 1. Define the validation rules and messages in one place
const validationRules = {
    "source_id": "Please select a campaign.",
    "company_name": "Please enter company name.",
    "company_industry": "Please enter company industry.",
    "prospect_first_name": "Please enter prospect first name.",
    "prospect_last_name": "Please enter prospect last name.",
    "designation": "Please enter designation.",
    "designation_level": "Please enter designation level.",
    "contact_number_1": "Please enter contact number 1.",
    "contact_number_2": "Please enter contact number 2.",
    "linkedin_address": "Please enter linkedin address.",
    "bussiness_function": "Please enter business function.",
    "location": "Please enter location.",
    "timezone": "Please enter timezone.",
    "prospect_email": {
        "required": "Please enter prospect email.",
        "format": "Enter a valid email address."
    }
};

// 2. The Master Validation Function
function validateField(element) {
    let id = $(element).attr('id');
    let value = $(element).val().trim();
    let errorBox = $("#" + id + "_error");
    let isValid = true;
    let message = validationRules[id];

    // Special handling for Email
    if (id === "prospect_email") {
        if (value === "") {
            message = validationRules[id].required;
            isValid = false;
        } else if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value)) {
            message = validationRules[id].format;
            isValid = false;
        }
    } 
    // Standard Required Check
    else if (value === "" || value === null) {
        isValid = false;
    }

    // Apply visual changes
    if (isValid) {
        $(element).removeClass("is-invalid").addClass("is-valid");
        errorBox.text("");
    } else {
        $(element).removeClass("is-valid").addClass("is-invalid");
        errorBox.text(message);
    }

    return isValid;
}

// 3. Attach listeners for real-time feedback
$("input, select").on("input change", function() {
    validateField(this);
});

// 4. Handle Submit Button
$("#saveButton").on("click", function (e) {
    e.preventDefault();
    let isFormValid = true;

    // Loop through all defined rules and validate
    Object.keys(validationRules).forEach(function(id) {
        if (!validateField($("#" + id))) {
            isFormValid = false;
        }
    });

    if (isFormValid) {
        $("#leadForm").submit();
    } else {
        // Scroll to the first error smoothly
        $('html, body').animate({
            scrollTop: $(".is-invalid").first().offset().top - 100
        }, 200);
    }
});
});
</script>

@endsection
