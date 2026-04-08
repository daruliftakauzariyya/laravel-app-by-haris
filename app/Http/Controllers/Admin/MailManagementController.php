<?php

namespace App\Http\Controllers\Admin;

use App\Mail\MuftiPendingReminderMail;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailManagementController extends Controller
{
    /**
     * Display a listing of Muftis and their pending question counts.
     */
    public function index()
    {
        // Get all active muftis
        $muftis = User::role('mufti')
            ->where('is_active', true)
            ->withCount(['assignedQuestions as pending_count' => function ($query) {
                $query->whereIn('status', ['assigned', 'under_review']);
            }])
            ->get();

        return view('admin.mail-management.index', compact('muftis'));
    }

    /**
     * Show the pending questions for a specific Mufti.
     */
    public function show(User $mufti)
    {
        $pendingQuestions = Question::where('assigned_mufti_id', $mufti->id)
            ->whereIn('status', ['assigned', 'under_review'])
            ->orderBy('assigned_at', 'asc')
            ->get();

        return view('admin.mail-management.show', compact('mufti', 'pendingQuestions'));
    }

    /**
     * Manually trigger the reminder email for a specific Mufti.
     */
    public function sendNow(User $mufti)
    {
        $pendingQuestions = Question::where('assigned_mufti_id', $mufti->id)
            ->whereIn('status', ['assigned', 'under_review'])
            ->get();

        if ($pendingQuestions->isEmpty()) {
            return redirect()->back()->with('error', "No pending questions found for " . $mufti->name);
        }

        try {
            Mail::to($mufti->email)->send(new MuftiPendingReminderMail($mufti, $pendingQuestions));
            
            return redirect()->route('admin.mail-management.index')
                ->with('success', "Reminder email successfully sent to " . $mufti->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed to send email: " . $e->getMessage());
        }
    }
}
