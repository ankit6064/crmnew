@extends('layouts.admin')
@section('content')
<style type="text/css">
    ul.pagination {
        float: right;
        margin-right: 25px;
    }

    .filterform {
        display: flex;
        justify-content: end;
        margin-right: 25px;
    }

    .padd-all {
        padding: 1.25rem;
    }

    span.group_actions {
        padding-right: 12px;
        cursor: pointer;
    }

    span i.fa.fa-check.green-color.onchange_element_approve {
        padding-right: 8px;
    }
</style>

<form id="Reassignedform">
<div id="RevertModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Linkedin Address</h4>
                <button type="button" id="modelclose" class="close" data-dismiss="modal" aria-hidden="true" onclick="closerevertmodal();">×</button>
            </div>
            <div class="modal-body">
               
                    <div class="NoResponseData">
                        <div class="form-group" id="employe" name="employe">
                            <label class="control-label">Linkedin</label>
                            <input type="text" id="linkedinurl" name="camp_id" value="" class="form-control ">
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
            <input type="hidden" id="lead_id_quick_note" name="lead_id_quick_note">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" onclick="closerevertmodal();">Close</button>
                {{-- <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button> --}}
                <button id="save-data-reassigned" type="button" class="btn btn-info waves-effect waves-light ">Reassigned</button>
            </div>
        </div>
    </div>
</div>
</form>
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Dashboard</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Unapproved Leads</li>
        </ol>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('success') }}
                </div>
            @elseif (Session::has('error'))
                <div class="alert alert-danger" role="alert">
                    {{ Session::get('error') }}
                </div>
            @endif

            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white">Unapproved Leads</h4>
                </div>
                <div class="filterform"></div>
                <div class="container_search" style="display: none;">
                    <div class="card-body"></div>
                </div>
                <div class="table-responsive m-t-40 padd-all" id="table_data" style="padding-bottom: 50px;">
                    <table id="unapprovedLeadsTable"
                        class="display nowrap table table-hover table-striped table-bordered" cellspacing="0"
                        width="100%">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Employee Name</th>
                                <th>Company Name</th>
                                <th>Prospect Name</th>
                                <th>Designation</th>
                                <th>Date</th>
                                <th>Campaign Name</th>
                                <th>LinkedIn</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
        const unapprovedLeadsTable = $('#unapprovedLeadsTable');
        $('#spinner-overlay').show(); // Show full-page spinner

        // Initialize DataTable with server-side processing
        const table = unapprovedLeadsTable.DataTable({
            processing: false,
            serverSide: true,
            ajax: '{{ route("unapproved_manager_leads_list_pagination") }}',
            columns: [
                { data: "action", orderable: false },
                { data: "employee_name", orderable: false },
                { data: "company_name" },
                {
                    data: "prospect_full_name",
                    render: function (data, type, row) {
                        return `${data}`;
                    }
                },
                { data: "designation" },
                { data: "created_at" },
                { data: "source_name" },
                { data: "LinkedIn", orderable: false }
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

    function editmodule(id,url){
        $('#RevertModel').modal('show');
            }
</script>
@endsection
