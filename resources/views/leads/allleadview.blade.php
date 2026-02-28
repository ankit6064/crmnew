@extends('layouts.admin')
@section('content')

@php 
    if (isset($_GET["status"])) {
        if ($_GET['status'] == '1') {
            $title = "Pending Leads";
        } else if ($_GET['status'] == '3') {
            $title = "Closed Leads";
        } else if ($_GET['status'] == '2') {
            $title = "Failed Leads";
        } else {
            $title = "Leads";
        }
    } else {
        $title = "Leads";
    }
@endphp

<div class="main-right">
    <div class="right-side submanager">
        <h2>Leads Listing</h2>

        <div class="graph campaignslist logstable lead-listing">
            <div class="table">
                <div class="table-container">
                    <table class="table table-striped table-hover" id="employee-table">
                        <thead class="thead-main">
                            <tr>
                                <th>Campaign Name</th>
                                <th>Company Name</th>
                                <th>Prospect Name</th>
                                <th>Time Zone</th>
                                <th>Designation</th>
                                <th>Phone No.</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="action_th">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables (make sure already included in your layout or add if missing) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
    .toggle-text {
        cursor: pointer;
        font-size: 12px;
        color: #007bff;
    }
</style>

<script>
    var table;

    $(document).ready(function () {

        $('#spinner-overlay').show();

        table = $('#employee-table').DataTable({
            processing: false,
            serverSide: true,
            ajax: {
                url: "{{ route('allgetsourceslead') }}",
            },
            columns: [
                { data: 'campaign_name', name: 'campaign_name' },
                { data: 'company_name', name: 'company_name' },
                { data: 'prospect_name', name: 'prospect_name', orderable: false, searchable: false },
                { data: 'timezone', name: 'timezone' },
                { data: 'designation', name: 'designation' },

                // ✅ Phone Number Column with Show More / Less
                {
                    data: 'contact_number_1',
                    name: 'contact_number_1',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        if (!data) return '';

                        let maxLength = 8; // how many chars to show
                        let shortText = data.substring(0, maxLength);

                        if (data.length <= maxLength) {
                            return data;
                        }

                        return `
                            <span class="short-text">${shortText}...</span>
                            <span class="full-text d-none">${data}</span>
                            <span class="toggle-text">Show More</span>
                        `;
                    }
                },

                { data: 'created_at', name: 'created_at' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],

            order: [[6, 'desc']],

            initComplete: function () {
                $('#spinner-overlay').hide();
            },
            preDrawCallback: function () {
                $('#spinner-overlay').show();
            },
            drawCallback: function () {
                $('#spinner-overlay').hide();
            }
        });

        // ✅ Toggle Show More / Show Less
        $(document).on('click', '.toggle-text', function () {
            let parent = $(this).parent();

            parent.find('.short-text').toggleClass('d-none');
            parent.find('.full-text').toggleClass('d-none');

            if ($(this).text() === 'Show More') {
                $(this).text('Show Less');
            } else {
                $(this).text('Show More');
            }
        });

        // Spinner handling for DataTables processing
        $('#employee-table').on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $('#spinner-overlay').show();
            } else {
                $('#spinner-overlay').hide();
            }
        });
    });

    function search() {
        table.ajax.reload();
    }
</script>

@endsection
