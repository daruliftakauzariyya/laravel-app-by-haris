<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuestionAndAnswer;
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
        $questions = QuestionAndAnswer::with('user')
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
        $total = QuestionAndAnswer::count();
        $pending = QuestionAndAnswer::where('status', 'pending')->count();
        $ongoing = QuestionAndAnswer::whereIn('status', ['assigned', 'under_review'])->count();
        $muftiAnswered = QuestionAndAnswer::where('status', 'answered')->count();
        $published = QuestionAndAnswer::where('status', 'published')->count();

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
    public function acceptQuestion(Request $request, $id)
    {
        $question = QuestionAndAnswer::findOrFail($id);

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
        ]);

        // Log status change
        $question->statusHistory()->create([
            'previous_status' => 'pending',
            'new_status'      => 'accepted',
            'user_id'         => Auth::id(),
            'remarks'         => 'Categorised under: ' . optional($question->fresh()->category)->name,
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
