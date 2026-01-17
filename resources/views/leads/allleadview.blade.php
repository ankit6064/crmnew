@extends('layouts.admin')
@section('content')

    @php 


        if (isset($_GET["status"])) {

            if (isset($_GET['status']) && '1' == $_GET['status']) {

                $title = "Pending Leads";
            } else if ($_GET['status'] == 3) {

                $title = "Closed Leads";
            } else if ($_GET['status'] == 2) {

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
            <tr> <!-- <th>Sr. No</th> -->
                                    <th>Campaign Name</th>
                                    <th>Company Name</th>
                                    <th>Prospect Name</th> <!-- <th>LinkedIn</th> -->
                                    <th>Time Zone</th>
                                    <th>Designation</th>
                                    <th>Phone No.</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="action_th" style="width: auto">Action</th>
                                </tr>
            </thead>
          </table>
        </div>
      </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        var table; // Declare table globally

        $(document).ready(function () {
            $('#spinner-overlay').show(); // Show spinner when processing starts

            // Initialize DataTable
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
                order: [[6, 'desc']], // Default sort by Date column
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

            // Listen for DataTable processing events
            $('#example23').on('processing.dt', function (e, settings, processing) {
                if (processing) {
                    $('#spinner-overlay').show(); // Show spinner when processing starts
                } else {
                    $('#spinner-overlay').hide(); // Hide spinner when processing ends
                }
            });
        });

        // Function to reload DataTable
        function search() {
            table.ajax.reload(); // Reload DataTable with new data
        }


    </script>
@endsection