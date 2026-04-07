<?php

namespace App\Console\Commands;

use App\Mail\MuftiPendingReminderMail;
use App\Models\User;
use App\Models\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMuftiPendingReminders extends Command
{
    protected $signature   = 'mufti:send-reminders';
    protected $description = 'Send pending question reminder emails to all muftis every 3 days';

    public function handle(): void
    {
        $muftis = User::role('mufti')->where('is_active', true)->get();

        $sent = 0;

        foreach ($muftis as $mufti) {
            // Find all questions assigned to this mufti that are still 'assigned' (not yet answered)
            $pendingQuestions = Question::where('assigned_mufti_id', $mufti->id)
                ->where('status', 'assigned')
                ->get();

            if ($pendingQuestions->isEmpty()) {
                $this->line("  ↳ No pending questions for {$mufti->name} — skipping.");
                continue;
            }

            try {
                Mail::to($mufti->email)->send(new MuftiPendingReminderMail($mufti, $pendingQuestions));
                $sent++;
                $this->info("  ✓ Reminder sent to {$mufti->name} ({$mufti->email}) — {$pendingQuestions->count()} pending");
            } catch (\Exception $e) {
                $this->error("  ✗ Failed to send to {$mufti->email}: " . $e->getMessage());
            }
        }

        $this->info("\nDone. Reminder emails sent to {$sent} mufti(s).");
    }
}
