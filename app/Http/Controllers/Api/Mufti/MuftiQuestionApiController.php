<?php

namespace App\Http\Controllers\Api\Mufti;

use App\Http\Controllers\Controller;
use App\Models\QuestionAndAnswer;
use App\Models\FatwaActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MuftiQuestionApiController extends Controller
{
    /**
     * Check for live incoming assignments (for mobile app call alerts)
     * Rule 1: Once a Mufti opens/views a fatwa, call notifications for it must STOP.
     * Rule 2: If 1 fatwa is newly assigned -> Forward to that specific fatwa.
     * Rule 3: If >1 fatwas are newly assigned -> Forward to the Mufti Dashboard.
     */
    public function checkIncomingCalls(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        // 1. Get IDs of all fatwas already opened/viewed by this Mufti
        $openedQuestionIds = FatwaActivityLog::where('user_id', $user->id)
            ->where('action', 'mufti_opened')
            ->pluck('question_and_answer_id')
            ->toArray();

        // 2. Fetch pending assigned questions that have NOT been opened yet
        $unopenedAssignedQuery = QuestionAndAnswer::with(['category', 'madhab', 'user'])
            ->where('assigned_mufti_id', $user->id)
            ->where('status', 'assigned');

        if (!empty($openedQuestionIds)) {
            $unopenedAssignedQuery->whereNotIn('id', $openedQuestionIds);
        }

        $unopenedAssigned = $unopenedAssignedQuery->latest('updated_at')->get();
        $count = $unopenedAssigned->count();

        if ($count === 0) {
            return response()->json([
                'status'            => 'success',
                'has_incoming_call' => false,
                'assigned_count'    => 0,
                'data'              => null,
            ]);
        }

        // Rule: Exactly 1 newly assigned fatwa -> Forward directly to that fatwa details screen
        if ($count === 1) {
            $single = $unopenedAssigned->first();
            return response()->json([
                'status'            => 'success',
                'has_incoming_call' => true,
                'assigned_count'    => 1,
                'forward_target'    => 'task_details',
                'target_url'        => '/tasks/' . $single->id,
                'data'              => [
                    'id'             => $single->id,
                    'ticket_no'      => $single->question_id ?? ("#" . $single->id),
                    'title'          => $single->title ?: 'New Question for Ruling',
                    'question'       => $single->body ?: 'Question assigned for scholarly review and fatwa.',
                    'category'       => $single->category?->name ?? 'General Fiqh',
                    'madhab'         => $single->madhab?->name ?? 'Hanafi',
                    'asker_name'     => $single->user?->name ?? 'Darul Ifta Questioner',
                    'urgency'        => 'HIGH PRIORITY',
                    'assigned_count' => 1,
                    'forward_target' => 'task_details',
                    'target_url'     => '/tasks/' . $single->id,
                    'assigned_at'    => $single->updated_at ? $single->updated_at->toIso8601String() : now()->toIso8601String(),
                ]
            ]);
        }

        // Rule: More than 1 newly assigned fatwas -> Forward to Mufti Dashboard
        $firstItem = $unopenedAssigned->first();
        return response()->json([
            'status'            => 'success',
            'has_incoming_call' => true,
            'assigned_count'    => $count,
            'forward_target'    => 'dashboard',
            'target_url'        => '/',
            'data'              => [
                'id'             => $firstItem->id,
                'ticket_no'      => "{$count} NEW FATWAS",
                'title'          => "{$count} New Questions Assigned for Your Ruling",
                'question'       => "You have {$count} unreviewed scholarly inquiries waiting in your workspace.",
                'category'       => 'Multiple Inquiries',
                'madhab'         => 'Darul Ifta Council',
                'asker_name'     => 'Darul Ifta Academic Desk',
                'urgency'        => 'URGENT DISPATCH',
                'assigned_count' => $count,
                'forward_target' => 'dashboard',
                'target_url'     => '/',
                'assigned_at'    => $firstItem->updated_at ? $firstItem->updated_at->toIso8601String() : now()->toIso8601String(),
            ]
        ]);
    }

    /**
     * Mark Question as Opened by Mufti (silences future call notifications)
     */
    public function markOpened(Request $request, $id)
    {
        $user = Auth::user();
        $question = QuestionAndAnswer::findOrFail($id);

        if ($user) {
            FatwaActivityLog::log(
                $question->id,
                'mufti_opened',
                'Mufti ' . $user->name . ' Opened the Fatwa',
                'bi-eye-fill',
                'info',
                'Mufti ' . $user->name . ' opened the fatwa @ ' . now()->format('Y-m-d H:i:s'),
                $user->id
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Fatwa marked as opened.',
        ]);
    }

    /**
     * Tabbed Scholarly Inquiry Repository
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'assigned'); // assigned | verified | approved
        $search = $request->get('search', '');
        $madhabId = $request->get('madhab_id');
        $categoryId = $request->get('category_id');

        // Counts across all tabs
        $counts = [
            'assigned' => QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                ->where('status', 'assigned')
                ->count(),
            'verified' => QuestionAndAnswer::where(function ($q) use ($user) {
                $q->where('checked_approved_by', $user->id)
                  ->orWhereHas('statusHistory', fn($h) => $h->where('user_id', $user->id)->where('new_status', 'approved'));
            })->count(),
            'approved' => QuestionAndAnswer::where(function ($q) use ($user) {
                $q->where('published_by', $user->id)
                  ->orWhere('checked_approved_by', $user->id);
            })->whereIn('status', ['approved', 'published'])->count(),
        ];

        // Base query for current tab
        $query = QuestionAndAnswer::with(['user', 'category', 'madhab', 'references']);

        if ($tab === 'verified') {
            $query->where(function ($q) use ($user) {
                $q->where('checked_approved_by', $user->id)
                  ->orWhereHas('statusHistory', fn($h) => $h->where('user_id', $user->id)->where('new_status', 'approved'));
            });
        } elseif ($tab === 'approved') {
            $query->where(function ($q) use ($user) {
                $q->where('published_by', $user->id)
                  ->orWhere('checked_approved_by', $user->id);
            })->whereIn('status', ['approved', 'published']);
        } else {
            // Default: assigned inquiries
            $query->where('assigned_mufti_id', $user->id);
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
        }

        // Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('question_id', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Madhab & Category Filter
        if (!empty($madhabId)) {
            $query->where('madhab_id', $madhabId);
        }
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        $questions = $query->latest()->paginate(15);

        return response()->json([
            'status' => 'success',
            'tab'    => $tab,
            'counts' => $counts,
            'data'   => $questions,
        ]);
    }

    /**
     * Show Specific Inquiry & Research Workspace Data
     */
    public function show($id)
    {
        $user = Auth::user();
        $question = QuestionAndAnswer::with(['user', 'category', 'madhab', 'references', 'draftReferences', 'statusHistory'])
            ->findOrFail($id);

        if ($user && $question->assigned_mufti_id === $user->id) {
            FatwaActivityLog::log(
                $question->id,
                'mufti_opened',
                'Mufti ' . $user->name . ' Opened the Fatwa',
                'bi-eye-fill',
                'info',
                'Mufti ' . $user->name . ' opened the fatwa @ ' . now()->format('Y-m-d H:i:s'),
                $user->id
            );
        }

        return response()->json([
            'status' => 'success',
            'data'   => $question,
        ]);
    }

    /**
     * Submit Fatwa Ruling
     */
    public function submitAnswer(Request $request, $id)
    {
        $user = Auth::user();
        $question = QuestionAndAnswer::findOrFail($id);

        $request->validate([
            'summary'     => 'required|string|min:5',
            'full_answer' => 'required|string|min:15',
            'references'  => 'nullable|array',
        ]);

        $question->update([
            'title'        => $request->summary,
            'draft_answer' => $request->full_answer,
            'status'       => 'answered',
        ]);

        // Sync references
        if ($request->has('references') && is_array($request->references)) {
            if (method_exists($question, 'draftReferences')) {
                $question->draftReferences()->delete();
                foreach ($request->references as $index => $ref) {
                    if (!empty($ref['arabic_text'])) {
                        $question->draftReferences()->create([
                            'arabic_text' => $ref['arabic_text'],
                            'book_name'   => $ref['book_name'] ?? '',
                            'language'    => $ref['language'] ?? 'Arabic',
                            'sort_order'  => $index,
                            'is_draft'    => true,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Scholarly ruling submitted successfully for institutional verification.',
            'data'    => $question->fresh(['draftReferences', 'references']),
        ]);
    }

    /**
     * Save Draft In-Progress
     */
    public function saveDraft(Request $request, $id)
    {
        $question = QuestionAndAnswer::findOrFail($id);

        $question->update([
            'title'        => $request->input('summary', $question->title),
            'draft_answer' => $request->input('full_answer', $question->draft_answer),
        ]);

        if ($request->has('references') && is_array($request->references) && method_exists($question, 'draftReferences')) {
            $question->draftReferences()->delete();
            foreach ($request->references as $index => $ref) {
                if (!empty($ref['arabic_text'])) {
                    $question->draftReferences()->create([
                        'arabic_text' => $ref['arabic_text'],
                        'book_name'   => $ref['book_name'] ?? '',
                        'language'    => $ref['language'] ?? 'Arabic',
                        'sort_order'  => $index,
                        'is_draft'    => true,
                    ]);
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Draft saved successfully.',
        ]);
    }
}
