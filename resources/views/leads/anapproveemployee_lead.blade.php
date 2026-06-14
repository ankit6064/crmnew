@extends('layouts.admin')
@section('content')

    {{-- Include SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Styling for the error message below input */
        .error-text {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }

        .is-invalid-input {
            border: 1px solid #dc3545 !important;
        }

        .table-container {
            width: 100% !important;
            overflow-x: auto !important;
        }
    </style>

    {{-- LinkedIn Update Modal --}}
    <form id="Reassignedform">
        <div id="RevertModel" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Linkedin Address</h4>
                        <button type="button" class="close" data-dismiss="modal" onclick="closemodallinkedin();">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Linkedin URL</label>
                            <input type="hidden" id="leadid">
                            <input type="text" id="linkedinurl" class="form-control"
                                placeholder="https://www.linkedin.com/in/username">
                            {{-- Error message container --}}
                            <div id="linkedin_error" class="error-text"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"
                            onclick="closemodallinkedin()">Close</button>
                        <button id="save-data-reassigned" type="button" class="btn btn-info"
                            onclick="updatelinkedin()">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="main-right">
        <div class="right-side submanager completed-leads closed-leads unaproved-leads">
            <h2>Unapproved Leads</h2>

            <div class="graph campaignslist unapprovedlisting">
                <div class="table">
                    <div class="table-container">
                        <table id="employee-table" class="display nowrap" style="width:100% !important">
                            <thead class="thead-main">
                                <tr>
                                    <th>Linkedin</th>
                                    <th>Company Name</th>
                                    <th>Campaign Name</th>
                                    <th>Prospect Name</th>
                                    <th>Designation</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        let hideSpinner = false; // Global flag to suppress spinner during LinkedIn updates

        $(document).ready(function () {
            const _token = $('input[name="_token"]').val();
            const spinnerOverlay = $('#spinner-overlay');
            const unapprovedLeadsTable = $('#employee-table');
            $('#spinner-overlay').show(); // Show full-page spinner

            // Initialize DataTable with server-side processing
            const table = unapprovedLeadsTable.DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route("unapproved_emp_leads_list_pagination") }}',
                columns: [
                    { data: "LinkedIn", orderable: false },
                    { data: "company_name" },
                    { data: "source_name" },
                    {
                        data: "prospect_full_name", orderable: false,
                        render: function (data, type, row) {
                            return `${data}`;
                        }
                    },
                    { data: "designation", orderable: false },
                    { data: "created_at" },
                ],
                lengthMenu: [[10, 20, 30], [10, 20, 30]],
                searching: true,
                drawCallback: function () {
                    $('#spinner-overlay').hide(); // Show full-page spinner

                    this.api().rows().every(function () {
                        const $row = $(this.node());
                        const $selectBox = $row.find('select');
                        const dataId = $selectBox.data('id');
                        if (dataId) {

                            $selectBox.val(dataId).trigger('change');
                        }
                    });
                    // Show spinner overlay on processing start
                    table.on('preXhr.dt', function (e, settings, data) {
                        if (hideSpinner) {
                            hideSpinner = false; // Reset the flag
                        } else {
                            $('#spinner-overlay').show(); // Show full-page spinner
                        }
                    });

                    // Hide spinner overlay when data is loaded
                    table.on('xhr.dt', function (e, settings, json, xhr) {
                        $('#spinner-overlay').hide(); // Hide full-page spinner
                    });
                    table.on('init.dt', function () {
                        $('div.dataTables_filter input')
                            .attr('placeholder', 'Search by campaign, prospect, company')
                            .css({ 'width': '250px', 'display': 'inline-block' });
                    });
                },
                initComplete: function () {
                    $(document).on('change', '.unapproved_lead', function () {
                        const leadId = $(this).attr('id');
                        $(`#icons_${leadId}`).show();
                    });

                    handleAction('.onchange_element_approve', 'approved');
                    handleAction('.onchange_element_cross', 'cancel');
                }
            });

            // Generic handler for approve/cancel actions
            function handleAction(selector, status) {
                $(document).on('click', selector, function () {
                    const leadId = $(this).data('id');
                    const empId = $(this).data('emp-id') || '';
                    const selectedValue = $(`#${leadId}`).val();
                    const selectedCampaignText = $(`#${leadId} option:selected`).text().trim();

                    let title = '';
                    let text = '';
                    let confirmButtonColor = '';

                    if (status === 'approved') {
                        if (!selectedValue) {
                            Swal.fire({
                                title: 'Select Campaign',
                                text: 'Please select a campaign/source from the dropdown list before approving.',
                                icon: 'warning',
                                confirmButtonColor: '#ffc107'
                            });
                            return;
                        }
                        title = 'Approve Lead?';
                        text = `Do you want to approve this lead for campaign: ${selectedCampaignText}?`;
                        confirmButtonColor = '#28a745';
                    } else {
                        title = 'Decline Lead?';
                        text = 'Do you want to decline this lead?';
                        confirmButtonColor = '#dc3545';
                    }

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: confirmButtonColor,
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: status === 'approved' ? 'Yes, approve it!' : 'Yes, decline it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route("updateApprovalStatus") }}',
                                type: 'POST',
                                data: {
                                    leadId,
                                    sourceId: selectedValue,
                                    status,
                                    user_id: empId,
                                    _token
                                },
                                success: () => {
                                    toastr.success(status === 'approved' ? 'Lead approved successfully' : 'Lead declined successfully');
                                    table.ajax.reload(null, false);
                                },
                                error: (jqXHR, textStatus, errorThrown) => {
                                    console.error(`Error: ${textStatus}`, errorThrown);
                                    toastr.error('Something went wrong.');
                                }
                            });
                        }
                    });
                });
            }
        });

        // LinkedIn Update Functions
        function editmodule(leadId, linkedinUrl) {
            $('#leadid').val(leadId);
            $('#linkedinurl').val(linkedinUrl);
            $('#linkedin_error').hide().text('');
            $('#linkedinurl').removeClass('is-invalid-input');
            $('#RevertModel').modal('show');
        }

        function closemodallinkedin() {
            $('#RevertModel').modal('hide');
        }

        function updatelinkedin() {
            const leadid = $('#leadid').val();
            const linkedinurl = $('#linkedinurl').val().trim();
            const token = $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content');

            if (!linkedinurl) {
                $('#linkedinurl').addClass('is-invalid-input');
                $('#linkedin_error').text('LinkedIn address cannot be empty').show();
                return;
            }

            if (linkedinurl.indexOf('linkedin') === -1) {
                $('#linkedinurl').addClass('is-invalid-input');
                $('#linkedin_error').text('Invalid LinkedIn address').show();
                return;
            }

            $.ajax({
                url: '{{ route("updatelinkedin") }}',
                type: 'POST',
                data: {
                    leadid: leadid,
                    linkedinurl: linkedinurl,
                    _token: token
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        $('#RevertModel').modal('hide');
                        hideSpinner = true;
                        $('#employee-table').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error('Something went wrong. Please try again.');
                }
            });
        }
    </script>
@endsection