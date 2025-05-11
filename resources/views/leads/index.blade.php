@extends('layouts.admin')

@push('styles')
    <!-- Additional CSS -->
@endpush

@section('content')
    {{-- {{ dd(request()->route('source_id'))}} --}}
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Leads</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline-info">
                    <div class="card-header">
                        <h4 class="m-b-0 text-white">Leads</h4>
                    </div>
                    <div class="card-body align-right">
                        <a type="button" href="{{ route('manager.create') }}"
                            class="btn btn-success addButton addLead"><span class="material-symbols-outlined">
                                person_add
                            </span>
                        </a>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="leads-table">
                                <thead>
                                    <tr>
                                        <th>Campaign Name</th>
                                        <th class="campain_name">Sub-Campaign Name</th>
                                        <th>Organization</th>
                                        <th>Prospect Name</th>
                                        <th>Email ID</th>
                                        <th>LinkedIn</th>
                                        <th>Employee Assigned</th>
                                        <th>Manager Assigned</th>
                                        <th>Designation</th>
                                        <th>Created On</th>
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
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const source_id = '{{ request()->route('source_id') ? request()->route('source_id') : null }}'
            $('#spinner-overlay').show(); // Show full-page spinner
            var table = $('#leads-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: '{{ route('source.leads.data') }}',
                    type: 'GET',
                    data: function(d) {
                        d.source_id = source_id;
                    }
                },
                pageLength: 10,
                columns: [{
                        data: 'source.source_name',
                        name: 'source.source_name'
                    },
                    {
                        data: 'source.description',
                        name: 'source.description'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'prospect_first_name' + 'prospect_last_name',
                        render: function(data, type, row) {
                            // Concatenate 'prospect_first_name' and 'prospect_last_name'
                            return row.prospect_first_name + ' ' + row.prospect_last_name;
                        }
                    },
                    {
                        data: 'prospect_email',
                        name: 'prospect_email'
                    },
                    {
                        data: 'linkedin_address',
                        name: 'linkedin_address'
                    },
                    {
                        data: 'asign_to',
                        name: 'asign_to'
                    },
                    {
                        data: 'asign_to_manager',
                        name: 'asign_to_manager'
                    },
                    {
                        data: 'designation',
                        name: 'designation'
                    },
                    {
                        data: 'created_on',
                        name: 'created_on'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
                    tippy('.addLead', {
                        content: 'Add Lead',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.invalidLinkedin', {
                        content: 'Invalid LinkedIn URL',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.validLinkedin', {
                        content: 'LinkedIn URL',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.validLinkedin', {
                        content: 'LinkedIn URL',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.viewNotes', {
                        content: 'View All Notes',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.viewLead', {
                        content: 'View Lead',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.noLHS', {
                        content: 'No report to Download',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.downloadLHS', {
                        content: 'Download LHS',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.downloadMOM', {
                        content: 'Download MOM',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.pending', {
                        content: 'Pending',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.failed', {
                        content: 'Failed',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.in-progress', {
                        content: 'In Progress',
                        placement: 'top',
                        arrow: true,
                        animation: 'scale'
                    });
                    tippy('.closed', {
                        content: 'Closed',
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
                    tippy('.deleteLead', {
                        content: 'Delete Lead',
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
                        }
                    });
                }
            });

        });
    </script>
@endpush
