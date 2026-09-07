@extends('layouts.admin')

<style>
    .graph.campaignslist.logstable.mb-3.Notifications-form select#filter-type, select#filter-status, input#filter-date {
    min-width: 100% !important;
    box-shadow: 0px -1px 9px #dddddd70;
}
.graph.campaignslist.logstable.mb-3.Notifications-form button {
    color: #fff;
    font-weight: 500;
}
.graph.campaignslist.logstable.mb-3.Notifications-form {
    padding: 20px !important;
}
.graph.campaignslist.logstable.mb-3.Notifications-form select#filter-type, select#filter-status, input#filter-date {
    padding: 10px !important;
    font-size: 12px;
}
.graph.campaignslist.logstable.mb-3.Notifications-form button {
    padding: 11px !important;
        font-size: 12px;
}
.graph.campaignslist.logstable.mb-3.Notifications-form form#filter-form {
    padding: 0px !important;
    margin-bottom: 0;
}
.graph.campaignslist.logstable.notification-table tbody tr td:nth-child(2) span {
    padding: 10px;
    width: 100%;
    background: #0d3a6b !important;
    max-width: 120px;
    font-weight: 500;
    color: #fff !important;
    font-size: 11px;
}
.graph.campaignslist.logstable.notification-table button.btn.btn-sm.btn-info {
    color: #fff;
    font-weight: 500;
    font-size: 11px;
}
.graph.campaignslist.logstable.notification-table table tr td:last-child a {
    font-size: 14px;
}
.graph.campaignslist.logstable.notification-table tr td:last-child a.btn.btn-sm.btn-secondary.mt-1 {
    margin-top: 0px !important;
    font-weight: 500;
    font-size: 11px;
    padding: 8px 20px !important;
}
.graph.campaignslist.logstable.mb-3.Notifications-form button.btn.btn-info.flex-grow-1 {
    background: #0a2b63;
}
.graph.campaignslist.logstable.mb-3.Notifications-form button#reset-btn {
    background: #78a5ee;
}
span.text-success {
    color: #78a5ee !important;
    font-weight: 500;
}
span.text-danger {
    color: #0d3a6b !important;
    font-weight: 500;
}
.graph.campaignslist.logstable.mb-3.Notifications-form .col-md-3:last-child .d-flex.gap-2 {
    max-width: 242px;
    gap: 0px !important;
}
.graph.campaignslist.logstable.notification-table table tr td:last-child a {
    background: #0d3a6b;
    color: #fff !important;
}
</style>

@section('content')
    <div class="main-right">
        <div class="right-side submanager">
            <div class="row align-items-center mb-3">
                <div class="col-md-8">
                    <h2 class="mb-0">Notifications</h2>
                </div>
            </div>

            <!-- Filters -->
            <div class="graph campaignslist logstable mb-3 Notifications-form">
                <form id="filter-form" action="{{ route('notifications.filter') }}" method="POST" class="p-3">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Notification Type</label>
                            <select name="type" id="filter-type" class="form-control">
                                <option value="">All Types</option>
                                <option value="meeting_status_overdue">Meeting Status Overdue</option>
                                <option value="confirmation_overdue">LHS Overdue</option>
                                <option value="callback_overdue">Callback Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" id="filter-status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="read">Read</option>
                                <option value="unread">Unread</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Date</label>
                            <input type="date" name="date" id="filter-date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-info flex-grow-1">Filter</button>
                                <button type="button" id="reset-btn" class="btn btn-secondary flex-grow-1">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="graph campaignslist logstable notification-table">
                <div class="table">
                    <div class="table-container" id="table-container">
                        @include('notifications.table_partial')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function fetchNotifications(page = 1) {
            let type = $('#filter-type').val();
            let status = $('#filter-status').val();
            let date = $('#filter-date').val();
            
            $.ajax({
                url: "{{ route('notifications.filter') }}",
                type: "POST",
                data: {
                    type: type,
                    status: status,
                    date: date,
                    page: page,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#table-container').html(response.html);
                },
                error: function(xhr) {
                    console.error("Error filtering notifications:", xhr.responseText);
                }
            });
        }

        // Filter Form submit
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            fetchNotifications(1);
        });

        // Reset button
        $('#reset-btn').on('click', function() {
            $('#filter-type').val('');
            $('#filter-status').val('');
            $('#filter-date').val('');
            fetchNotifications(1);
        });

        // Pagination links click
        $(document).on('click', '.ajax-pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            let page = 1;
            if (url) {
                let match = url.match(/page=(\d+)/);
                if (match) {
                    page = match[1];
                }
            }
            fetchNotifications(page);
        });

        // AJAX Mark Read submit
        $(document).on('submit', '.mark-read-form', function(e) {
            e.preventDefault();
            let form = $(this);
            let url = form.attr('action');
            
            $.ajax({
                url: url,
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    // Update navigation unread count dynamically if visible
                    if (typeof updateNavNotificationCount === 'function') {
                        updateNavNotificationCount();
                    }
                    
                    // Reload current page of notifications
                    let currentPage = 1;
                    let activePageElem = $('.ajax-pagination .active span, .ajax-pagination .active a');
                    if (activePageElem.length) {
                        let text = activePageElem.text().trim();
                        if (!isNaN(text) && text !== '') {
                            currentPage = parseInt(text);
                        }
                    }
                    fetchNotifications(currentPage);
                },
                error: function(xhr) {
                    console.error("Error marking notification as read:", xhr.responseText);
                }
            });
        });
    });
</script>
@endpush