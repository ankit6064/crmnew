@extends('layouts.admin')
@section('content')



    <div class="main-right">
        <div class="right-side submanager">
            <h2>Myself Logs</h2>

            <!-- Filter Section -->
            <div class="employee-filterss employeefilter2">
                <div class="filter-container">
                    <select id="source_id" class="filter-select">
                        <option value="">Select Campaign</option>
                        @if(isset($sources) && !empty($sources))
                            @foreach($sources as $sourcelisting)
                                <option value="{{ $sourcelisting->id }}">
                                    {{ $sourcelisting->source_name . '-' . $sourcelisting->description }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <select id="type" class="filter-select">
                        <option value="">Select Type</option>
                        <option value="1">Note Added</option>
                        <option value="5">Lead Added</option>
                        <option value="7">Account Created</option>
                        <option value="8">Manage Login</option>
                        <option value="9">Account Active/Deactive</option>
                        <option value="10">Assigned Submanager</option>
                        <option value="11">Employee Deleted</option>
                        <option value="12">Company Transferred</option>
                        <option value="13">Campaign Active/Deactive</option>
                        <option value="14">New Campaign Added</option>
                        <option value="15">Lead Approved/Disapproved</option>
                        <option value="16">Employee Assigned to Submanager</option>
                        <option value="17">LHS Sent</option>
                        <option value="18">LHS Reminder Sent</option>
                        <option value="19">Lead Confirmed</option>
                    </select>

                    <input type="text" id="daterange" class="filter-date" placeholder="Select Date Range">
                    <button type="button" id="filterLogs" class="filter-btn filter-blue">Filter</button>
                    <button type="button" id="reset" class="filter-btn filter-red">Reset</button>
                </div>
            </div>

            <!-- Messages -->
            @if (session('success'))
                <div class="message-box success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="message-box error">{{ session('error') }}</div>
            @endif



            <!-- Logs Table -->
            <div class="graph campaignslist logstable myself-logs">
                <div class="table">
                    <div class="table-container">
                        <table class="custom-table" id="employee-table">
                            <thead class="thead-main">
                                <tr>
                                    <th>S.No</th>
                                    <th>Campaign Name</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Created On</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="pagination" id="pagination-container"></div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(document).ready(function () {
            var table = $('#employee-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: true,
                ajax: {
                    url: "{{ route('managerlogs') }}",
                    data: function (d) {
                        d.sourceid = $('#source_id').val();
                        d.date = $('#daterange').val();
                        d.type = $('#type').val();
                    }
                },
                columns: [
                    { data: null, name: 'sno', orderable: false, searchable: false },
                    { data: 'campaign', name: 'sources.source_name', orderable: false, searchable: false },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            if (!data) return '--';
                            const cleanText = $('<div>').html(data).text();
                            if (cleanText.length > 80) {
                                return `
                                <span class="short-text">${cleanText.slice(0, 80)}...</span>
                                <a href="javascript:void(0);" class="read-more">Read more</a>
                                <span class="full-text d-none">${data}</span>
                            `;
                            }
                            return data;
                        }
                    },
                    { data: 'type', name: 'type', orderable: false },
                    { data: 'created_at', name: 'created_at', orderable: true },
                ],
                order: [[4, 'desc']],
                drawCallback: function (settings) {
                    var api = this.api();
                    api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                        cell.innerHTML = api.page.info().start + i + 1;
                    });
                }
            });

            $('#filterLogs').on('click', function () {
                table.ajax.reload();
            });

            $('#reset').on('click', function () {
                $('#source_id, #type, #daterange').val('');
                table.ajax.reload();
            });

            // Read more / less toggle
            $('#employeelogs').on('click', '.read-more', function () {
                const $cell = $(this).closest('td');
                const fullText = $cell.find('.full-text').html();
                $cell.html(`${fullText} <a href="javascript:void(0);" class="read-less">Show less</a>`);
            });
            $('#employeelogs').on('click', '.read-less', function () {
                const $cell = $(this).closest('td');
                const fullText = $cell.text();
                const shortText = fullText.slice(0, 80);
                $cell.html(`
                <span class="short-text">${shortText}...</span>
                <a href="javascript:void(0);" class="read-more">Read more</a>
                <span class="full-text d-none">${fullText}</span>
            `);
            });
        });

        // Daterange Picker
        $(function () {
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                opens: 'left',
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                }
            });
            $('#daterange').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
            });
            $('#daterange').on('cancel.daterangepicker', function () {
                $(this).val('');
            });
        });
    </script>
@endpush