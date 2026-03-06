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
                    { data: "company_name" },
                    { data: "source_name" },
                    {
                        data: "prospect_full_name", orderable: false ,
                        render: function (data, type, row) {
                            return `${data}`;
                        }
                    },
                    { data: "designation", orderable: false  },
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
                        $('#spinner-overlay').show(); // Show full-page spinner
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

                    if (leadId && confirm(`Do you want to ${status} this lead?`)) {
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
                            success: () => table.ajax.reload(),
                            error: (jqXHR, textStatus, errorThrown) => console.error(`Error: ${textStatus}`, errorThrown)
                        });
                    }
                });
            }
        });
    </script>
@endsection