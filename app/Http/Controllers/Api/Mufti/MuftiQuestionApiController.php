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
            \App\Models\FatwaActivityLog::log(
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
