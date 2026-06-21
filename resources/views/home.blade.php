@extends('layouts.admin')
@section('content')

    <div class="main-right">
        <div class="right-side">
            <div class="row align-items-center mb-4">
                <div class="col-md-12">
                    <h2 class="mb-0">Campaigns</h2>
                </div>
            </div>

            <div class="graph">
                <div class="table">
                    <div class="table-container">
                        <table class="table table-striped table-hover" id="employee-table" cellspacing="0" width="100%">
                            <thead class="thead-main">
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
                searching: true,
                ajax: '{{ route('home_datatable') }}',
                pageLength: 10,
                language: {
                    searchPlaceholder: "Search by name, campaign..."
                },
                columns: [
                    // {data: 'assign_to_employee', name: 'assign_to_employee'},
                    { data: 'name', name: 'name' },
                    { data: 'source_name', name: 'source_name' },
                    { data: 'description', name: 'description', searchable: false, orderable: true },
                    { data: 'lead_assigned', name: 'lead_assigned', searchable: false, orderable: true },
                    { data: 'last_login_new', name: 'last_login' },
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

            table.on('init.dt', function () {
                $('div.dataTables_filter input').css({ 'width': '250px', 'display': 'inline-block' });
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