@extends('layouts.admin')
@section('content')
<style>
        .graph tbody tr.odd td:last-child {
    display: table-cell !important;
    gap: 11px;
    align-items: center;
}

</style>
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
    <div class="row align-items-center mb-3">
                <div class="col-md-8">
                    <h2 class="mb-0">Leads Listing</h2>
                </div>

                <div class="col-md-4 text-end">
                    <button type="button" class="btn return-btn"
                        onclick="window.history.back() || (window.location.href='/managerdashboard');">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </button>
                </div>
            </div>

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


            <div class="modal fade" id="numberModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #192e62; color: #fff; padding: 10px 15px;">
                <h6 class="modal-title">Contact Numbers</h6>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 1;" onclick="closemodal();">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="numberRow" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; align-items: center;">
                    </div>
            </div>
        </div>
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
                { data: 'contact_number_1', name: 'contact_number_1', orderable: false, searchable: false },


                { data: 'created_at', name: 'created_at' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],

            order: [[6, 'desc']],

            initComplete: function () {
                table.on('init.dt', function () {
                $('div.dataTables_filter input').attr('placeholder', 'Search by company,prospect,designation').css({ 'width': '250px' });
                });
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

    function showAllNumbers(numbers) {
    let rowHtml = '';

        rowHtml += '<p>' + numbers + '</p>';
    

    $('#numberRow').html(rowHtml);
    $('#numberModal').modal('show');
}



function closemodal(){
    $('#numberModal').modal('hide');

}
</script>

@endsection
