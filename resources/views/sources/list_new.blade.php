@extends('layouts.admin')
{{-- @push('head-style') --}}
<style>
    .message-box {
        padding: 15px 20px;
        margin: 15px 0;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 500;
        color: #fff;
        background-color: red;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    /* --- New Card Button Styles --- */
    .stat-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-card.active-card {
        border-color: #192e62 !important;
        background-color: #f8f9ff !important;
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(25, 46, 98, 0.2);
    }

    /* ------------------------------ */

    table.employee-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-family: Arial, sans-serif;
    }

    .employee-table th,
    .employee-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .employee-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .employee-table tr:nth-child(even) {
        background-color: #fafafa;
    }

    .employee-table tr:hover {
        background-color: #f1f1f1;
    }

    .employee-table input[type="checkbox"] {
        transform: scale(1.2);
        cursor: pointer;
    }

    .view_emp {
        cursor: pointer;
        color: blue;
    }

    .menu-title.active {
        background-color: transparent;
        font-weight: bold;
    }

    .graph tbody tr.odd td:last-child {
        display: flex;
    }

    /*-15-08-2026-*/
.graph.campaignslist table tr td:first-child {
    display: flex;
    flex-wrap: wrap;
}
.graph.campaignslist table tr td:first-child a {
    margin-bottom: 0px !important;
    padding-right: 0;
    padding-top: 0;
    padding-bottom: 0;
}
.graph.campaignslist table tr td:first-child a:hover {
    background: none;
}
.graph.campaignslist table tr td:first-child a {
    margin-bottom: 0px !important;
    padding-right: 0;
}
.graph.campaignslist tbody tr td:last-child {
    justify-content: center;
}
.graph.campaignslist table tr td {
    text-align: left !important;
}
.graph.campaignslist thead.thead-main tr th {
    text-align: left !important;
}
.graph.campaignslist thead.thead-main tr th:nth-child(6) {
    text-align: center !important;
}
.graph.campaignslist table tr td:nth-child(6) {
    text-align: center !important;
}
.graph.campaignslist table tr td:nth-child(5) {
    text-align: center !important;
}
.graph.campaignslist tr td:nth-child(6) button {
    border: none;
    padding: 2px 10px;
}
.graph.campaignslist table tr td:first-child a:first-child {
    padding-left: 0;
}
div#leadsAssignToUserModal tr td a {
    color: #fff!important;
    background: #5fbc01;
    padding: 5px 10px;
    margin-bottom: 0;
}
/*-15-08-2026-*/

</style>
{{-- @endpush --}}

