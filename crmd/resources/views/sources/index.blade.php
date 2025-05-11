@extends('layouts.admin')

@push('styles')
    <!-- Additional CSS -->
@endpush

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
                    <div class="card-header">
                        <h4 class="m-b-0 text-white">Campaigns</h4>
                    </div>
                    <div class="card-body align-right">
                        <a type="button" href="{{-- route('sources.create') --}}" class="btn btn-success addButton"> + Add
                            Campaign</a>
                        <table class="table table-striped table-hover" id="sources-table">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Sub Campaign</th>
                                    <th>Total Leads</th>
                                    <th>Manager</th>
                                    <th>Change Status</th>
                                    <th>Created On</th>
                                    <th>Modified On</th>
                                    <th>Actions</th>
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
        $(document).ready(function() {
            $('#spinner-overlay').show(); // Show full-page spinner
            var table = $('#sources-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route('sources.data') }}',
                pageLength: 3,
                columns: [{
                        data: 'source_name',
                        name: 'source_name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'total_leads',
                        name: 'total_leads'
                    },
                    {
                        data: 'manager_name',
                        name: 'manager_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function() {
                    // Initialize Switchery for each checkbox
                    $('.switchery').each(function() {
                        if (!$(this).data('switchery')) {
                            new Switchery(this, {
                                color: '#192e62',
                                secondaryColor: '#f9f9f9',
                                jackColor: '#d3da44',
                                size: 'small'
                            });
                        }
                    });
                    tippy('.leadsAsignTo', {
                        placement: 'right-start',
                        arrow: true,
                        trigger: 'click',
                        animation: 'scale',
                        allowHTML: true,
                        interactive: true,
                        onShow(instance) {
                            // Fetch the content when the tooltip is about to show
                            fetchLeadAssignContent(instance);
                        }
                    });
                    tippy('.assignedLead', {
                        content: 'Assigned',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.notAssignedLead', {
                        content: 'Click to assign',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.viewLeads', {
                        content: 'View Leads',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.editLeads', {
                        content: 'Edit Leads',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.deleteLeads', {
                        content: 'Delete Leads',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                }
            });

            // Show spinner overlay on processing start
            table.on('preXhr.dt', function(e, settings, data) {
                $('#spinner-overlay').show(); // Show full-page spinner
            });

            // Hide spinner overlay when data is loaded
            table.on('xhr.dt', function(e, settings, json, xhr) {
                $('#spinner-overlay').hide(); // Hide full-page spinner
            });

            document.addEventListener('change', function(event) {
                if (event.target.matches('.switchery')) {

                    var switchElement = event.target;

                    // Accessing the checked state
                    var originalState = switchElement.checked;

                    // Accessing custom data attributes
                    var switchId = switchElement.dataset.sid;
                    var newState = !originalState; // Toggle the state

                    Swal.fire({
                        title: 'Are you sure?',
                        text: newState ? 'Change campaign status to Activate.' :
                            'Change campaign status to Deactivate.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, proceed!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Fetch the content when the tooltip is about to show
                            updateSourceStatus(originalState, switchId);
                        } else {

                            switchElement.checked = originalState;
                            // Trigger a change event to update Switchery
                            var event = document.createEvent('HTMLEvents');
                            event.initEvent('change', true, true);
                            switchElement.dispatchEvent(event);
                            // switchElement.dispatchEvent(event);

                            // Refresh or reinitialize Switchery
                            // var switchery = switchElement.__switchery;
                            // console.log(switchery);
                        }
                    });
                }
            });

        });
    </script>

    <script>
        // Function to fetch content via AJAX
        function fetchLeadAssignContent(instance) {
            // Get the target element
            const targetElement = instance.reference;

            // Get the value from data attribute
            const sourceId = targetElement.getAttribute('data-sid');
            // Simulate an AJAX request
            $.ajax({
                url: '{{ route('sources.leads.assign_to') }}',
                method: 'POST',
                data: {
                    source_id: sourceId,
                },
                success: function(data) {
                    if (data.length > 0) {
                        // Use map to create a new array with each value doubled
                        var content = data.map(function(assignTo) {
                            return `<h5>${assignTo.user_name} - ${assignTo.total_asign_to}</h5>`;
                        });
                        instance.setContent(content.join(''));
                    } else {
                        instance.setContent('<p>Lead is not assign to any one.</p>');
                    }
                },
                error: function() {
                    // Handle any errors
                    instance.setContent('<p>Error loading content.</p>');
                }
            });
        }

        // Function to fetch content via AJAX
        function updateSourceStatus(status, sourceId) {
            // Simulate an AJAX request
            $.ajax({
                url: '{{ route('sources.updateStatus') }}',
                method: 'POST',
                data: {
                    source_id: sourceId,
                    status: status,
                },
                success: function(res) {
                    // Assume response.message contains the dynamic content
                    var message = res.message || 'Record updated successfully'; // Fallback message
                    if (res.success) {
                        toastr.success(res.message);
                    } else {
                        toastr.error('Something went wrong.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred: ' + xhr.responseText);
                }
            });
        }
    </script>
@endpush
