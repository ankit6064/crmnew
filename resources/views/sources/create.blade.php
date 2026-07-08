@extends('layouts.admin')

@section('content')

    <style>
        .error-input {
            border: 1px solid red !important;
        }
    </style>

    <div class="main-right addsubmanager">
        <div class="right-side add-sub">
            <div class="graph">
                <h2>Add Campaign</h2>

                <form id="campaignForm" action="{{ route('sources.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="import_duplicate" id="import_duplicate" value="0">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="campaign">Campaign Name</label>
                            <input type="text" name="source_name" id="campaign" placeholder="Enter Campaign">
                        </div>

                        <div class="form-group">
                            <label for="sub_campaign">Sub Campaign</label>
                            <input type="text" name="description" id="sub_campaign" placeholder="Enter Sub Campaigns">
                        </div>
                    </div>


                    <div class="file-upload-section">
                        <label for="lead_file" class="file-label">Import Bulk Leads</label>
                        <div class="file-upload">
                            <label class="custom-file-upload">
                                Choose File
                                <input type="file" name="lead_file" id="lead_file" />
                            </label>
                            <span id="file-name">No File Chosen</span>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-save">Save</button>
                        <button type="button" class="btn btn-cancel"
                            onclick="window.location.href='{{ route('sources.getMangerSource') }}'">Cancel</button>
                    </div>

                </form>

            </div>
        </div>
    </div>



    <div class="popupcenter" id="duplicateModal"
        style="display:none; position: fixed; top:0; left:0; width:100vw; height:100vh;
                                        background-color: rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">

        <div class="popupp" style="background:#fff; padding:30px; border-radius:8px; text-align:center; width:350px;">

            <h4 style="margin-bottom:15px;">Campaign Already Exists</h4>

            <p style="margin-bottom:25px;">
                Do you want to import duplicate leads?
            </p>

            <div style="display:flex; justify-content:center; gap:15px;">
                <button id="importDuplicateBtn" class="btn btn-save">
                    Import Duplicate
                </button>

                <button id="skipDuplicateBtn" class="btn btn-cancel">
                    Don't Import
                </button>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function () {

            let skipCampaignCheck = false;

            $('#campaignForm').validate({

                rules: {
                    source_name: {
                        required: true,
                        minlength: 2
                    },
                    description: {
                        required: true,
                        minlength: 2
                    },
                    lead_file: {
                        required: true,
                        extension: "csv|xls|xlsx"
                    }
                },

                messages: {
                    source_name: {
                        required: "Please enter a campaign name.",
                        minlength: "Campaign name must be at least 2 characters"
                    },
                    description: {
                        required: "Please enter a sub campaign name.",
                        minlength: "Sub campaign name must be at least 2 characters"
                    },
                    lead_file: {
                        required: "Please choose a file.",
                        extension: "Only CSV, XLS, or XLSX files are allowed"
                    }
                },

                errorElement: 'span',

                errorPlacement: function (error, element) {
                    error.addClass('text-danger');

                    if (element.attr("type") === "file") {
                        error.insertAfter('#file-name');
                    } else {
                        error.insertAfter(element);
                    }
                },

                highlight: function (element) {
                    $(element).addClass('error-input');
                },

                unhighlight: function (element) {
                    $(element).removeClass('error-input');
                },

                submitHandler: function (form) {

                    if (skipCampaignCheck) {
                        form.submit();
                        return;
                    }

                    const campaignName = $('#campaign').val();
                    const description = $('#sub_campaign').val();

                    $.ajax({
                        url: "{{ route('sources.checkCampaignExists') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            source_name: campaignName,
                            description: description
                        },

                        success: function (response) {

                            if (response.status == 400) {

                                var validator = $('#campaignForm').validate();
                                validator.showErrors({
                                    source_name: "Campaign already exists."
                                });

                            } else {

                                skipCampaignCheck = true;
                                form.submit();

                            }

                        },

                        error: function () {
                            alert("Something went wrong while checking campaign.");
                        }

                    });
                }

            });


            // File name preview
            $('#lead_file').on('change', function () {
                const fileName = $(this).val().split('\\').pop();
                $('#file-name').text(fileName || 'No File Chosen');
            });


            // Import Duplicate
            $('#importDuplicateBtn').click(function () {

                $('#import_duplicate').val(1);
                skipCampaignCheck = true;

                $('#duplicateModal').hide();

                $('#campaignForm').submit();

            });


            // Don't Import Duplicate
            $('#skipDuplicateBtn').click(function () {

                $('#import_duplicate').val(0);
                skipCampaignCheck = true;

                $('#duplicateModal').hide();

                $('#campaignForm').submit();

            });

        });
    </script>


    <script>
        @if(session('success'))
            $(document).ready(function () {
                setTimeout(function () {
                    window.location.href = "{{ route('sources.getMangerSource') }}";
                }, 1500);
            });
        @endif
    </script>


@endsection