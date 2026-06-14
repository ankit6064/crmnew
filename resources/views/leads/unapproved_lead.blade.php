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

            <div class="graph campaignslist">
                <div class="table">
                    <div class="table-container">
                        <table id="employee-table" class="display nowrap" style="width:100% !important">
                            <thead class="thead-main">
                                <tr>
                                    <th>Linkedin</th>
                                    <th>Employee Name</th>
                                    <th>Company Name</th>
                                    <th>Prospect Name</th>
                                    <th>Designation</th>
                                    <th>Date</th>
                                    <th>Campaign Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let hideSpinner = false; // Global flag to suppress spinner during LinkedIn updates

        $(document).ready(function () {
            $('#spinner-overlay').show();

            const table = $('#employee-table').DataTable({
                processing: false, // We use our custom spinner-overlay instead
                serverSide: true,
                ajax: '{{ route("unapproved_manager_leads_list_pagination") }}',
                ordering: true,
                autoWidth: false,
                deferRender: true,
                scrollCollapse: true,
                columns: [
                    { data: "LinkedIn", orderable: false },
                    { data: "employee_name", orderable: false },
                    { data: "company_name", orderable: false },
                    { data: "prospect_full_name", orderable: false },
                    { data: "designation", orderable: false },
                    { data: "created_at", orderable: true },
                    { data: "source_name", orderable: false },
                    { data: "action", orderable: false }
                ],
                initComplete: function () {
                    this.api().columns.adjust();
                }
            });

            // --- LOADER LOGIC FOR SEARCH & PAGINATION ---

            // Show loader whenever an AJAX request starts (Search, Page Change, Sort)
            table.on('preXhr.dt', function () {
                if (hideSpinner) {
                    hideSpinner = false; // Reset the flag
                } else {
                    $('#spinner-overlay').show();
                }
            });

            // Hide loader whenever the table finishes drawing
            table.on('draw.dt', function () {
                $('#spinner-overlay').hide();
            });

            // Custom search input placeholder
            table.on('init.dt', function () {
                $('div.dataTables_filter input')
                    .attr('placeholder', 'Search by campaign, employee, prospect, company')
                    .css({ 'width': '250px', 'display': 'inline-block' });
            });

            // Custom search trigger
            $('#search').keyup(function () {
                // The 'preXhr' event above will handle showing the spinner 
                // as soon as table.search().draw() is called.
                table.search(this.value).draw();
            });

            // Action Handlers (Approve/Cross)
            $(document).on('click', '.onchange_element_approve', function () {
                updateStatus($(this).data('id'), 'approved', $(this).data('emp-id'));
            });

            $(document).on('click', '.onchange_element_cross', function () {
                updateStatus($(this).data('id'), 'cancel', $(this).data('emp-id'));
            });


            function updateStatus(leadId, status, empId) {
                const selectedValue = $(`#${leadId}`).val();
                const selectedCampaignText = $(`#${leadId} option:selected`).text().trim();
                const _token = $('input[name="_token"]').val();

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
                                toastr.success(status === 'approved' ? 'Lead has been approved.' : 'Lead has been declined.');
                                table.ajax.reload(null, false);
                            },
                            error: (jqXHR, textStatus, errorThrown) => {
                                console.error(`Error: ${textStatus}`, errorThrown);
                                toastr.error('Something went wrong.');
                            }
                        });
                    }
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
            const _token = $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content');

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
                    _token: _token
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        $('#RevertModel').modal('hide');
                        hideSpinner = true;
                        $('#employee-table').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Something went wrong. Please try again.');
                }
            });
        }
    </script>

@endsection