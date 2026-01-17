@extends('layouts.admin')
{{-- @push('head-style') --}}
<style>
    .table td,
    .table th {
        padding: 5px 0 !important;
        font-size: 12.5px;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background: #f2f4f859 !important;
    }

    #exampleModal .modal-dialog,
    #externalManagerModal .modal-dialog {
        max-width: 340px;
    }

    #exampleModal .modal-dialog select,
    #externalManagerModal .modal-dialog select {
        border: 1px solid #ccc;
        width: 100%;
        padding: 8px;
        border-radius: 5px;
        font-size: 12.5px;
        margin-bottom: 5px;
    }

    #exampleModal .modal-header,
    #externalManagerModal .modal-header {
        background: #081840;
        border-color: #081840;
        border-radius: 0.3rem 0.3rem 0 0;
    }

    #exampleModal .modal-header .modal-title,
    #externalManagerModal .modal-header .modal-title {
        color: #fff;
    }

    #exampleModal .modal-content,
    #externalManagerModal .modal-content {
        border: none;
    }

    .tooltip1 {
        position: relative;
        display: inline-block;
        border-bottom: 1px dotted #192e62;
    }

    .tooltip1 .tooltiptext1 {
        visibility: hidden;
        width: 200px;
        background-color: #192e62;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        /*padding: 4px 0;*/
        position: absolute;
        z-index: 1;
        top: -5px;
        left: 110%;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 13px 18px;
        flex-wrap: wrap;
        height: fit-content;
    }

    .tooltip1 .tooltiptext1::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 100%;
        margin-top: -16px;
        border-width: 5px;
        border-style: solid;
        border-color: transparent #192e62 transparent transparent;
    }

    .tooltip1:hover .tooltiptext1 {
        visibility: visible;
    }

    fieldset.group {
        margin: 0;
        padding: 0;
        margin-bottom: 1.25em;
        padding: .125em;
    }

    .group {
        border: 1px solid #000;
        padding: 10px !important;
    }

    fieldset.group legend {
        margin: 0;
        padding: 0;
        font-weight: bold;
        /*margin-left: 20px; */
        font-size: 100%;
        color: black;
        margin-top: 10px;
        text-align: center;
    }

    ul.checkbox {
        margin: 0;
        padding: 0;
        margin-left: 20px;
        margin-top: 10px;
        list-style: none;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-evenly;

    }

    ul.checkbox li input {
        margin-right: .25em;
    }

    /*#manager_external {
                    margin-bottom: 30px;
                }*/

    ul.checkbox li {
        border: 1px transparent solid;
        display: inline-block;
        width: 12em;
    }

    ul.checkbox li label {
        margin-left: ;
    }

    ul.checkbox li:hover,
    ul.checkbox li.focus {
        width: 12em;
    }

    #externalManagerModal .modal-dialog {
        max-width: 585px;
    }

    ul.pagination {
        float: right;
    }

    .table th,
    .table thead th {
        border: 1px solid #08184026 !important;
        padding: 11px 20px !important;
        align-items: center;
        text-align: left;
        vertical-align: middle !important;
    }

    .table td,
    .table th {
        vertical-align: middle !important;
        border: 1px solid #c9d1e3 !important;
    }

    .assign_manager_icon i {
        color: #000;
        font-size: 17px;
    }

    .assign_manager_icon {
        background: transparent !important;
    }

    .assign_manager_icon img {
        width: 19px;
    }

    a.tooltiplink {
        position: relative;
    }

    a.tooltiplink:hover::after {
        content: attr(data-title);
        background-color: #d3e215;
        color: #000;
        padding: 8px;
        font-size: 10px;
        line-height: 14px;
        display: block;
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        z-index: 1;

    }

    .unselect_manager_error {
        font-size: 14px;
        color: red;
    }

    /*input[type="checkbox"]:disabled+label:before {
        top: -4px;
        left: -5px;
        border-top: 2px solid transparent;
        border-left: 2px solid transparent;
        border-right: 2px solid #26a69a;
        border-bottom: 2px solid #26a69a;
        transform: rotate(40deg);
        backface-visibility: hidden;
        transform-origin: 100% 100%;
    }

    */
    [type="checkbox"]:checked:disabled+label::before {
        border-right: 2px solid #26a69a !important;
        border-bottom: 2px solid #26a69a !important;
    }

    .fields001 {
        font-size: 16px !important;
        text-align: center;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .Custom_fields {
        justify-content: left !important;
        margin-left: 60px !important;
    }

    ul.checkbox.custom_lead_fields.Custom_fields li {
        width: 14.7em !important;
    }

    .group {
        overflow-x: auto;
        height: auto;
        min-height: 130px;
    }

    #externalManagerModal .modal-body {
        padding: 1rem 1rem 0rem 1rem !important;
    }

    .tooltiptext1 p {
        margin: 3px;
        padding: 0px;
        white-space: break-spaces;
    }

    .source-item {
        cursor: pointer;
    }

    [type="checkbox"]:checked,
    [type="checkbox"]:not(:checked) {
        opacity: unset !important;
        position: static !important;
    }

    /* Dim background modal when child modal opens */
