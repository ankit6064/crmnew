@extends('layouts.admin')
@section('content')
    <style>
        .message-box {
            padding: 15px 20px;
            margin: 15px 0;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            background-color: #007bff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .message-box.success {
            background-color: #28a745;
        }

        .message-box.error {
            background-color: #dc3545;
        }

        .d-none {
            display: none !important;
        }

        .toggle-description {
            color: #007bff;
            cursor: pointer;
            margin-left: 5px;
        }
    </style>

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Myself Logs</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline-info">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0 text-white">Myself Logs</h4>
                    </div>
                    <div class="card-body">
                        <div class="filter-box">
                            <select name="filter_status" id="source_id" style="width: 200px;">
                                <option value="">Select Campaign</option>
                                @if(isset($sources) && !empty($sources))
                                    @foreach($sources as $sourcelisting)
                                        <option value="{{ $sourcelisting->id }}">
                                            {{ $sourcelisting->source_name . '-' . $sourcelisting->description }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>

                            <select name="type" id="type">
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
                                <option value="17">Employee Removed from Submanager</option>
                            </select>

                            <input type="text" name="daterange" id="daterange" class="form-control" />
                            <button type="button" id="filterLogs" style="background-color:#192e62 !important;border:1px solid #192e62">Filter</button>
                            <button type="button" id="reset" style="background-color:red">Reset</button>
                        </div>

                        @if (session('success'))
                            <div class="message-box success">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="message-box error">
                                {{ session('error') }}
                            </div>
                        @endif

                        <table id="employeelogs" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th>Campaign</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Created On</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>
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
        var table = $('#employeelogs').DataTable({
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
                },
                error: function (xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                }
            },
            columns: [
                { data: null, name: 'sno', orderable: false, searchable: false },
                { data: 'campaign', name: 'sources.source_name', orderable: true, searchable: false },
                {
                    data: 'description',
                    name: 'description',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (!data) return '--';

                        const cleanText = $('<div>').html(data).text();
                        if (cleanText.length > 80) {
                            return `
                                <span class="short-text" title="${cleanText}">${cleanText.slice(0, 80)}...</span>
                                <a href="javascript:void(0);" class="read-more">Read more</a>
                                <span class="full-text d-none">${data}</span>
                            `;
                        }
                        return data;
                    }
                },
                { data: 'type', name: 'type', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at', orderable: true, searchable: false },
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
            $('#source_id').val('');
            $('#daterange').val('');
            $('#type').val('');
            table.ajax.reload();
        });

        // Read more / Read less toggle
        $('#employeelogs').on('click', '.read-more', function () {
            const $row = $(this).closest('td');
            const fullText = $row.find('.full-text').html();
            $row.html(`${fullText} <a href="javascript:void(0);" class="read-less">Show less</a>`);
        });

        $('#employeelogs').on('click', '.read-less', function () {
            const $row = $(this).closest('td');
            const fullText = $row.text();
            const shortText = fullText.slice(0, 80);
            $row.html(`
                <span class="short-text">${shortText}...</span>
                <a href="javascript:void(0);" class="read-more">Read more</a>
                <span class="full-text d-none">${fullText}</span>
            `);
        });
    });

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

            $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endpush