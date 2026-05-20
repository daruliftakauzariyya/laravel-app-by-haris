<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\FatwaActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminApiController extends Controller
{
    /**
     * Get the latest pending unassigned question for the "Call UI" (Admin only)
     */
    public function pendingCall()
    {
        $questions = Question::with('user')
                    ->where('status', 'pending')
                    ->whereNull('assigned_mufti_id')
                    ->latest()
                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => $questions
        ]);
    }

    /**
     * Get summary counts for the dashboard
     */
    public function dashboardStats()
    {
        $total = Question::count();
        $pending = Question::where('status', 'pending')->count();
        $ongoing = Question::whereIn('status', ['assigned', 'under_review'])->count();
        $muftiAnswered = Question::where('status', 'answered')->count();
        $published = Question::where('status', 'published')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $total,
                'pending' => $pending,
                'ongoing' => $ongoing,
                'mufti_answered' => $muftiAnswered,
                'published' => $published
            ]
        ]);
    }

    /**
     * Accept, Categorize, and Approve a question for processing
     */
    public function acceptQuestion(Request $request, Question $question)
    {
        $request->validate([
            'title'       => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'language'    => 'required|string',
        ]);

        $question->update([
            'title'       => $request->title,
            'category_id' => $request->category_id,
            'language'    => $request->language,
            'status'      => 'accepted', // Admin accepts it, it's now ready for assignment or muftis
            'accepted_at' => now(),
        ]);

        FatwaActivityLog::log(
            $question->id, 'accepted', 'Question Accepted & Categorised',
            'bi-check-circle', 'success',
            'Categorised under: ' . optional($question->fresh()->category)->name, Auth::id()
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Question categorized and approved successfully'
        ]);
    }
}