.modal.dimmed {
    opacity: 0.35;
    pointer-events: none;
}

/* Ensure active modal stays normal */
/* .modal.show {
    opacity: 1 !important;
    pointer-events: auto;
} */


.modal.removedimmed {
    opacity: 1;
    pointer-events: none;
}

</style>
{{-- @endpush --}}

@section('content')
    <?php
    $defaultLeadFieldsArray = Config::get('constants.default_lead_fields');
    $customLeadFieldsArray = Config::get('constants.custom_lead_fields');
    $url = url(''); ?>

    <div class="main-right">
        <div class="right-side submanager">
            <h2>Campaign Listing</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="card-header">
                            <h4>Active</h4>
                            <div class="card-icon acti">
                                <i class="fa-regular fa-user"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{$active}}</h2>
                            <!-- <div class="arrow-icon"><img src="images/card-arrow.png"></div> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="card-header">
                            <h4>Inactive</h4>
                            <div class="card-icon inactive">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{$inactive}}</h2>
                            <!-- <div class="arrow-icon"><img src="images/card-arrow.png"></div> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="graph campaignslist">
                <div class="row">

                    @if(Auth::user()->is_admin == SUBMANAGER)
                        @if(!empty($permissions) && $permissions->campaign->add == 1)
                            <div class="add-submanager"><a href="{{ route('sources.create') }}">Add Campaign</a></div>

                        @endif
                    @else
                        <div class="add-submanager"><a href="{{ route('sources.create') }}">Add Campaign</a></div>

                    @endif
                </div>
                <div class="table-container" id="table_data">
                    <div class="table-container-inner">
                        <table id="employee-table">
                            <thead class="thead-main">
                                <tr>

                                    <th width="200" style="text-align:center">Campaign</th>
                                    <th width="200" style="text-align:center">Sub Campaign</th>
                                    <th width="200" style="text-align:center">Transfer</th>
                                    <th width="50" style="text-align:center">Leads</th>
                                    <th width="50" style="text-align:center">Distribution</th>
                                    <th width="50" style="text-align:center">Manager</th>
                                    <th width="50" style="text-align:center">Status</th>

                                    <th style="text-align:center">Created On</th>
                                    <th style="text-align:center">Modified On</th>

                                    <!--<th>Total Amount</th>-->
                                    <th style="text-align:center">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal to show leads by company name and lead status -->
    <div class="modal fade" id="totalLeadsModal" tabindex="-1" role="dialog" aria-labelledby="totalLeadsLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="totalLeadsLabel">Total Leads According to Company</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closemodal()">
                        <span aria-hidden="true" style="color: black;">&times;</span>
                    </button>
                </div>
                <div id="totalLeadsModalBody" class="modal-body">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="sourceId" name="sourceId" value="">
                    <button type="button" class="btn btn-info" data-dismiss="modal" onclick="closemodal()">Close</button>
                </div>
            </div>

        </div>
        </form>
    </div>


    <div class="modal fade" id="transferleadmodal" tabindex="-1" role="dialog" aria-labelledby="totalLeadsLabel"
        aria-hidden="true">
        <form method="post" id="transferform">
            <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="totalLeadsLabel">Company listing</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="closemodaltransfer()">
                            <span aria-hidden="true" style="color:black">&times;</span>
                        </button>
                    </div>
                    <div id="transferleadbody" class="modal-body">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <span id="error_company_select"
                        style="padding-left: var(--bs-modal-padding);color:red;display:none">Select
                        atleast any one company</span>
                    <div id="source_div" style="padding-left: var(--bs-modal-padding);">
                    </div>
                    <span id="error_source_select"
                        style="padding-left: var(--bs-modal-padding);color:red;display:none">Select
                        transfer source</span>

                    <div class="modal-footer">
                        <input type="hidden" id="sourceoldId" name="sourceoldId" value="">
                        <button type="button" class="btn btn-success addButton" onclick="transferleads()">Transfer</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal"
                            onclick="closemodaltransfer()">Close</button>
                    </div>
                </div>

            </div>
        </form>
    </div>


    <div class="modal fade" id="employeeleads" tabindex="-1" role="dialog" aria-labelledby="totalLeadsLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document" style="height: auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="totalLeadsLabel">Distribution of leads for each employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closemodal()">
                        <span aria-hidden="true" style="color:black">&times;</span>
                    </button>
                </div>
                <div id="employeeleadsdetails" class="modal-body">

                </div>
                <div class="modal-footer">
                    <input type="hidden" id="sourceId" name="sourceId" value="">
                    <button type="button" class="btn btn-info" data-dismiss="modal" onclick="closemodal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal to show user list to assign leads -->
    <div class="modal fade" id="leadsAssignToUserModal" tabindex="-1" role="dialog" aria-labelledby="leadsAssignToUserLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leadsAssignToUserLabel">Users List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="closeleadassignmodal();">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="leadsAssignToUserModalBody" class="modal-body">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="companyName" name="companyName" value="">
                    <input type="hidden" id="assignLeadsType" name="assignLeadsType" value="">
                    <input type="hidden" id="existingAssignedUser" name="assignLeadsType" value="">
                    <button type="button" class="btn btn-info" data-dismiss="modal"
                        onclick="closeleadassignmodal();">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal to show users list that are currently assigned -->
    <div class="modal fade"
     id="leadsAssignedUsers"
     tabindex="-1"
     role="dialog"
     aria-labelledby="leadsAssignedUsersLabel"
     aria-hidden="true"
     data-backdrop="static"
     data-keyboard="false">

        <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leadsAssignedUsersLabel">Select from assigned users</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="leadassignedmodal();">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="leadsAssignedUsersBody" class="modal-body">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="getUserListing()">Next</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal"
                        onclick="leadassignedmodal();">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- =========== MODAL POPUP ================ -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Campaign Assign</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="campaign_id_new">
                    <select id="manager">
                        <option value="0">Choose Manager</option>
                        <?php 
                                                                         if (isset($managers) && !empty($managers[0])) {
        foreach ($managers as $manager) { 
                                                                            ?>
                        <option value="<?php        echo $manager->id; ?>">
                            <?php        echo $manager->name; ?>
                        </option>

                        <?php    }
    } ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close"
                        onclick="closemodal();">Close</button>
                    <button type="button" id="assignToManagerPopupBtn" class="btn btn-success"
                        onclick="savemanager();">Assign to Manager</button>
                </div>
            </div>
        </div>
    </div>


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var leadscountUrl = "{{ route('sources.leadscount') }}";
        var transferleadsurl = "{{ route('transferleads') }}";

    </script>
    <script>
        $(document).ready(function () {
            // Initializing the DataTable (for example)
            $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                searching: true,
                pageLength: 10,
                ordering: true,
                ajax: {
                    url: '{{ route('campaigns_list_ajax_pagination') }}',
                    type: 'GET',
                },
                columns: [
                    { data: 'source_name_new', name: 'source_name' },
                    { data: 'description', name: 'description' },
                    { data: 'transfer', name: 'transfer', orderable: false },
                    { data: 'total_leads_new', name: 'total_leads' },
                    { data: 'company_distribution', name: 'company_distribution', orderable: false },
                    { data: 'manager_name', name: 'manager_name', orderable: false },
                    { data: 'status', name: 'status', orderable: false },

                    { data: 'created_at_new', name: 'created_at', orderable: true },
                    { data: 'updated_at_new', name: 'updated_at', orderable: true },
                    { data: 'action', name: 'action', orderable: false },
                ],


                initComplete: function () {
                    $('#spinner-overlay').hide();

                    // Add placeholder to the default DataTable search bar
                    $('.dataTables_filter input[type="search"]')
                        .attr('placeholder', 'Search Campaign Name')
                        .css('width', '250px'); // optional styling
                },

                drawCallback: function () {
                    // Initialize Switchery for each checkbox

                    // Initialize Switchery + Tippy on each checkbox
                    $('.switchery').each(function () {

// 1️⃣ Initialize Switchery ONLY once on checkbox
if (!this.switchery) {
    this.switchery = new Switchery(this, {
        color: '#192e62',
        secondaryColor: '#f9f9f9',
        jackColor: '#d3da44',
        size: 'small'
    });
}

// 2️⃣ Switchery UI element is NEXT sibling
var switcheryUI = this.nextSibling;

// 3️⃣ Destroy existing tooltip if present
if (switcheryUI._tippy) {
    switcheryUI._tippy.destroy();
}

// 4️⃣ Attach tooltip to Switchery UI
tippy(switcheryUI, {
    content: 'Manage Status',
    theme: 'light-border',
    placement: 'top'
});
});


                    // 🔥 INIT TIPPY (NEW)
                    tippy('[data-tippy-content]', {
                        theme: 'light-border',
                        placement: 'top',
                    });

                }
            });

            // Modal initialization
            $('#spinner-overlay').show(); // Show spinner initially

            // Open the Total Leads Modal
            $('#sources').on('click', '.modal-trigger', function () {
                var id = $(this).data('id'); // Get ID for dynamic modal loading
                clickmodal(id);
            });

            // Hide spinner once modal content is loaded
            $('#sources').on('processing.dt', function (e, settings, processing) {
                if (processing) {
                    $('#spinner-overlay').show();
                } else {
                    $('#spinner-overlay').hide();
                }
            });

            $('#employee-table').on('preXhr.dt', function () {
                $('#spinner-overlay').show();
            });

            // Hide loader after data is loaded
            $('#employee-table').on('xhr.dt', function () {
                $('#spinner-overlay').hide();
            });

        });

        // Function to open the modal and load content dynamically
        function clickmodal(id) {
            // Show modal
            $('#totalLeadsModal').modal('show');

            // Show loading spinner while content loads
            let loaderHtml = '<div class="d-flex justify-content-center">' +
                '<div class="spinner-border" role="status">' +
                '<span class="sr-only">Loading...</span>' +
                '</div>' +
                '</div>';
            $('#totalLeadsModalBody').html(loaderHtml);

            // Load dynamic content
            $('#totalLeadsModalBody').load('/source-lead/' + id, function () {
                $('#sourceId').val(id);
            });
        }

        // Reset modal content when hidden
        $('#totalLeadsModal').on('hidden.bs.modal', function () {
            let loaderHtml = '<div class="d-flex justify-content-center">' +
                '<div class="spinner-border" role="status">' +
                '<span class="sr-only">Loading...</span>' +
                '</div>' +
                '</div>';
            $('#totalLeadsModalBody').html(loaderHtml); // Reset to spinner when modal is hidden
        });

        // Get already assigned users for leads
        function getAlredayAssignedUsers(ele) {
            var leadsType = [];
            let companyName = $(ele).data("company");

            // Get selected lead types
            $("input[name='" + companyName + "[]']:checked").each(function (index, obj) {
                leadsType[index] = $(obj).val();
            });

            // If no leads type is selected
            if (leadsType.length == 0) {
                swal.fire("Error!", 'Please select at least one lead type.', "error");
                return false;
            }

            if (companyName) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('assigned.users') }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "companyName": companyName,
                        "sourceId": $('#sourceId').val(),
                    },
                    beforeSend: function () {
                        $(".loader-ajax").show();
                    },
                    success: function (response) {
                        $('#totalLeadsModal').addClass('dimmed');
                        $(".loader-ajax").hide();
                        $('#assignLeadsType').val(leadsType);
                        $('#companyName').val(companyName);
                        $('#leadsAssignedUsersBody').html(response);
                        $('#leadsAssignedUsers').modal('show');
                    }
                });
            } else {
                swal.fire({
                    toast: true,
                    icon: 'error',
                    title: 'Company is empty.',
                    animation: false,
                    position: 'top-right',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            }
        }

        // Function to get the user listing and show modal
        function getUserListing() {
            let existingAssignedUserId = $("input[type='radio'][name=selectedUser]:checked").val();
            if (!existingAssignedUserId) {
                swal.fire("Error!", 'Please select at least one user.', "error");
                return false;
            }

            $('#leadsAssignedUsers').modal('hide');

            // Load user list for assignment
            $('#leadsAssignToUserModalBody').load('manager-users', function () {
                $('#leadsAssignToUserModal').modal('show');
                $('#existingAssignedUser').val(existingAssignedUserId);
            });
        }

        // Function to assign leads to a user
        function assignLeadsToUser(ele) {
            swal.fire({
                title: "Want to Assign?",
                text: "Please ensure and then confirm!",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, assign it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then(function (e) {
                if (e.value === true) {
                    let userId = $(ele).data("user");
                    let companyName = $('#companyName').val();
                    let leadsType = $('#assignLeadsType').val();
                    let sourceId = $('#sourceId').val();
                    let assignedUserId = $('#existingAssignedUser').val();

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('assign.lead') }}',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "sourceId": sourceId,
                            "userId": userId,
                            "companyName": companyName,
                            "leadsType": leadsType,
                            "assignedUserId": assignedUserId,
                        },
                        beforeSend: function () {
                            $(".loader-ajax").show();
                        },
                        success: function (results) {
                            $(".loader-ajax").hide();
                            if (results.status === true) {
                                swal.fire({
                                    toast: true,
                                    icon: 'success',
                                    title: results.message,
                                    animation: false,
                                    position: 'top-right',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                });

                                $('#leadsAssignToUserModal').modal('hide');
                            } else {
                                swal.fire({
                                    toast: true,
                                    icon: 'error',
                                    title: results.message,
                                    animation: false,
                                    position: 'top-right',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                });
                            }
                        }
                    });
                } else {
                    e.dismiss;
                }
            });
        }

        function closeleadassignmodal() {
            $('#leadsAssignToUserModal').modal('hide');
            $('#totalLeadsModal').removeClass('dimmed');

        }
        function leadassignedmodal() {
            $('#leadsAssignedUsers').modal('hide');
            $('#totalLeadsModal').removeClass('dimmed');

        }

        function updatestatus(id) {
    $.ajax({
        type: 'POST',
        url: "{{ route('statusUpdate') }}",
        data: {
            _token: "{{ csrf_token() }}",
            source_id: id,
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Updated',
                    text: response.message || 'Status updated successfully',
                    confirmButtonText:"Ok",
                    // timer: 2000,
                    showConfirmButton: true
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: response.message || 'Unable to update status'
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.'
            });
        }
    });
}


        function assignmanager(id) {
            $('#campaign_id_new').val(id);
            $('#exampleModal').modal('show');
        }

        function closemodal() {
            $('#exampleModal').modal('hide');
            $('#totalLeadsModal').modal('hide');
            $('#employeeleads').modal('hide');

        }

        function savemanager() {
            var campaign_id = $('#campaign_id_new').val();
            var manager_id = $('#manager').val();
            $.ajax({
                type: 'POST',
                url: "{{ route('assignManager') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "campaign_id": campaign_id,
                    "manager_id": manager_id
                },
                success: function (response) {
                    alert('Manager Assigned');
                    location.reload(true);
                }
            });
        }

        $(document).on("click", ".source-item", function () {

            let sourceId = $(this).data("source-id");
            $('#employeeleads').modal('show');
            $.ajax({
                url: leadscountUrl,  // Global variable passed from Blade
                method: "GET",
                data: { source_id: sourceId },
                success: function (response) {
                    $("#employeeleadsdetails").html(response.html);
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: ", error);
                }
            });
        });

        function transfermodal(id, name) {
            $('#error_company_select').css('display', 'none');
            $('#error_source_select').css('display', 'none');


            $('#transferleadmodal').modal('show');

            // Show loading spinner while content loads
            let loaderHtml = '<div class="d-flex justify-content-center">' +
                '<div class="spinner-border" role="status">' +
                '<span class="sr-only">Loading...</span>' +
                '</div>' +
                '</div>';
            $('#transferleadbody').html(loaderHtml);

            // Load dynamic content
            var sourceleadtransfer = "{{ url('source-lead-transfer') }}";

            $.ajax({
                url: sourceleadtransfer + '/' + id,  // Global variable passed from Blade
                method: "GET",
                dataType: "json",
                success: function (response) {
                    // $('#transferleadbody').html('');

                    $('#transferleadbody').html(response.html);
                    $('#source_div').html(response.sourcediv);


                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: ", error);
                }
            });
            // $('#transferleadbody').load('/source-lead-transfer/' + id, function () {
            // });
            $('#sourceoldId').val(id);

        }


        function transferleads() {
            var new_source_id = $('#sourcelistid').val();
            var old_source_id = $('#sourceoldId').val();
            let selectedValues = [];
            var is_valid = 1;
            $('input[name="test[]"]:checked').each(function () {
                selectedValues.push($(this).val());
            });
            if (selectedValues.length === 0) {
                $('#error_company_select').css('display', 'block');
                var is_valid = 0;
                // Stop further processing
            }
            if (new_source_id == '') {
                $('#error_source_select').css('display', 'block');
                var is_valid = 0;
            }
            if (is_valid == 1) {

                $.ajax({
                    url: transferleadsurl,  // Global variable passed from Blade
                    method: "POST",
                    data: { leads: selectedValues, new_source_id, old_source_id },
                    dataType: "json",
                    success: function (response) {
                        if (response.status == 200) {
                            alert(response.message);
                            location.reload(true);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error: ", error);
                    }
                });
            }
        }

        function closemodaltransfer() {
            $('#transferleadmodal').modal('hide');

        }


    </script>

<script>
    // When child modal opens → dim parent modal
    $('#leadsAssignedUsers').on('shown.bs.modal', function () {
        $('#employeeleads').addClass('dimmed');
    });

    // When child modal closes → restore parent modal
    $('#leadsAssignedUsers').on('hidden.bs.modal', function () {
        $('#employeeleads').removeClass('dimmed');
    });
</script>


@endsection