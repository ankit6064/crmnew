@extends('layouts.admin')
@section('content')

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
                        <label>Linkedin</label>
                        <input type="hidden" id="leadid">
                        <input type="text" id="linkedinurl" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" onclick="closemodallinkedin()">Close</button>
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
            <!-- <div class="row">
                <div class="add-submanager">
                    <input type="search" id="search" name="search" placeholder="search...">
                </div>
            </div> -->

            <div class="table">
                <div class="table-container">
                <table id="employee-table" class="display nowrap" style="width:100% !important">
                        <thead class="thead-main">
                            <tr>
                                <th>Linkdin</th>
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

                {{-- CUSTOM PAGINATION --}}
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
    processing: false,
    serverSide: true,
    ajax: '{{ route("unapproved_manager_leads_list_pagination") }}',
    ordering: true,
        autoWidth: false,
        deferRender: true,

        // scrollX: true,
        scrollCollapse: true,  // performance
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
        // Force correct width AFTER arrows are added
        this.api().columns.adjust();

        // Small delay ensures DOM is fully painted
        setTimeout(() => {
            $('#spinner-overlay').hide();
        }, 100);
    }
});

/* Show spinner on every ajax reload */
table.on('preXhr.dt', function () {
    $('#spinner-overlay').show();
});

/* Hide spinner after redraw (ordering, paging, search) */
table.on('draw.dt', function () {
    $('#spinner-overlay').hide();
});

    // Custom search input → DataTable search
    $('#search').keyup(function () {
        table.search(this.value).draw();
    });

    // Approve / Cancel Actions
    $(document).on('click', '.onchange_element_approve', function () {
        updateStatus($(this).data('id'), 'approved', $(this).data('emp-id'));
    });

    $(document).on('click', '.onchange_element_cross', function () {
        updateStatus($(this).data('id'), 'cancel', $(this).data('emp-id'));
    });
});

// Update approve/cancel
function updateStatus(leadId, status, empId = '') {
    if (!confirm(`Do you want to ${status} this lead?`)) return;

    $.post('{{ route("updateApprovalStatus") }}', {
        leadId, status, user_id: empId, _token: '{{ csrf_token() }}'
    }, function () {
        $('#unapprovedLeadsTable').DataTable().ajax.reload();
    });
}

// LinkedIn update modal functions
function editmodule(id, url) {
    $('#leadid').val(id);
    $('#linkedinurl').val(url);
    $('#RevertModel').modal('show');
}

function closemodallinkedin() {
    $('#RevertModel').modal('hide');
}

function updatelinkedin() {
    const leadid = $('#leadid').val();
    const linkedinurl = $('#linkedinurl').val();

    if (!linkedinurl.trim()) {
        $('#linkedinurl').css('border', '1px solid red');
        return;
    }

    $.post('{{ route("updatelinkedin") }}', {
        leadid, linkedinurl, _token: '{{ csrf_token() }}'
    }, function (response) {
        alert('Linkedin address updated');
        location.reload();
    });
}
</script>

@endsection
