<?php

namespace App\Http\Controllers\Api\Mufti;

use App\Http\Controllers\Controller;
use App\Models\QuestionAndAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MuftiQuestionApiController extends Controller
{
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
            'verified' => QuestionAndAnswer::where('checked_by_mufti_id', $user->id)
                ->count(),
            'approved' => QuestionAndAnswer::where(function ($q) use ($user) {
                $q->where('assigned_mufti_id', $user->id)
                  ->orWhere('checked_by_mufti_id', $user->id);
            })->whereIn('status', ['approved', 'published'])->count(),
        ];

        // Base query for current tab
        $query = QuestionAndAnswer::with(['user', 'category', 'madhab', 'files']);

        if ($tab === 'verified') {
            $query->where('checked_by_mufti_id', $user->id);
        } elseif ($tab === 'approved') {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_mufti_id', $user->id)
                  ->orWhere('checked_by_mufti_id', $user->id);
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
                  ->orWhere('question', 'like', "%{$search}%")
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
        $question = QuestionAndAnswer::with(['user', 'category', 'madhab', 'files', 'references', 'draftReferences'])
            ->findOrFail($id);

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
            'answered_at'  => now(),
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
                            'page_info'   => $ref['page_info'] ?? '',
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
            'data'    => $question->fresh(['draftReferences']),
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
                        'page_info'   => $ref['page_info'] ?? '',
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
