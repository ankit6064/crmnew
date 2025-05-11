@extends('layouts.admin')
@section('content')
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
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-outline-info">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <!-- Manager Name and Employees -->
                    <h4 class="m-b-0 text-white">Campaigns</h4>
                    <!-- Back Button on the Right -->
              <!--       <a href="{{ url()->previous() }}" class="btn btn-light d-flex align-items-center">
                        <span class="material-symbols-outlined mr-2">
                            arrow_back
                        </span>
                        Back
                    </a>
 -->                </div>
                <div class="card-body align-right">

                    <table class="table table-striped table-hover" id="employee-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Campaign</th>
                                <th>Sub-Campaign</th>
                                <th>Leads</th>
                                <th>Last Login</th>
                                <th>Comments since last session</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#spinner-overlay').show(); // Show full-page spinner
            var table = $('#employee-table').DataTable({
                processing: false,
                serverSide: true,
                searching:false,
                ajax: '{{ route('home_datatable') }}',
                pageLength: 10,
                columns: [
                    // {data: 'assign_to_employee', name: 'assign_to_employee'},
                    { data: 'name', name: 'name' },
                    { data: 'camp_source_name', name: 'camp_source_name' },
                    { data: 'camp_description', name: 'camp_description', searchable: false, orderable: false },
                    { data: 'lead_assigned', name: 'lead_assigned', searchable: false, orderable: false },
                    { data: 'last_login', name: 'last_login' },
                    { data: 'notes_count', name: 'notes_count', searchable: false, orderable: false },
                ],
                drawCallback: function () {
                    // Initialize Switchery for each checkbox
                    $('.switchery').each(function () {
                        if (!$(this).data('switchery')) {
                            new Switchery(this, {
                                color: '#192e62',
                                secondaryColor: '#f9f9f9',
                                jackColor: '#d3da44',
                                size: 'small'
                            });
                        }
                    });

                }
            });

            // Show spinner overlay on processing start
            table.on('preXhr.dt', function (e, settings, data) {
                $('#spinner-overlay').show(); // Show full-page spinner
            });

            // Hide spinner overlay when data is loaded
            table.on('xhr.dt', function (e, settings, json, xhr) {
                $('#spinner-overlay').hide(); // Hide full-page spinner
            });
        });
    </script>
@endpush