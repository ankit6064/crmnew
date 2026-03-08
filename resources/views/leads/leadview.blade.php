@extends('layouts.admin')
@section('content')
<style>
        .graph tbody tr.odd td:last-child {
    display: table-cell !important;
    gap: 11px;
    align-items: center;
}
.main-right.view-camp .table .table-container tr td:last-child{
    display: table-cell !important;
    gap: 11px;
    align-items: center; 
}
</style>
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


    <div class="main-right view-camp">
        <div class="right-side submanager">
        <div class="row">
                <div class="row align-items-center mb-3">
                    <div class="col-md-8">
                        <h2 class="mb-0">Leads Listing</h2>

                    </div>

                    <div class="col-md-4 text-end">
                        <button type="button" class="btn return-btn"
                            onclick="window.history.back() || (window.location.href='/sources/getMangerSource');">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </button>
                    </div>
                </div>

            </div>
   
            <div class="employee-filterss">
    <div class="filter-container">

        <form class="form-horizontal form-label-left input_mask" 
              id="assignForm" 
              method="post" 
              action="">
            @csrf

            <!-- Campaign Select -->
            <select name="source_id" id="source_id" class="filter-select">
                <option value="">Select Campaigns</option>
                @foreach($sources as $sources)
                    @if ($source_ids == $sources['id'])
                        <option value="{{ $sources['id'] }}" selected>
                            {{ $sources['source_name'] }} ({{ $sources['description'] }})
                        </option>
                    @else
                        <option value="{{ $sources['id'] }}">
                            {{ $sources['source_name'] }} ({{ $sources['description'] }})
                        </option>
                    @endif
                @endforeach
            </select>

            @if($errors->has('source_id'))
                <div class="alert alert-danger">{{ $errors->first('source_id') }}</div>
            @endif

            <!-- Filter/Search Button -->
            <button type="button" id="submitButton" class="filter-btn filter-blue" onclick="search();">
                Filter
            </button>

            <!-- Import Leads Button (only non admin 2)
            @if(Auth::user()->is_admin != 2)
            <a href="{{ url('/add_leads').'/'.$source_ids }}" 
               class="btn btn-success addButton" 
               style="margin-left: 10px;">
                Import Leads +
            </a>
            @endif -->

        </form>

    </div>
</div>

            <div class="graph campaignslist logstable">

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
                                    <th>Action</th>
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
                    url: "{{ route('getsourceslead') }}",
                    data: function (d) {
                        d.source_id = $('#source_id').val(); // Pass source_id from filter
                    }
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
                table.on('init.dt', function () {
                $('div.dataTables_filter input').attr('placeholder', 'Search by company,prospect,designation').css({ 'width': '250px' });
                });
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