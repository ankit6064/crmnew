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
                        <input type="text" id="linkedinurl" class="form-control" placeholder="https://www.linkedin.com/in/username">
                        {{-- Error message container --}}
                        <div id="linkedin_error" class="error-text"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="closemodallinkedin()">Close</button>
                    <button id="save-data-reassigned" type="button" class="btn btn-info" onclick="updatelinkedin()">Update</button>
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
        $('#spinner-overlay').show();
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
});
</script>

@endsection