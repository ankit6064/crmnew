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
            $exists = \App\Models\Notification::where('lead_id', $lead->id)
                ->where('type', 'confirmation_overdue')
                ->exists();

            if (!$exists) {
                \App\Models\Notification::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->asign_to_manager,
                    'type' => 'confirmation_overdue',
                    'message' => "Confirmation is overdue (48h+) for lead: {$lead->prospect_first_name} {$lead->prospect_last_name}",
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

        $this->info('LHS Notifications checked and updated.');
    }
}
