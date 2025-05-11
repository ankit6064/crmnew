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
</style>
{{-- @endpush --}}

@section('content')
<?php
$defaultLeadFieldsArray = Config::get('constants.default_lead_fields');
$customLeadFieldsArray = Config::get('constants.custom_lead_fields');
$url = url(''); ?>
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Dashboard</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Campaigns</li>
        </ol>
    </div>
    <div>
        <!--<button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10"><i class="ti-settings text-white"></i></button>--->
    </div>
</div>

<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">

            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {!! Session::get('success') !!}
                </div>
            @elseif (Session::has('error'))
                <div class="alert alert-danger" role="alert">
                    {{ Session::get('error') }}
                </div>
            @endif

            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white">Campaigns</h4>

                </div>

                <div id="myModal" class="modal fade in " tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Add Lable</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <form id="ajaxform">

                                    <meta name="csrf-token" content="{{ csrf_token() }}" />

                                    <div class="alert alert-danger print-error-msg" style="display:none">
                                        <ul></ul>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-12">Date</label>
                                        <div class="col-md-12">
                                            <input type="date" class="form-control" placeholder="Date" name="date">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-12">Amount</label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" placeholder="Amount" name="amount">
                                        </div>
                                    </div>


                                    <input type="hidden" id="source_id" name="source_id">
                                    </from>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info waves-effect save-data"
                                    data-dismiss="modal">Save</button>
                                <button type="button" class="btn btn-default waves-effect"
                                    data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <div class="card-body">
                    <!--<h4 class="card-title">Data Export </h4>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           <h6 class="card-subtitle">Copy, CSV, PDF & Print</h6>-->
                    <a type="button" href="{{ route('sources.create') }}" class="btn btn-success addButton"> + Add
                        Campaign</a>
                    <div class="table-responsive m-t-40" id="table_data">
                        <table id="sources" class="display nowrap table table-hover table-striped table-bordered"
                            cellspacing="0" width="100%">
                            <thead class="heading-custom">
                                <tr>

                                    <th width="200" style="text-align:center">Campaign</th>
                                    <th width="200" style="text-align:center">Sub-Campaign Name</th>
                                    <th width="50" style="text-align:center">Total Leads</th>
                                    <th width="50" style="text-align:center">Company Distribution</th>

                                    <th style="text-align:center">Created On </th>

                                    <!--<th>Total Amount</th>-->
                                    <th style="text-align:center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
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
                    <span aria-hidden="true">&times;</span>
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
</div>

<!-- Modal to show user list to assign leads -->
<div class="modal fade" id="leadsAssignToUserModal" tabindex="-1" role="dialog" aria-labelledby="leadsAssignToUserLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leadsAssignToUserLabel">Users List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal to show users list that are currently assigned -->
<div class="modal fade" id="leadsAssignedUsers" tabindex="-1" role="dialog" aria-labelledby="leadsAssignedUsersLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leadsAssignedUsersLabel">Select from assigned users</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<!-- Button trigger modal -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#spinner-overlay').show(); // Hide spinner after table is fully initialized

        $('#sources').DataTable({
            processing: false,
            serverSide: true,
            pageLength: 10,
            ajax: {
                url: '{{ route('campaigns_list_ajax_pagination') }}', // URL to the server-side data
                type: 'GET',
            },
            columns: [
                { data: 'source_name', name: 'source_name' },  // Campaign Name from source_name field in the query
                { data: 'description', name: 'description' },
                { data: 'total_leads', name: 'total_leads' },
                { data: 'company_distribution', name: 'company_distribution' },  // Sub-Campaign Name from description field
                { data: 'created_at', name: 'created_at' }, // Created On (created_at) column
                { data: 'action', name: 'action' } // Action column with 'Edit' link
            ],
            initComplete: function () {
                $('#spinner-overlay').hide(); // Hide spinner after table is fully initialized
            },
            preDrawCallback: function () {
                $('#spinner-overlay').show(); // Show spinner before table redraw
            },

            drawCallback: function () {
                $('#spinner-overlay').hide(); // Ensure spinner is hidden after each redraw
            }

        });
        // Bind processing event to show and hide the spinner
        $('#sources').on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $('#spinner-overlay').show(); // Show spinner when processing starts
            } else {
                $('#spinner-overlay').hide(); // Hide spinner when processing ends
            }
        });

    });


    function clickmodal(id) {
        // Open the modal using jQuery
        $('#totalLeadsModal').modal('show');

        // Show the loader while content loads
        let loaderHtml = '<div class="d-flex justify-content-center">' +
            '<div class="spinner-border" role="status">' +
            '<span class="sr-only">Loading...</span>' +
            '</div>' +
            '</div>';
        $('#totalLeadsModalBody').html(loaderHtml);

        // Dynamically load content into the modal body
        $('#totalLeadsModalBody').load('/source-lead/' + id, function () {
            $('#sourceId').val(id); // Set the source ID in the hidden input field
        });
    }

    // Reset the modal content when it’s hidden
    $('#totalLeadsModal').on('hidden.bs.modal', function () {
        let loaderHtml = '<div class="d-flex justify-content-center">' +
            '<div class="spinner-border" role="status">' +
            '<span class="sr-only">Loading...</span>' +
            '</div>' +
            '</div>';
        $('#totalLeadsModalBody').html(loaderHtml); // Reset to loader when modal is hidden
    });

    function closemodal(){
        $('#totalLeadsModal').modal('hide');

    }


</script>


@endsection