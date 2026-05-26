<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckLhsNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lhs:check-notifications';
    protected $description = 'Check for LHS confirmation and reminder overdue leads and notify users';

    public function handle()
    {
        $now = now();

        // 1. Waiting for confirmation > 48 hours
        $leads48 = \App\Models\Lead::whereNotNull('lhs_sent_at')
            ->whereNull('invitation_date')
            ->where('lhs_sent_at', '<=', $now->copy()->subHours(48))
            ->get();

        foreach ($leads48 as $lead) {
            // 1. Delete associated LhsReport
            \App\Models\LhsReport::where('lead_id', $lead->id)->delete();

            // 2. Delete associated LhsFiles records (and physical files)
            $lhsFiles = \App\Models\LhsFiles::where('lead_id', $lead->id)->get();
            foreach ($lhsFiles as $file) {
                $filePath = storage_path('app/public/' . $file->file_path);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $file->delete();
            }

            // 3. Reset lead columns to make it a fresh lead
            $lead->status = 1; // 1 is Fresh Lead
            $lead->lhs_sent_at = null;
            $lead->lhs_reminder_sent_at = null;
            $lead->invitation_date = null;
            $lead->confirmation_status = null;
            $lead->meeting_status = null;
            $lead->meeting_failed_reason = null;
            $lead->save();

            // 4. Create log entry (auto reverted by system)
            $logs = new \App\Models\Logs();
            $logs->user_id = 0; // 0 for system-automated action
            $logs->type = 21; // unique log type for reverting to fresh
            $logs->reference_id = $lead->id;
            $logs->source_id = $lead->source_id;
            $logs->description = 'Lead automatically reverted to fresh lead (LHS deleted due to 48 hours without confirmation) for lead - ' . $lead->prospect_first_name . ' ' . $lead->prospect_last_name;
            $logs->save();

            // 5. Create notifications for the BDM and Manager
            $message = "Lead: {$lead->prospect_first_name} {$lead->prospect_last_name} has been automatically reverted to a Fresh Lead because it remained pending confirmation for over 48 hours.";
            
            // Notify manager
            if ($lead->asign_to_manager) {
                \App\Models\Notification::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->asign_to_manager,
                    'type' => 'confirmation_overdue',
                    'message' => $message,
                ]);
            }
            
            // Notify assigned employee
            if ($lead->asign_to) {
                \App\Models\Notification::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->asign_to,
                    'type' => 'confirmation_overdue',
                    'message' => $message,
                ]);
            }
        }

        // 2. Reminder overdue > 24 hours
        $leads24 = \App\Models\Lead::whereNotNull('lhs_reminder_sent_at')
            ->whereNull('invitation_date')
            ->where('lhs_reminder_sent_at', '<=', $now->copy()->subHours(24))
            ->get();

        foreach ($leads24 as $lead) {
            $exists = \App\Models\Notification::where('lead_id', $lead->id)
                ->where('type', 'reminder_overdue')
                ->exists();

            if (!$exists) {
                \App\Models\Notification::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->asign_to,
                    'type' => 'reminder_overdue',
                    'message' => "Confirmation is still pending (24h+ after reminder) for lead: {$lead->prospect_first_name} {$lead->prospect_last_name}",
                ]);
            }
        }

        // 3. Pending Callbacks
        $pendingCallbacks = \App\Models\CallbackLeads::join('leads', 'callback_leads.lead_id', '=', 'leads.id')
            ->where('callback_leads.status', 0) // 0 is pending
            ->whereRaw('CONCAT(callback_date, " ", callback_time) <= ?', [$now->toDateTimeString()])
            ->select('callback_leads.*', 'leads.prospect_first_name', 'leads.prospect_last_name', 'leads.asign_to', 'leads.asign_to_manager')
            ->get();

        foreach ($pendingCallbacks as $cb) {
            $exists = \App\Models\Notification::where('lead_id', $cb->lead_id)
                ->where('type', 'callback_overdue')
                ->exists();

            if (!$exists) {
                \App\Models\Notification::create([
                    'lead_id' => $cb->lead_id,
                    'user_id' => $cb->asign_to_manager ?? $cb->asign_to,
                    'type' => 'callback_overdue',
                    'message' => "Callback is overdue for lead: {$cb->prospect_first_name} {$cb->prospect_last_name} (Scheduled: {$cb->callback_date} {$cb->callback_time})",
                ]);
            }
        }

        // 4. Meeting status overdue
        $overdueMeetings = \App\Models\Lead::whereNotNull('invitation_date')
            ->where('invitation_date', '<=', $now)
            ->where(function ($q) {
                $q->whereNull('meeting_status')
                    ->orWhere('meeting_status', '')
                    ->orWhere('meeting_status', 'Pending');
            })
            ->get();

        foreach ($overdueMeetings as $lead) {
            $exists = \App\Models\Notification::where('lead_id', $lead->id)
                ->where('type', 'meeting_status_overdue')
                ->exists();

            if (!$exists) {
                \App\Models\Notification::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->asign_to_manager,
                    'type' => 'meeting_status_overdue',
                    'message' => "Meeting status update is overdue for lead: {$lead->prospect_first_name} {$lead->prospect_last_name} (Meeting was: " . \Carbon\Carbon::parse($lead->invitation_date)->format('d M, Y h:i A') . ")",
                ]);
            }
        }

        $this->info('LHS Notifications checked and updated.');
    }
}
