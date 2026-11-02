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

    /* --- Manage DNC Modal Styling --- */
    #dncCompaniesTable thead th {
        background-color: #071e38 !important;
        color: #ffffff !important;
        border-color: #1e3a5f !important;
        box-shadow: none !important;
        font-weight: 600;
        vertical-align: middle;
        padding-top: 10px;
        padding-bottom: 10px;
    }
    #dncCompaniesTable tbody td {
        vertical-align: middle;
    }
    .dnc-filter-pill {
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        outline: none;
    }
    .dnc-filter-pill:hover {
        background: #f8fafc;
    }
    .dnc-filter-pill.filter-all.active {
        background: #071e38 !important;
        border-color: #071e38 !important;
        color: #ffffff !important;
    }
    .dnc-filter-pill.filter-dnc {
        border-color: #fca5a5;
        color: #dc2626;
        background: #ffffff;
    }
    .dnc-filter-pill.filter-dnc:hover {
        background: #fef2f2;
    }
    .dnc-filter-pill.filter-dnc.active {
        background: #dc2626 !important;
        border-color: #dc2626 !important;
        color: #ffffff !important;
    }
    .dnc-filter-pill.filter-active {
        border-color: #86efac;
        color: #16a34a;
        background: #ffffff;
    }
    .dnc-filter-pill.filter-active:hover {
        background: #f0fdf4;
    }
    .dnc-filter-pill.filter-active.active {
        background: #16a34a !important;
        border-color: #16a34a !important;
        color: #ffffff !important;
    }
    .dnc-filter-pill .pill-badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: 700;
        background: #e2e8f0;
        color: #334155;
    }
    .dnc-filter-pill.active .pill-badge {
        background: rgba(255, 255, 255, 0.28) !important;
        color: #ffffff !important;
    }

    /* Highlight DNC rows in Company Listing Modals (Total Leads, Transfer, etc.) */
    tr.dnc-highlighted-row {
        background-color: #fee2e2 !important;
        border-left: 4px solid #dc2626 !important;
    }
    tr.dnc-highlighted-row > td {
        background-color: #fee2e2 !important;
        color: #991b1b !important;
        border-color: #fca5a5 !important;
    }
    tr.dnc-highlighted-row:hover > td {
        background-color: #fecaca !important;
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
        color: #fff !important;
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
                                    <th width="50" style="text-align:center">Leads</th>
                                    <th width="200" style="text-align:center">Transfer</th>
                                    <th width="120" style="text-align:center">Manage DNC</th>

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
                            <span aria-hidden="true">&times;</span>
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
                        <span aria-hidden="true">&times;</span>
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

    <!-- ================= MANAGE DNC MODAL ================= -->
    <div class="modal fade" id="manageDncModal" tabindex="-1" role="dialog" aria-labelledby="manageDncModalLabel"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document" style="max-width: 860px;">
            <div class="modal-content" style="border-radius: 8px; border: none; box-shadow: 0 5px 25px rgba(0,0,0,0.25);">
                <div class="modal-header" style="background-color: #071e38; color: #fff; border-top-left-radius: 8px; border-top-right-radius: 8px; padding: 14px 20px;">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h5 class="modal-title mb-1" id="manageDncModalLabel" style="font-weight: 700; color: #fff; font-size: 17px;">
                                <i class="fa-solid fa-ban text-danger me-2"></i> Manage DNC Companies
                            </h5>
                            <small id="dncCampaignTitle" style="color: #cbd5e1; font-size: 13px; font-weight: 500;"></small>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                            onclick="closeDncModal();" style="opacity: 0.9; font-size: 26px; border: none; background: transparent; cursor: pointer;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="modal-body" style="padding: 16px 20px; background-color: #f8fafc;">
                    <!-- Stat counters -->
                    <div class="row g-2 mb-3" style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <div class="col" style="flex: 1;">
                            <div class="p-2 text-center rounded border bg-white shadow-sm" style="background:#fff; border:1px solid #e2e8f0; border-radius:6px; padding:10px;">
                                <span class="d-block text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700; color:#64748b;">Total Companies</span>
                                <span id="dncStatTotal" style="font-size: 20px; font-weight: 700; color: #192e62;">0</span>
                            </div>
                        </div>
                        <div class="col" style="flex: 1;">
                            <div class="p-2 text-center rounded border bg-white shadow-sm" style="background:#fff; border:1px solid #fecaca; border-radius:6px; padding:10px;">
                                <span class="d-block text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700; color:#dc3545;">In DNC (Excluded)</span>
                                <span id="dncStatDnc" style="font-size: 20px; font-weight: 700; color: #dc3545;">0</span>
                            </div>
                        </div>
                        <div class="col" style="flex: 1;">
                            <div class="p-2 text-center rounded border bg-white shadow-sm" style="background:#fff; border:1px solid #bbf7d0; border-radius:6px; padding:10px;">
                                <span class="d-block text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700; color:#16a34a;">Active Companies</span>
                                <span id="dncStatActive" style="font-size: 20px; font-weight: 700; color: #16a34a;">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Add Company Directly to DNC -->
                    <!-- <div class="card mb-3 border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 15px;">
                        <div class="card-body" style="padding: 10px 14px;">
                            <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">
                                <i class="fa fa-plus-circle text-danger me-1"></i> Add Specific Company to DNC
                            </label>
                            <div class="input-group" style="display: flex;">
                                <input type="text" id="manualDncCompanyName" class="form-control"
                                    placeholder="Enter company name to add to DNC (even if not imported yet)..."
                                    style="border-top-right-radius: 0; border-bottom-right-radius: 0;"
                                    onkeydown="if(event.key === 'Enter'){ event.preventDefault(); addCustomDncCompany(); }">
                                <button type="button" class="btn btn-danger" onclick="addCustomDncCompany()"
                                    style="font-weight: 600; padding: 6px 16px; border-top-left-radius: 0; border-bottom-left-radius: 0; white-space: nowrap;">
                                    <i class="fa fa-ban me-1"></i> Add to DNC
                                </button>
                            </div>
                        </div>
                    </div> -->

                    <!-- Filter bar and bulk actions -->
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 12px;">
                        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px;">
                            <!-- Search input -->
                            <div style="flex: 1 1 220px; min-width: 180px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white" style="border-color: #cbd5e1; border-right: none;">
                                        <i class="fa fa-search text-muted"></i>
                                    </span>
                                    <input type="text" id="dncCompanyFilterInput" class="form-control form-control-sm"
                                        placeholder="Search company in list..." onkeyup="filterDncTable();"
                                        style="border-color: #cbd5e1; border-left: none; font-size: 12px;">
                                </div>
                            </div>

                            <!-- Status filter pills -->
                            <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                                <button type="button" class="dnc-filter-pill filter-all active" id="filterBtnAll" onclick="setDncFilter('all')">
                                    All <span id="pillCountAll" class="pill-badge">0</span>
                                </button>
                                <button type="button" class="dnc-filter-pill filter-dnc" id="filterBtnDnc" onclick="setDncFilter('dnc')">
                                    <i class="fa fa-ban me-1"></i> DNC Only <span id="pillCountDnc" class="pill-badge">0</span>
                                </button>
                                <button type="button" class="dnc-filter-pill filter-active" id="filterBtnActive" onclick="setDncFilter('active')">
                                    <i class="fa fa-check me-1"></i> Active Only <span id="pillCountActive" class="pill-badge">0</span>
                                </button>
                            </div>

                            <!-- Bulk actions -->
                            <div style="display: flex; gap: 6px; align-items: center;">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="bulkToggleDnc(1)" style="font-size: 11px; font-weight: 600; padding: 5px 12px; border-radius: 4px;" title="Mark checked companies as DNC">
                                    <i class="fa fa-ban me-1"></i> Mark DNC
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="bulkToggleDnc(0)" style="font-size: 11px; font-weight: 600; padding: 5px 12px; border-radius: 4px;" title="Remove checked companies from DNC">
                                    <i class="fa fa-check me-1"></i> Remove DNC
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Companies Table Container -->
                    <div id="dncTableWrapper" style="max-height: 400px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff;">
                        <table class="table table-hover table-striped mb-0" id="dncCompaniesTable" style="width: 100%; font-size: 13px;">
                            <thead style="position: sticky; top: 0; z-index: 2;">
                                <tr>
                                    <th width="40" class="text-center" style="vertical-align: middle;">
                                        <input type="checkbox" id="dncMasterCheckbox" onchange="toggleSelectAllDnc(this)" title="Select all visible">
                                    </th>
                                    <th width="45" class="text-center">#</th>
                                    <th>Company Name</th>
                                    <th width="90" class="text-center">Leads</th>
                                    <th width="110" class="text-center">Status</th>
                                    <th width="140" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="dncCompaniesListBody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 10px 20px; background-color: #f1f5f9; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <small class="text-muted" style="font-size: 12px;">
                        <i class="fa fa-info-circle text-primary me-1"></i> Leads from DNC companies are automatically hidden across all system reports and listings.
                    </small>
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal" onclick="closeDncModal();" style="border-radius: 4px;">Close</button>
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

            // Support starting on a specific page via URL parameter ?page=X
            var urlParams = new URLSearchParams(window.location.search);
            var initialPage = parseInt(urlParams.get('page'), 10);
            if (isNaN(initialPage) || initialPage < 1) {
                initialPage = 1;
            }
            var defaultPageLength = 25;
            var initialDisplayStart = (initialPage - 1) * defaultPageLength;

            var table = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                searching: true,
                pageLength: defaultPageLength,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                displayStart: initialDisplayStart,
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
                    { data: 'total_leads_new', name: 'total_leads' },

                    { data: 'transfer', name: 'transfer', orderable: false },
                    { data: 'manage_dnc', name: 'manage_dnc', orderable: false, searchable: false },
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
                        table.page(0).draw(false);
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

                    // --- Jump to Page Controls & URL Sync ---
                    var pageInfo = table.page.info();
                    var currentPage = pageInfo.page + 1;
                    var totalPages = pageInfo.pages || 1;

                    // Sync URL ?page= parameter so user can copy/share or refresh on that page
                    try {
                        var currentUrl = new URL(window.location.href);
                        if (currentPage > 1) {
                            currentUrl.searchParams.set('page', currentPage);
                        } else {
                            currentUrl.searchParams.delete('page');
                        }
                        window.history.replaceState({}, '', currentUrl.toString());
                    } catch (e) { }

                    // Append Jump to Page input control in pagination container
                    var $paginate = $('#employee-table_paginate');
                    if ($paginate.length) {
                        var $jumpWrap = $paginate.find('.dt-page-jump-wrap');
                        if (!$jumpWrap.length) {
                            var jumpHtml = '<div class="dt-page-jump-wrap" style="display:inline-flex;align-items:center;margin-left:14px;gap:6px;vertical-align:middle;">' +
                                '<span style="font-size:13px;font-weight:500;color:#495057;">Go to page:</span>' +
                                '<input type="number" min="1" max="' + totalPages + '" value="' + currentPage + '" class="form-control form-control-sm dt-page-jump-input" style="width:65px;height:32px;display:inline-block;text-align:center;font-size:13px;padding:2px 6px;border:1px solid #ced4da;border-radius:4px;" placeholder="' + currentPage + '">' +
                                '<span class="dt-page-total-label" style="font-size:12px;color:#6c757d;">of ' + totalPages + '</span>' +
                                '<button type="button" class="btn btn-sm dt-page-jump-btn" style="background-color:#0d3a6b;color:#fff;border:none;height:32px;padding:0 12px;font-size:12px;font-weight:600;border-radius:4px;cursor:pointer;">Go</button>' +
                                '</div>';
                            $paginate.append(jumpHtml);
                        } else {
                            $jumpWrap.find('.dt-page-jump-input').attr('max', totalPages).val(currentPage);
                            $jumpWrap.find('.dt-page-total-label').text('of ' + totalPages);
                        }

                        $paginate.off('click.dtJump', '.dt-page-jump-btn').on('click.dtJump', '.dt-page-jump-btn', function (e) {
                            e.preventDefault();
                            executePageJump();
                        });

                        $paginate.off('keypress.dtJump', '.dt-page-jump-input').on('keypress.dtJump', '.dt-page-jump-input', function (e) {
                            if (e.which === 13) {
                                e.preventDefault();
                                executePageJump();
                            }
                        });

                        function executePageJump() {
                            var inputVal = $paginate.find('.dt-page-jump-input').val();
                            var targetPage = parseInt(inputVal, 10);
                            if (isNaN(targetPage) || targetPage < 1) {
                                targetPage = 1;
                            } else if (targetPage > totalPages) {
                                targetPage = totalPages;
                            }
                            $paginate.find('.dt-page-jump-input').val(targetPage);
                            if (targetPage !== currentPage) {
                                table.page(targetPage - 1).draw('page');
                            }
                        }
                    }

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

        // Get already assigned users for leads (supports multi-company selection)
        function getAlredayAssignedUsers(ele) {
            let clickedCompany = $(ele).data("company");

            // Collect all selected companies from checkboxes
            var selectedCompanies = [];
            $(".company-select-chk:checked").each(function () {
                selectedCompanies.push($(this).data("company"));
            });

            // If no company checkbox is selected, use the clicked company only
            if (selectedCompanies.length === 0) {
                selectedCompanies = [clickedCompany];
            }

            // Collect lead types from ALL selected companies
            var leadsType = [];
            $.each(selectedCompanies, function (i, companyName) {
                $("input[name='" + companyName + "[]']:checked").each(function () {
                    var val = $(this).val();
                    if (leadsType.indexOf(val) === -1) {
                        leadsType.push(val);
                    }
                });
            });

            // If no leads type is selected
            if (leadsType.length == 0) {
                swal.fire("Error!", 'Please select at least one lead type.', "error");
                return false;
            }

            if (selectedCompanies.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('assigned.users') }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "companyNames": selectedCompanies,
                        "companyName": selectedCompanies[0],
                        "sourceId": $('#sourceId').val(),
                    },
                    beforeSend: function () {
                        $(".loader-ajax").show();
                    },
                    success: function (response) {
                        $('#totalLeadsModal').addClass('dimmed');
                        $(".loader-ajax").hide();
                        $('#assignLeadsType').val(leadsType);
                        $('#companyName').val(JSON.stringify(selectedCompanies));
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

        // Function to assign leads to a user (supports multi-company)
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
                    let companyNamesRaw = $('#companyName').val();
                    let companyNames;
                    try {
                        companyNames = JSON.parse(companyNamesRaw);
                    } catch (err) {
                        companyNames = [companyNamesRaw];
                    }
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
                            "companyNames": companyNames,
                            "companyName": companyNames[0],
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

    <!-- ================= MANAGE DNC JAVASCRIPT ================= -->
    <script>
        var dncSourceId = null;
        var dncSourceName = '';
        var dncCompaniesData = [];
        var dncFilteredCompanies = [];
        var dncCurrentFilter = 'all';
        var dncModified = false;

        function manageDncModal(sourceId, sourceName) {
            dncSourceId = sourceId;
            dncSourceName = sourceName;
            dncModified = false;
            dncCompaniesData = [];
            dncFilteredCompanies = [];
            dncCurrentFilter = 'all';

            $('#dncCampaignTitle').text(sourceName);
            $('#manualDncCompanyName').val('');
            $('#dncCompanyFilterInput').val('');
            $('#dncStatTotal').text('0');
            $('#dncStatDnc').text('0');
            $('#dncStatActive').text('0');
            $('#pillCountAll').text('0');
            $('#pillCountDnc').text('0');
            $('#pillCountActive').text('0');
            $('#dncMasterCheckbox').prop('checked', false);

            $('.dnc-filter-pill').removeClass('active');
            $('#filterBtnAll').addClass('active');

            $('#dncCompaniesListBody').html(
                '<tr><td colspan="6" class="text-center py-4 text-muted">' +
                '<div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>' +
                'Loading companies...' +
                '</td></tr>'
            );

            $('#manageDncModal').modal('show');

            $.ajax({
                url: "{{ url('sources/dnc-companies') }}/" + sourceId,
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.status) {
                        dncCompaniesData = res.companies || [];
                        recalcDncStatsAndRender();
                    } else {
                        toastr.error(res.message || 'Failed to load companies');
                    }
                },
                error: function (xhr) {
                    toastr.error('Error loading companies for this campaign');
                }
            });
        }

        function renderDncTable() {
            var search = ($('#dncCompanyFilterInput').val() || '').toLowerCase().trim();

            dncFilteredCompanies = dncCompaniesData.filter(function (item) {
                var name = (item.company_name || '').toLowerCase();
                var matchesSearch = !search || name.indexOf(search) !== -1;
                var matchesStatus = true;
                if (dncCurrentFilter === 'dnc') {
                    matchesStatus = item.is_dnc === 1;
                } else if (dncCurrentFilter === 'active') {
                    matchesStatus = item.is_dnc === 0;
                }
                return matchesSearch && matchesStatus;
            });

            var tbody = $('#dncCompaniesListBody');
            tbody.empty();
            $('#dncMasterCheckbox').prop('checked', false);

            if (dncFilteredCompanies.length === 0) {
                var emptyMsg = 'No companies found matching criteria.';
                if (search) {
                    emptyMsg = 'No companies matching "' + $('<div>').text(search).html() + '" found.';
                } else if (dncCurrentFilter === 'dnc') {
                    emptyMsg = 'No companies are marked as DNC in this campaign.';
                } else if (dncCurrentFilter === 'active') {
                    emptyMsg = 'No active companies found.';
                }
                tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fa fa-info-circle me-1"></i> ' + emptyMsg + '</td></tr>');
                return;
            }

            var rowsHtml = '';
            for (var i = 0; i < dncFilteredCompanies.length; i++) {
                var c = dncFilteredCompanies[i];
                var isDnc = c.is_dnc === 1;
                var safeName = $('<div>').text(c.company_name).html();

                var badge = isDnc
                    ? '<span class="badge" style="background:#dc3545;color:#fff;font-size:11px;padding:3px 8px;border-radius:4px;"><i class="fa fa-ban me-1"></i> DNC</span>'
                    : '<span class="badge" style="background:#16a34a;color:#fff;font-size:11px;padding:3px 8px;border-radius:4px;"><i class="fa fa-check me-1"></i> Active</span>';

                var actionBtn = isDnc
                    ? '<button type="button" class="btn btn-sm" style="background:#fff;border:1px solid #16a34a;color:#16a34a;padding:2px 8px;font-size:11px;font-weight:600;border-radius:4px;" onclick="toggleCompanyDncByIndex(' + i + ', 0)"><i class="fa fa-undo me-1"></i> Remove DNC</button>'
                    : '<button type="button" class="btn btn-sm" style="background:#fff;border:1px solid #dc3545;color:#dc3545;padding:2px 8px;font-size:11px;font-weight:600;border-radius:4px;" onclick="toggleCompanyDncByIndex(' + i + ', 1)"><i class="fa fa-ban me-1"></i> Mark DNC</button>';

                rowsHtml += '<tr style="' + (isDnc ? 'background-color: #fff5f5;' : '') + '">' +
                    '<td class="text-center" style="vertical-align: middle;"><input type="checkbox" class="dnc-row-cb" data-idx="' + i + '"></td>' +
                    '<td class="text-center text-muted" style="vertical-align: middle; font-size: 11px;">' + (i + 1) + '</td>' +
                    '<td style="vertical-align: middle; font-weight: 600; color: #1e293b;">' + safeName + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;"><span class="badge" style="background:#e2e8f0;color:#334155;font-size:11px;padding:3px 7px;">' + (c.total_leads || 0) + '</span></td>' +
                    '<td class="text-center" style="vertical-align: middle;">' + badge + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' + actionBtn + '</td>' +
                    '</tr>';
            }
            tbody.html(rowsHtml);
        }

        function setDncFilter(filter) {
            dncCurrentFilter = filter;
            $('.dnc-filter-pill').removeClass('active');
            if (filter === 'all') {
                $('#filterBtnAll').addClass('active');
            } else if (filter === 'dnc') {
                $('#filterBtnDnc').addClass('active');
            } else if (filter === 'active') {
                $('#filterBtnActive').addClass('active');
            }
            renderDncTable();
        }

        function filterDncTable() {
            renderDncTable();
        }

        function toggleSelectAllDnc(masterCheckbox) {
            $('.dnc-row-cb').prop('checked', masterCheckbox.checked);
        }

        function toggleCompanyDncByIndex(idx, isDnc) {
            if (!dncFilteredCompanies[idx]) return;
            var companyName = dncFilteredCompanies[idx].company_name;
            toggleCompanyDnc(companyName, isDnc);
        }

        function toggleCompanyDnc(companyName, isDnc) {
            if (!companyName || !companyName.trim()) {
                toastr.warning('Company name cannot be empty');
                return;
            }

            $.ajax({
                url: "{{ route('sources.toggle_dnc') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    source_id: dncSourceId,
                    company_name: companyName.trim(),
                    is_dnc: isDnc
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status) {
                        toastr.success(res.message);
                        dncModified = true;

                        var found = false;
                        for (var i = 0; i < dncCompaniesData.length; i++) {
                            if (dncCompaniesData[i].company_name.toLowerCase().trim() === companyName.toLowerCase().trim()) {
                                dncCompaniesData[i].is_dnc = isDnc;
                                found = true;
                                break;
                            }
                        }
                        if (!found && isDnc === 1) {
                            dncCompaniesData.unshift({
                                company_name: companyName.trim(),
                                total_leads: 0,
                                is_dnc: 1
                            });
                        }
                        recalcDncStatsAndRender();
                    } else {
                        toastr.error(res.message || 'Operation failed');
                    }
                },
                error: function (xhr) {
                    toastr.error('Error updating DNC status');
                }
            });
        }

        function bulkToggleDnc(isDnc) {
            var selected = [];
            $('.dnc-row-cb:checked').each(function () {
                var idx = $(this).data('idx');
                if (dncFilteredCompanies[idx]) {
                    selected.push(dncFilteredCompanies[idx].company_name);
                }
            });

            if (selected.length === 0) {
                toastr.warning('Please select at least one company using the checkboxes');
                return;
            }

            var actionText = isDnc === 1 ? 'mark ' + selected.length + ' companies as DNC' : 'remove ' + selected.length + ' companies from DNC';
            if (!confirm('Are you sure you want to ' + actionText + '?')) {
                return;
            }

            $.ajax({
                url: "{{ route('sources.toggle_dnc') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    source_id: dncSourceId,
                    company_names: selected,
                    is_dnc: isDnc
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status) {
                        toastr.success(res.message);
                        dncModified = true;

                        var selectedLower = selected.map(function (s) { return s.toLowerCase().trim(); });
                        for (var i = 0; i < dncCompaniesData.length; i++) {
                            if (selectedLower.indexOf(dncCompaniesData[i].company_name.toLowerCase().trim()) !== -1) {
                                dncCompaniesData[i].is_dnc = isDnc;
                            }
                        }
                        recalcDncStatsAndRender();
                    } else {
                        toastr.error(res.message || 'Bulk operation failed');
                    }
                },
                error: function (xhr) {
                    toastr.error('Error performing bulk DNC update');
                }
            });
        }

        function addCustomDncCompany() {
            var name = ($('#manualDncCompanyName').val() || '').trim();
            if (!name) {
                toastr.warning('Please enter a company name');
                $('#manualDncCompanyName').focus();
                return;
            }
            toggleCompanyDnc(name, 1);
            $('#manualDncCompanyName').val('');
        }

        function recalcDncStatsAndRender() {
            var total = dncCompaniesData.length;
            var dnc = 0;
            for (var i = 0; i < dncCompaniesData.length; i++) {
                if (dncCompaniesData[i].is_dnc === 1) {
                    dnc++;
                }
            }
            var active = total - dnc;
            $('#dncStatTotal').text(total);
            $('#dncStatDnc').text(dnc);
            $('#dncStatActive').text(active);

            $('#pillCountAll').text(total);
            $('#pillCountDnc').text(dnc);
            $('#pillCountActive').text(active);

            renderDncTable();
        }

        function closeDncModal() {
            $('#manageDncModal').modal('hide');
            if (dncModified) {
                dncModified = false;
                $('#employee-table').DataTable().ajax.reload(null, false);
            }
        }

        $('#manageDncModal').on('hidden.bs.modal', function () {
            if (dncModified) {
                dncModified = false;
                $('#employee-table').DataTable().ajax.reload(null, false);
            }
        });
    </script>
@endsection