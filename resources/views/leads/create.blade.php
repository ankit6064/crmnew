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

    /* ------------------------------
       VALIDATION FUNCTIONS
    ------------------------------- */

    function validateRequired(value) {
        return value.trim() !== "";
    }

    function validateEmail(value) {
        if (value.trim() === "") return false;
        return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
    }

    function runValidation(input, errorBox, validateFn, errorMsg) {
        let value = input.val();

        if (validateFn(value)) {
            input.removeClass("is-invalid").addClass("is-valid");
            errorBox.text("");
            return true;
        } else {
            input.removeClass("is-valid").addClass("is-invalid");
            errorBox.text(errorMsg);
            return false;
        }
    }

    function attachValidation(input, errorBox, validateFn, errorMsg) {
        input.on("input", function () {
            runValidation(input, errorBox, validateFn, errorMsg);
        });
    }

    function attachSelectValidation(select, errorBox, errorMsg) {
        select.on("change", function () {
            runValidation(select, errorBox, validateRequired, errorMsg);
        });
    }

    /* ------------------------------
       ATTACH VALIDATIONS
    ------------------------------- */

    attachSelectValidation($("#source_id"), $("#source_id_error"), "This field is required.");
    attachValidation($("#company_name"), $("#company_name_error"), validateRequired, "This field is required.");
    attachValidation($("#company_industry"), $("#company_industry_error"), validateRequired, "This field is required.");
    attachValidation($("#prospect_first_name"), $("#prospect_first_name_error"), validateRequired, "This field is required.");
    attachValidation($("#prospect_last_name"), $("#prospect_last_name_error"), validateRequired, "This field is required.");
    attachValidation($("#designation"), $("#designation_error"), validateRequired, "This field is required.");
    attachValidation($("#designation_level"), $("#designation_level_error"), validateRequired, "This field is required.");
    attachValidation($("#contact_number_1"), $("#contact_number_1_error"), validateRequired, "This field is required.");
    attachValidation($("#contact_number_2"), $("#contact_number_2_error"), validateRequired, "This field is required.");
    attachValidation($("#linkedin_address"), $("#linkedin_address_error"), validateRequired, "This field is required.");
    attachValidation($("#bussiness_function"), $("#bussiness_function_error"), validateRequired, "This field is required.");
    attachValidation($("#location"), $("#location_error"), validateRequired, "This field is required.");
    attachValidation($("#timezone"), $("#timezone_error"), validateRequired, "This field is required.");

    // Email
    attachValidation($("#prospect_email"), $("#prospect_email_error"), validateEmail, "Enter a valid email.");

    /* ------------------------------
       VALIDATE ON SUBMIT
    ------------------------------- */

    $("#saveButton").on("click", function (e) {
        e.preventDefault();

        let allValid = true;

        $("input, select").each(function () {
            let input = $(this);
            let id = input.attr("id");
            let errorBox = $("#" + id + "_error");

            if (id === "prospect_email") {
                if (!runValidation(input, errorBox, validateEmail, "Enter a valid email.")) {
                    allValid = false;
                }
            } else {
                if (!runValidation(input, errorBox, validateRequired, "This field is required.")) {
                    allValid = false;
                }
            }
        });

        if (allValid) {
            $("#leadForm").submit();
        }
    });

});
</script>

@endsection
