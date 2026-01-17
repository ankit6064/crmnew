@extends('layouts.admin')

@section('content')


    <div class="main-right addsubmanager">
        <div class="right-side add-sub">
            <div class="graph">
            <h2>Add Leads to {{$source['source_name']}} </h2>
                <form id="campaignForm" action="{{ route('import_leads') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="campaign">Campaigns</label>
                            <input type="hidden" id="source_name1" name='source_name' readonly class="form-control" placeholder="Enter Campaign" value="{{$source['id']}}">

                            <input type="text" id="source" name='source' readonly placeholder="Enter Campaign" value="{{$source['source_name']}}">
                        </div>
                        <div class="form-group">
                            <label for="sub_campaign">Sub Campaigns</label>
                            <input type="text" name="sub_campaign"  readonly  placeholder="Enter Campaign" value="{{$source['description']}}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="campaign">Start Date</label>
                            <input type="text" name="start_date" id="start_date" placeholder="Start Date" readonly value="{{ $source['start_date'] }}">
                        </div>
                        <div class="form-group">
                            <label for="sub_campaign">End Date</label>
                            <input type="text" name="end_date" id="end_date" placeholder="End Date" readonly value="{{ $source['end_date'] }}">
                        </div>
                    </div>

                    <div class="file-upload-section">
                        <label for="lead_file" class="file-label">Import Bulk Leads</label>
                        <div class="file-upload">
                            <label class="custom-file-upload">
                                Choose File
                                <input type="file" name="file" id="lead_file" />
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

    <div class="popupcenter" id="successModal"
     style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background-color: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div class="popupp">
        <div class="success-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <p>Campagin has been <br> added successfully.</p>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
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
                        required: "Please enter a campaign name",
                        minlength: "Campaign name must be at least 2 characters"
                    },
                    description: {
                        required: "Please enter a sub campaign name",
                        minlength: "Sub campaign name must be at least 2 characters"
                    },
                    lead_file: {
                        required: "Please choose a file",
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
                }
            });

            // Update file name display
            $('#lead_file').on('change', function () {
                const fileName = $(this).val().split('\\').pop();
                $('#file-name').text(fileName || 'No File Chosen');
            });
        });
    </script>

<script>
    @if(session('success'))
        $(document).ready(function () {
            // Add small delay to ensure layout/CSS is applied
            setTimeout(function () {
                $('#successModal').css('display', 'flex');

                // Auto-close after 3 seconds and redirect
                setTimeout(function () {
                    $('#successModal').fadeOut(300, function () {
                        window.location.href = "{{ route('sources.getMangerSource') }}";
                    });
                }, 3000);
            }, 100); // small delay before showing modal
        });
    @endif
</script>



@endsection