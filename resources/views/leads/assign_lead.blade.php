@extends('layouts.admin')

@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}" />


    <div class="main-right addsubmanager assignlead">

        <div class="right-side add-sub">
            <div class="graph">
                <h2>Assign Leads</h2>

                <form>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Select Campaigns</label>
                            <select id="source_id" class="form-control">
                                <option value="">Select Campaign</option>
                                @foreach($sources as $src)
                                    <option value="{{ $src['id']}}" {{ (isset($selectedSource) && $selectedSource == $src['id']) ? 'selected' : '' }}>{{ $src['source_name'] }} ({{ $src['description'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                <div class="table">
                    <div class="table-container add_table"></div>
                </div>
            </div>

            <form class="assignleadform" style="display:none">
                <div class="assign-block-container"></div>
            </form>
            <div class="graph assigned_table_graph" style="display: none;">


                <div class="table">
                    <div class="table-container assigned_table"></div>
                </div>
            </div>

        </div>
    </div>

    <form id="Reassignedform">
        <div id="RevertModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Reassigned</h4>
                        <button type="button" id="modelclose" class="close" data-dismiss="modal" aria-hidden="true"
                            onclick="closerevertmodal();">×</button>
                    </div>
                    <div class="modal-body">

                        <div class="NoResponseData">
                            <div class="form-group" id="employe" name="employe">
                                <label class="control-label">Select Employee</label>
                                <input type="hidden" id="source_id_new" name="camp_id" value="">
                                <input type="hidden" id="previous_user_id_new" name="previous_user_id" value="">
                                <input type="hidden" id="total_leads_new" name="total_leads" value="">
                                <select class="form-control ReassignedCustomer" name="ReassignedCustomer">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employeesd)
                                        <option value="{{ $employeesd['id'] }}">{{ $employeesd['name'] }}</option>
                                    @endforeach
                                </select>

                                <div class="alert alert-danger print-error-msg" style="display:none;margin-top: 10px;">
                                    <ul class="custom_text"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="lead_id_quick_note" name="lead_id_quick_note">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"
                            onclick="closerevertmodal();">Close</button>
                        {{-- <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button> --}}
                        <button id="save-data-reassigned" type="button"
                            class="btn btn-info waves-effect waves-light ">Reassigned</button>
                    </div>
                </div>
            </div>
        </div>
    </form>



    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    <script>
        $(document).ready(function () {

            // ================= LOAD CAMPAIGN DATA =================
            function getCampaignData() {
                let camp_id = $("#source_id").val();
                if (!camp_id) return;

                $.ajax({
                    type: "GET",
                    url: "{{ route('leads.campname') }}",
                    data: { camp_id },
                    success: function (res) {

                        $(".add_table").html(res.headerTable);
                        $('.assignleadform').css('display', '');
                        $(".assign-block-container").html(res.assignBlock);
                        $('.assigned_table_graph').css('display', '');
                        $(".assigned_table").html(res.assignedTable);
                    }
                });
            }

            $(document).on("change", "#source_id", getCampaignData);

            // Load campaign data on page load if pre-selected
            getCampaignData();

            // ================= TOGGLE EDIT COUNT =================
            $(document).on("change", "#edit_count_chk", function () {
                let isChecked = $(this).is(":checked");
                let assignCountInput = $("#assign_count");
                assignCountInput.prop("readonly", !isChecked);
                if (!isChecked) {
                    // Reset to max unassigned leads count
                    assignCountInput.val(assignCountInput.data("max"));
                    $(".error_msg").text("");
                }
            });

            // ================= ASSIGN LEADS =================
            $(document).on("click", "#assignLeadBtn", function (e) {
                e.preventDefault();

                $(".error_msg").text("");

                let leadsStr = $("#assign_count").val();
                let emp_id = $("#employee_id").val();
                let camp_id = $("#source_id").val();

                if (!leadsStr || !emp_id) {
                    $(".error_msg").text("Please fill all fields");
                    return;
                }

                let leads = parseInt(leadsStr, 10);
                let maxLeads = parseInt($("#assign_count").data("max"), 10);

                if (isNaN(leads) || leads <= 0) {
                    $(".error_msg").text("Please enter a valid lead count greater than 0");
                    return;
                }

                if (maxLeads === 0) {
                    $(".error_msg").text("No unassigned leads available to assign");
                    return;
                }

                if (leads > maxLeads) {
                    $(".error_msg").text("Cannot assign more than " + maxLeads + " unassigned leads");
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('leads.assingParticalurleads') }}",
                    data: {
                        assign_leads: leads,
                        emp_id,
                        cmp_id: camp_id,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function () {
                        getCampaignData();
                    }
                });
            });

            // ================= WITHDRAW =================
            $(document).on("click", ".Withdraw", function () {
                if (!confirm("Are you sure?")) return;

                $.ajax({
                    type: "GET",
                    url: "{{ route('leads.unassigned') }}",
                    data: {
                        camp_id: $(this).data("camp"),
                        user_id: $(this).data("emp")
                    },
                    success: getCampaignData
                });
            });

            // ================= REASSIGN =================
            $(document).on("click", ".Reassign", function () {

                $("#source_id_new").val($(this).data("camp"));
                $("#previous_user_id_new").val($(this).data("assign"));
                $("#total_leads_new").val($(this).data("count"));

                $("#RevertModel").modal("show");
            });



            $("#save-data-reassigned").click(function () {
                let new_emp = $(".ReassignedCustomer").val();

                if (!new_emp) {
                    $('.print-error-msg').show();
                    $('.custom_text').html("<li>Please select employee</li>");
                    return;
                }

                $.ajax({
                    type: "GET",
                    url: "{{ route('leads.reassigned') }}",
                    data: {
                        camp_id: $("#source_id_new").val(),
                        user_id: $("#previous_user_id_new").val(),
                        new_id: new_emp,
                        leads: $("#total_leads_new").val()
                    },
                    success: function () {
                        $("#RevertModel").modal("hide");
                        getCampaignData();
                    }
                });
            });

        });
    </script>

    <script>
        function closerevertmodal() {
            $('#RevertModel').modal('hide');
        }
    </script>

@endsection