@section('content')
    <?php
    $defaultLeadFieldsArray = Config::get('constants.default_lead_fields');
    $customLeadFieldsArray = Config::get('constants.custom_lead_fields');
    $url = url(''); ?>

    <div class="main-right">
        <div class="right-side submanager">
            <div class="row">
                <div class="row align-items-center mb-3">
                    <div class="col-md-8">
                        <h2 class="mb-0">Campaign Listing</h2>
                    </div>

                    <div class="col-md-4 text-end">
                        @if(Auth::user()->is_admin == null)
                            <button type="button" class="btn return-btn"
                                onclick="window.history.back() || (window.location.href='{{ route('dashboard') }}');">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                        @else
                            <button type="button" class="btn return-btn"
                                onclick="window.history.back() || (window.location.href='{{ route('managerdashboard') }}');">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                        @endif

                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card filter-card active-card" data-filter="total">
                        <div class="card-header">
                            <h4>Total</h4>
                            <div class="card-icon">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{$totalCampaigns}}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card filter-card" data-filter="active">
                        <div class="card-header">
                            <h4>Active</h4>
                            <div class="card-icon acti">
                                <i class="fa-regular fa-user"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{$active}}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card filter-card" data-filter="inactive">
                        <div class="card-header">
                            <h4>Inactive</h4>
                            <div class="card-icon inactive">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>{{$inactive}}</h2>
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

                                    <th style="text-align:center">Actions</th>
                                    <th width="200" style="text-align:center">Campaign</th>
                                    <th width="200" style="text-align:center">Sub Campaign</th>
                                    <th width="50" style="text-align:center">Manager</th>
                                    <th width="50" style="text-align:center">Leads</th>
                                    <th width="200" style="text-align:center">Transfer</th>

                                    <!-- <th width="50" style="text-align:center">Distribution</th> -->

                                    <th style="text-align:center">Created At</th>
                                    <th style="text-align:center">Updated At</th>
                                    <th width="50" style="text-align:center">Status</th>

                                    <!--<th>Total Amount</th>-->
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
    <div class="modal fade" id="leadsAssignedUsers" tabindex="-1" role="dialog" aria-labelledby="leadsAssignedUsersLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">

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
                    <select id="manager" style="width: 100%;max-width:100% !important">

                        @if(isset($managers) && !empty($managers))
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}">
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        @endif
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
            var currentStatus = 'total';
            var table = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                searching: true,
                pageLength: 100,
                ordering: true,
                ajax: {
                    url: '{{ route('campaigns_list_ajax_pagination') }}',
                    type: 'GET',
                    data: function (d) {
                        d.status_filter = currentStatus;
                    }
                },
                columns: [
                    { data: 'action', name: 'action', orderable: false },
                    { data: 'source_name_new', name: 'source_name' },
                    { data: 'description', name: 'description' },
                    { data: 'manager_name', name: 'manager_name', orderable: false },
                    { data: 'total_leads_new', name: 'total_leads' },

                    { data: 'transfer', name: 'transfer', orderable: false },
                    // { data: 'company_distribution', name: 'company_distribution', orderable: false },

                    { data: 'created_at_new', name: 'created_at', orderable: true },
                    { data: 'updated_at_new', name: 'updated_at', orderable: true },
                    { data: 'status', name: 'status', orderable: false }

                ],


                initComplete: function () {
                    $('#spinner-overlay').hide();

                    // Add placeholder to the default DataTable search bar
                    $('.dataTables_filter input[type="search"]')
                        .attr('placeholder', 'Search Name,SubCampaign')
                        .css('width', '250px'); // optional styling

                    // --- Card Click Handler ---
                    $('.filter-card').on('click', function () {
                        // 1. UI Update
                        $('.filter-card').removeClass('active-card');
                        $(this).addClass('active-card');

                        // 2. Update status and reload Table
                        currentStatus = $(this).data('filter');
                        table.draw();
                    });

                    // Trigger "Total" card by default on load (though it's visually marked in HTML)
                    // $('.filter-card[data-filter="total"]').trigger('click');
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
                            content: this.checked ? 'Inactive Campaign' : 'Active Campaign',
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
            $('#totalLeadsModalBody').load('/crm2/source-lead/' + id, function () {
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
                            confirmButtonText: "Ok",
                            // timer: 2000,
                            showConfirmButton: true
                        });

                        // Dynamically update the count cards
                        $('.filter-card[data-filter="total"] .card-body h2').text(response.total);
                        $('.filter-card[data-filter="active"] .card-body h2').text(response.active);
                        $('.filter-card[data-filter="inactive"] .card-body h2').text(response.inactive);

                        // Redraw table dynamically
                        $('#employee-table').DataTable().draw(false);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: response.message || 'Unable to update status'
                        });
                        // Revert checkbox state by redrawing table
                        $('#employee-table').DataTable().draw(false);
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                    // Revert checkbox state by redrawing table
                    $('#employee-table').DataTable().draw(false);
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
                    toastr.success('Manager Assigned');
                    setTimeout(function () {
                        location.reload(true);
                    }, 1000);
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
                            toastr.success(response.message);
                            setTimeout(function () {
                                location.reload(true);
                            }, 1000);
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