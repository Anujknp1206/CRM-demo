<?php

namespace App\Console\Commands;

use App\Models\Followup;
use Illuminate\Console\Command;

class FollowupReminder extends Command
{
    protected $signature = 'followup:reminder';
    protected $description = 'Send follow-up reminder notifications';

    public function handle()
    {
        $today = now()->toDateString();

        \Log::info('Followup reminder executed at ' . now());
        \Log::info('Today: ' . $today);

        $followups = Followup::with(['lead'])
            ->whereDate('nextactionDate', $today)
            ->where('reminder_sent', false)
            ->get();

        \Log::info('Followups found: ' . $followups->count());

        foreach ($followups as $f) {

            if (!$f->lead) {
                continue;
            }

            notifyAdmins(
                'Follow-up Reminder',
                "Follow-up due for Lead #{$f->lead->lead_code}",
                route('leads.index', [
                    $f->company_id,
                ]),
                'danger'
            );

            $f->update(['reminder_sent' => true]);
        }
    }
}
