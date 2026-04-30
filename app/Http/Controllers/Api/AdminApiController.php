<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminApiController extends Controller
{
    /**
     * Get the latest pending unassigned question for the "Call UI" (Admin only)
     */
    public function pendingCall()
    {
        $question = Question::with('user')
                    ->where('status', 'pending')
                    ->whereNull('assigned_mufti_id')
                    ->latest()
                    ->first();

        return response()->json([
            'status' => 'success',
            'data' => $question
        ]);
    }

    /**
     * Accept, Categorize, and Approve a question for processing
     */
    public function acceptQuestion(Request $request, Question $question)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'language'    => 'required|string',
        ]);

        $question->update([
            'category_id' => $request->category_id,
            'language'    => $request->language,
            'status'      => 'accepted', // Admin accepts it, it's now ready for assignment or muftis
            'accepted_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Question categorized and approved successfully'
        ]);
    }
}
