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
                <h2>Edit Campaign</h2>

                <form id="campaignForm" action="{{ route('sources.update', [$data->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="import_duplicate" id="import_duplicate" value="0">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Campaign Name</label>
                            <input type="text" name="source_name" id="campaign" value="{{ $data->source_name }}"
                                placeholder="Enter Campaign">
                        </div>

                        <div class="form-group">
                            <label>Sub Campaign</label>
                            <input type="text" name="description" id="sub_campaign" value="{{ $data->description }}"
                                placeholder="Enter Sub Campaign">
                        </div>
                    </div>

                    <!-- <div class="form-row">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" value="{{ $data->start_date }}">
                            </div>

                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" value="{{ $data->end_date }}">
                            </div>
                        </div> -->

                    <div class="btn-group">
                        <button type="submit" class="btn btn-save">Update</button>
                        <button type="button" class="btn btn-cancel"
                            onclick="window.location.href='{{ route('sources.getMangerSource') }}'">
                            Cancel
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Duplicate Modal --}}
    <div class="popupcenter" id="duplicateModal"
        style="display:none; position: fixed; top:0; left:0; width:100vw; height:100vh;
                                                    background-color: rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">

        <div class="popupp" style="background:#fff; padding:30px; border-radius:8px; text-align:center; width:350px;">
            <h4>Campaign Already Exists</h4>

            <p style="margin:20px 0;">
                Do you still want to update it?
            </p>

            <div style="display:flex; justify-content:center; gap:15px;">
                <button id="confirmUpdateBtn" class="btn btn-save">
                    Yes, Update
                </button>

                <button id="cancelUpdateBtn" class="btn btn-cancel">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    <div class="popupcenter" id="successModal" style="display:none;">
        <div class="popupp">
            <div class="success-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <p>Campaign updated successfully.</p>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function () {

            let skipCheck = false;

            $('#campaignForm').validate({

                rules: {
                    source_name: { required: true, minlength: 2 },
                    description: { required: true, minlength: 2 }
                },

                messages: {
                    source_name: {
                        required: "Please enter campaign name",
                        minlength: "Minimum 2 characters"
                    },
                    description: {
                        required: "Please enter sub campaign",
                        minlength: "Minimum 2 characters"
                    }
                },

                errorElement: 'span',

                errorPlacement: function (error, element) {
                    error.addClass('text-danger');
                    error.insertAfter(element);
                },

                highlight: function (el) {
                    $(el).addClass('error-input');
                },

                unhighlight: function (el) {
                    $(el).removeClass('error-input');
                },

                submitHandler: function (form) {

                    if (skipCheck) {
                        form.submit();
                        return;
                    }

                    $.ajax({
                        url: "{{ route('sources.checkCampaignExists') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            source_name: $('#campaign').val(),
                            description: $('#sub_campaign').val(),
                            id: "{{ $data->id }}"
                        },

                        success: function (res) {
                            if (res.status == 400) {
                                var validator = $('#campaignForm').validate();
                                validator.showErrors({
                                    source_name: "Campaign already exists."
                                });
                            } else {
                                skipCheck = true;
                                form.submit();
                            }
                        },

                        error: function () {
                            alert("Something went wrong while checking campaign.");
                        }
                    });
                }
            });

            $('#confirmUpdateBtn').click(function () {
                skipCheck = true;
                $('#duplicateModal').hide();
                $('#campaignForm').submit();
            });

            $('#cancelUpdateBtn').click(function () {
                $('#duplicateModal').hide();
            });

        });
    </script>

    {{-- Success popup --}}
    <script>
        @if(session('success'))
            $(document).ready(function () {
                $('#successModal').show();

                setTimeout(function () {
                    $('#successModal').fadeOut(function () {
                        window.location.href = "{{ route('sources.getMangerSource') }}";
                    });
                }, 2000);
            });
        @endif

        @if(session('error'))
            $(document).ready(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#192e62',
                });
            });
        @endif
    </script>

@endsection