@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush


@section('content')

    <input type="hidden" id="source_id" value="{{ $id }}">

    <div class="main-right">
        <div class="right-side meeting-scheduled-leads">

            @php
                $status = request('meeting_status');
                $pageTitle = 'Active Meetings';
                if ($status == 'Done') $pageTitle = 'Meeting Happened';
                elseif ($status == 'Failed') $pageTitle = 'Meeting Not Happened';
                elseif ($status == 'Rescheduled') $pageTitle = 'Meeting Rescheduled';
                elseif ($status == 'Pending') $pageTitle = 'Pending Meetings';
            @endphp
            <div class="row align-items-center mb-3">
                <div class="col-md-8">
                    <h2 id="page-title" class="mb-0">{{ $pageTitle }}</h2>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn return-btn"
                        onclick="window.history.back() || (window.location.href='{{ Auth::user()->is_admin == null ? route('dashboard') : (Auth::user()->is_admin == 1 ? route('employeedashboard') : route('managerdashboard')) }}');">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </button>
                </div>
            </div>

            <div class="graph campaignslist">

                <!-- Filters -->
                <style>
                    .filter-row {
                        display: flex;
                        gap: 15px;
                        flex-wrap: wrap;
                        margin-bottom: 20px;
                    }

                    .filter-select {
                        flex: 1 1 45%;
                        min-width: 250px;
                        height: 45px;
                        border-radius: 8px;
                        border: 1px solid #ddd;
                        padding: 0 15px;
                        font-size: 14px;
                        color: #333;
                        background-color: #fff;
                    }

                    .filter-select:focus {
                        border-color: #192e62;
                        outline: none;
                        box-shadow: 0 0 5px rgba(25, 46, 98, 0.2);
                    }

                    /* Specific styling for meeting scheduled leads table */
                    .meeting-scheduled-leads .table-container {
                        overflow-x: auto;
                    }

                    .meeting-scheduled-leads td {
                        white-space: nowrap;
                    }

                    .meeting-scheduled-leads tr td:nth-child(3) {
                        text-align: left;
                        display: flex;
                        align-items: center;
                    }

                    .meeting-scheduled-leads tr td:nth-child(3) a {
                        color: #212529;
                        font-size: 14px;
                        margin: 0;
                        padding: 0;
                    }

                    .meeting-scheduled-leads tr td:nth-child(3) a:hover {
                        background: transparent;
                    }

                    .meeting-scheduled-leads tr td:nth-child(3) i.fa-brands.fa-linkedin {
                        color: #0A66C2;
                        font-size: 20px;
                        margin-left: 5px;
                    }

                    /* Override any sticky last child from global css */
                    .meeting-scheduled-leads tbody tr td:last-child {
                        position: static !important;
                        background: transparent !important;
                    }
                </style>
                <div class="filter-row">
                    <select id="campaign_name" class="filter-select">
                        <option value="">Campaign</option>
                        @foreach($sourceNames as $s)
                            <option value="{{ $s['source_name'] }}">{{ $s['source_name'] }}</option>
                        @endforeach
                    </select>

                    <select id="company_s" class="filter-select">
                        <option value="">Company</option>
                        @foreach($comapnyName as $c)
                            <option value="{{ $c['company_name'] }}">{{ $c['company_name'] }}</option>
                        @endforeach
                    </select>

                    <input type="text" id="invitation_daterange" class="filter-select" placeholder="Invitation Date"
                        title="Invitation Date" readonly>

                    <select id="meeting_status_filter" class="filter-select">
                        <option value="">Meeting Status</option>
                        <option value="Done" {{ request('meeting_status') == 'Done' ? 'selected' : '' }}>Meeting Happened
                        </option>
                        <option value="Failed" {{ request('meeting_status') == 'Failed' ? 'selected' : '' }}>Meeting Not
                            Happened</option>
                        <option value="Rescheduled" {{ request('meeting_status') == 'Rescheduled' ? 'selected' : '' }}>Meeting
                            Rescheduled</option>
                        <option value="Pending" {{ request('meeting_status') == 'Pending' ? 'selected' : '' }}>Pending
                        </option>
                    </select>

                    <button id="reset_filters" class="btn btn-secondary"
                        style="height: 45px; border-radius: 8px; padding: 0 20px; font-size: 14px;">Reset Filters</button>
                </div>




                <div class="table">
                    <div class="table-container">
                        <table id="employee-table">
                            <thead class="thead-main">
                                <tr>
                                    <th>Campaign Name</th>
                                    <th>Company Name</th>
                                    <th>Prospect Name</th>
                                    <th>Email Id</th>
                                    <th>Phone Number</th>
                                    <th>Invitation Date</th>
                                    <th>Meeting Status</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Meeting Status Modal -->
    <div id="meeting-status-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <meta name="csrf-token" content="{{ csrf_token() }}" />
                <div class="modal-header">
                    <h4 class="modal-title">Update Meeting Status</h4>
                    <button type="button" class="close" data-dismiss="modal"
                        onclick="$('#meeting-status-modal').modal('hide')">×</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="meeting_lead_id">
                    <div class="form-group mb-3">
                        <label>Status</label>
                        <select id="meeting_status_input" class="form-control" onchange="toggleMeetingFields()">
                            <option value="">Select Status</option>
                            <option value="Done">Meeting Happened</option>
                            <option value="Failed">Meeting Not Happened</option>
                            <option value="Rescheduled">Meeting Rescheduled</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="failed_reason_div" style="display:none;">
                        <label>Reason for Failure</label>
                        <textarea id="meeting_failed_reason" class="form-control" rows="3"></textarea>
                    </div>

                    <div id="reschedule_div" style="display:none;">
                        <div class="form-group mb-3">
                            <label>New Date</label>
                            <input type="date" id="reschedule_date" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label>New Time</label>
                            <input type="time" id="reschedule_time" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal"
                        onclick="$('#meeting-status-modal').modal('hide')">Close</button>
                    <button class="btn btn-info" id="save-meeting-status">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Leads Modal -->
    @if(count($pendingLeads) > 0 && !request()->get('meeting_status'))
        <div id="pending-leads-modal" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Pending Meeting Status Actions</h4>
                        <button type="button" class="close" data-dismiss="modal"
                            onclick="$('#pending-leads-modal').modal('hide')">×</button>
                    </div>
                    <div class="modal-body">
                        <p>The following leads have meeting dates that have passed or are today. Please update their status.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-main">
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Prospect Name</th>
                                        <th>Invitation Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingLeads as $lead)
                                        <tr>
                                            <td>{{ $lead->company_name }}</td>
                                            <td>{{ $lead->prospect_first_name }} {{ $lead->prospect_last_name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($lead->invitation_date)->format('d M, Y h:i A') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary"
                                                    onclick="updateMeetingFromModal({{ $lead->id }})">Update Status</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal"
                            onclick="$('#pending-leads-modal').modal('hide')">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#spinner-overlay').show();
            var url = "{{ url('leads/meeting_scheduled') }}";

            $('#invitation_daterange').daterangepicker({
                autoUpdateInput: false,
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });

            $('#invitation_daterange').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                $('#spinner-overlay').show();
                table.ajax.reload();
            });

            $('#invitation_daterange').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                $('#spinner-overlay').show();
                table.ajax.reload();
            });

            var table = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                searching: true,
                ordering: false,
                searchDelay: 500, // smoother search
                ajax: {
                    url: url,
                    data: function (d) {
                        d.campaign_name = $('#campaign_name').val();
                        d.cName = $('#company_s').val();
                        d.meeting_status = $('#meeting_status_filter').val();
                        let dr = $('#invitation_daterange').val();
                        if (dr) {
                            let dates = dr.split(' - ');
                            d.invitation_from = dates[0];
                            d.invitation_to = dates[1];
                        } else {
                            d.invitation_from = '';
                            d.invitation_to = '';
                        }
                    }
                },
                columns: [
                    { data: 'source_name', name: 'sources.source_name', searchable: false },
                    { data: 'company_name' },
                    { data: 'prospect_first_name_new', name: 'prospect_first_name' },
                    { data: 'prospect_email' },
                    { data: 'contact_number_1' },
                    { data: 'invitation_date' },
                    { data: 'meeting_status' },
                    {
                        data: 'comments',
                        render: function (data, type, row) {
                            if (!data || data === 'N/A') return 'N/A';
                            if (data.length > 40) {
                                return `
                                                                                    <span class="short-text">${data.slice(0, 40)}...</span>
                                                                                    <span class="read-more badge" style="cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px; font-weight: normal; font-size: 12px; padding: 5px 8px;">+ show more</span>
                                                                                    <span class="full-text" style="display: none;">${data}</span>
                                                                                `;
                            }
                            return data;
                        }
                    }
                ],
                initComplete: function () {
                    $('div.dataTables_filter input')
                        .attr('placeholder', 'Search...')
                        .css({ 'width': '250px' });
                    $('#spinner-overlay').hide();
                }
            });

            $('#employee-table').on('click', '.read-more', function () {
                const $cell = $(this).closest('td');
                const fullText = $cell.find('.full-text').text();
                $cell.html(`
                                                                    <span class="full-text-display">${fullText}</span>
                                                                    <span class="read-less badge" style="cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px; font-weight: normal; font-size: 12px; padding: 5px 8px;">- show less</span>
                                                                    <span class="full-text" style="display: none;">${fullText}</span>
                                                                `);
            });

            $('#employee-table').on('click', '.read-less', function () {
                const $cell = $(this).closest('td');
                const fullText = $cell.find('.full-text').text();
                $cell.html(`
                                                                    <span class="short-text">${fullText.slice(0, 40)}...</span>
                                                                    <span class="read-more badge" style="cursor:pointer; background-color:#192e62; color:#fff; margin-left:5px; font-weight: normal; font-size: 12px; padding: 5px 8px;">+ show more</span>
                                                                    <span class="full-text" style="display: none;">${fullText}</span>
                                                                `);
            });

            $('#campaign_name, #company_s, #meeting_status_filter').on('change', function () {
                if ($(this).attr('id') === 'meeting_status_filter') {
                    var val = $(this).val();
                    var title = 'Active Meetings';
                    if (val == 'Done') title = 'Meeting Happened';
                    else if (val == 'Failed') title = 'Meeting Not Happened';
                    else if (val == 'Rescheduled') title = 'Meeting Rescheduled';
                    else if (val == 'Pending') title = 'Pending Meetings';
                    $('#page-title').text(title);
                }
                $('#spinner-overlay').show();
                table.ajax.reload();
            });

            $('#reset_filters').on('click', function () {
                $('#campaign_name, #company_s, #meeting_status_filter, #invitation_daterange').val('');
                $('#page-title').text('Active Meetings');
                $('#spinner-overlay').show();
                table.ajax.reload();
            });

            $('#reset_filters').click(function () {
                $('#campaign_name').val('');
                $('#company_s').val('');
                $('#invitation_daterange').val('');
                $('div.dataTables_filter input').val('');
                table.search('');
                $('#spinner-overlay').show();
                table.ajax.reload();
            });

            // Show loader before every AJAX request
            table.on('preXhr.dt', function () {
                $('#spinner-overlay').show();
            });

            // Hide loader after AJAX response
            table.on('xhr.dt', function () {
                $('#spinner-overlay').hide();
            });
            @if(count($pendingLeads) > 0)
                $('#pending-leads-modal').modal('show');
            @endif
                                                });

        function updateMeetingFromModal(id) {
            $('#pending-leads-modal').modal('hide');
            updateMeeting(id);
        }

        function updateMeeting(id) {
            $('#meeting_lead_id').val(id);
            $('#meeting_status_input').val('');
            $('#meeting_failed_reason').val('');
            $('#reschedule_date').val('');
            $('#reschedule_time').val('');
            toggleMeetingFields();
            $('#meeting-status-modal').modal('show');
        }

        function toggleMeetingFields() {
            var val = $('#meeting_status_input').val();
            if (val == 'Failed') {
                $('#failed_reason_div').show();
                $('#reschedule_div').hide();
            } else if (val == 'Rescheduled') {
                $('#failed_reason_div').hide();
                $('#reschedule_div').show();
            } else {
                $('#failed_reason_div').hide();
                $('#reschedule_div').hide();
            }
        }

        $('#save-meeting-status').click(function () {
            var id = $('#meeting_lead_id').val();
            var status = $('#meeting_status_input').val();
            var reason = $('#meeting_failed_reason').val();
            var res_date = $('#reschedule_date').val();
            var res_time = $('#reschedule_time').val();
            var _token = $('meta[name="csrf-token"]').attr('content');

            if (!status) {
                toastr.error('Please select status'); return;
            }
            if (status == 'Failed' && !reason) {
                toastr.error('Please enter reason'); return;
            }
            if (status == 'Rescheduled' && (!res_date || !res_time)) {
                toastr.error('Please select date and time'); return;
            }

            $.post("{{ url('leads/update_meeting_status') }}", {
                id: id,
                status: status,
                reason: reason,
                reschedule_date: res_date,
                reschedule_time: res_time,
                _token: _token
            }, function (res) {
                if (res.success) {
                    toastr.success(res.success);
                    $('#meeting-status-modal').modal('hide');
                    $('#employee-table').DataTable().ajax.reload(null, false);
                } else {
                    toastr.error('Error updating status');
                }
            });
        });

    </script>
@endpush