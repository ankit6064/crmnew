@extends('layouts.admin')

@section('content')

    <div class="main-right addsubmanager">
        <div class="right-side add-sub">
            <div class="graph">
                <h2>Add Leads to {{$source['source_name']}} </h2>
                <form id="campaignForm" action="{{ route('import_leads') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="import_duplicate" id="import_duplicate" value="0">
                    <input type="hidden" id="source_id" value="{{$source['id']}}">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="campaign">Campaign Name</label>
                            <input type="hidden" id="source_name1" name='source_name' class="form-control"
                                value="{{$source['id']}}">
                            <input type="text" id="source" name='source' readonly placeholder="Enter Campaign"
                                value="{{$source['source_name']}}">
                        </div>
                        <div class="form-group">
                            <label for="sub_campaign">Sub Campaign</label>
                            <input type="text" name="sub_campaign" readonly placeholder="Enter Campaign"
                                value="{{$source['description']}}">
                        </div>
                    </div>

                    <!-- <div class="form-row">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="text" name="start_date" id="start_date" placeholder="Start Date" readonly
                                        value="{{ $source['start_date'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="text" name="end_date" id="end_date" placeholder="End Date" readonly
                                        value="{{ $source['end_date'] }}">
                                </div>
                            </div> -->

                    <div class="file-upload-section">
                        <label for="lead_file" class="file-label">Import Bulk Leads <span
                                class="text-danger">*</span></label>
                        <div class="file-upload">
                            <label class="custom-file-upload">
                                Choose File
                                <input type="file" name="file" id="lead_file" accept=".csv,.xls,.xlsx" required /> </label>
                            <span id="file-name">No File Chosen</span>
                        </div>
                        <div id="file-error-container"></div>
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

    <div class="popupcenter" id="successModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
                        background-color: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div class="popupp">
            <div class="success-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <p>Campaign leads have been <br> imported successfully.</p>
        </div>
    </div>

    <div class="popupcenter" id="duplicateModal" style="display:none; position: fixed; top:0; left:0; width:100vw; height:100vh;
            background-color: rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
        <div class="popupp">
            <div class="success-icon">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <p>
                Duplicate leads detected.<br>
                Do you want to import duplicate leads?
            </p>
            <div style="margin-top:20px;">
                <button type="button" class="btn btn-save" id="importDuplicateBtn">Import Duplicate</button>
                <button type="button" class="btn btn-cancel" id="skipDuplicateBtn">Don't Import</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/additional-methods.min.js"></script>

    <script>
        $(document).ready(function () {

            // Fix for 'extension' method not being found
            if ($.validator) {
                $.validator.addMethod("extension", function (value, element, param) {
                    param = typeof param === "string" ? param.replace(/,/g, "|") : "png|jpe?g|gif";
                    return this.optional(element) || value.match(new RegExp("\\.(" + param + ")$", "i"));
                }, "Please enter a value with a valid extension.");
            }

            let skipDuplicateCheck = false;

            $.validator.setDefaults({ ignore: [] });

            $('#campaignForm').validate({
                rules: {
                    file: {
                        required: true,
                        extension: "csv,xls,xlsx"
                    }
                },
                messages: {
                    file: {
                        required: "You must upload a file.",
                        extension: "Only CSV, XLS, or XLSX files are permitted."
                    }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('text-danger');
                    if (element.attr("name") === "file") {
                        error.appendTo('#file-error-container');
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    $(element).closest('.file-upload').addClass('border border-danger');
                },
                unhighlight: function (element) {
                    $(element).closest('.file-upload').removeClass('border border-danger');
                },
                submitHandler: function (form) {
                    if (skipDuplicateCheck) {
                        form.submit();
                        return;
                    }

                    const fileName = $('#lead_file').val().split('\\').pop();
                    const ext = fileName.split('.').pop().toLowerCase();
                    const sourceName = $('#source').val();
                    const source_id = $('#source_name1').val();

                    if (ext === 'csv') {
                        $.ajax({
                            url: "{{ route('sources.checkCampaignExists') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                source_name: sourceName,
                                source_id: source_id
                            },
                            success: function (response) {
                                if (response.status == 400) {
                                    $('#duplicateModal').css('display', 'flex');
                                } else {
                                    skipDuplicateCheck = true;
                                    form.submit();
                                }
                            },
                            error: function () {
                                alert("Error checking campaign.");
                            }
                        });
                    } else {
                        skipDuplicateCheck = true;
                        form.submit();
                    }
                }
            });

            // File name display
            $('#lead_file').on('change', function () {
                const fileName = $(this).val().split('\\').pop();
                $('#file-name').text(fileName || 'No File Chosen');
                $(this).valid();
            });

            // Import Duplicate
            $('#importDuplicateBtn').click(function () {
                $('#import_duplicate').val(1);
                skipDuplicateCheck = true;
                $('#duplicateModal').hide();
                $('#campaignForm').submit();
            });

            // Don't Import Duplicate
            $('#skipDuplicateBtn').click(function () {
                $('#import_duplicate').val(0);
                skipDuplicateCheck = true;
                $('#duplicateModal').hide();
                $('#campaignForm').submit();
            });
        });
    </script>

    <script>
        @if(session('success'))
            $(document).ready(function () {
                setTimeout(function () {
                    $('#successModal').css('display', 'flex');
                    setTimeout(function () {
                        $('#successModal').fadeOut(300, function () {
                            window.location.href = "{{ route('sources.getMangerSource') }}";
                        });
                    }, 3000);
                }, 100);
            });
        @endif
    </script>

@endsection