@extends('layouts.admin')

@section('content')
<div class="main-right">
    <div class="right-side submanager">
        <div class="row align-items-center mb-3">
            <div class="col-md-8">
                <h2 class="mb-0">Notifications</h2>
            </div>
        </div>

        <div class="graph campaignslist logstable">
            <div class="table">
                <div class="table-container">
                    <table class="table table-striped table-hover">
                        <thead class="thead-main">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr class="{{ $notification->is_read ? '' : 'fw-bold bg-light' }}">
                                    <td>{{ $notification->created_at->format('d M, Y H:i') }}</td>
                                    <td>
                                        @if($notification->type == 'confirmation_overdue')
                                            <span class="badge bg-danger">LHS Overdue</span>
                                        @elseif($notification->type == 'callback_overdue')
                                            <span class="badge bg-primary">Callback Overdue</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Reminder Overdue</span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->message }}</td>
                                    <td>
                                        {!! $notification->is_read 
                                            ? '<span class="text-success">Read</span>' 
                                            : '<span class="text-danger">Unread</span>' !!}
                                    </td>
                                    <td>
                                        @if(!$notification->is_read)
                                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-info">Mark Read</button>
                                            </form>
                                        @endif
                                        @if($notification->lead_id)
                                            <a href="{{ url('/leads', [$notification->lead_id]) }}" class="btn btn-sm btn-secondary mt-1" target="_blank">View Lead</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No notifications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
