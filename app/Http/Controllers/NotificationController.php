<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->all());

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        Notification::where('id', $id)->where('user_id', Auth::id())->update(['is_read' => true]);
        return back()->with('success', 'Notification marked as read');
    }

    public function getUnreadCount()
    {
        // Dynamically create overdue meeting notifications for the current user
        $now = now();
        $overdueMeetings = \App\Models\Lead::where('asign_to_manager', Auth::id())
            ->whereNotNull('invitation_date')
            ->where('invitation_date', '<=', $now)
            ->where(function ($q) {
                $q->whereNull('meeting_status')
                    ->orWhere('meeting_status', '')
                    ->orWhere('meeting_status', 'Pending');
            })
            ->get();

        foreach ($overdueMeetings as $lead) {
            $exists = Notification::where('lead_id', $lead->id)
                ->where('type', 'meeting_status_overdue')
                ->exists();

            if (!$exists) {
                Notification::create([
                    'lead_id' => $lead->id,
                    'user_id' => Auth::id(),
                    'type' => 'meeting_status_overdue',
                    'message' => "Meeting status update is overdue for lead: {$lead->prospect_first_name} {$lead->prospect_last_name} (Meeting was: " . \Carbon\Carbon::parse($lead->invitation_date)->format('d M, Y h:i A') . ")",
                ]);
            }
        }

        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
