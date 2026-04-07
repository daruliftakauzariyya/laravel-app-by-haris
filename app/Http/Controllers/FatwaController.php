<?php

namespace App\Http\Controllers;

use App\Models\Fatwa;
use App\Models\FatwaActivityLog;
use App\Models\FatwaUserView;
use App\Models\FatwaViewSnapshot;
use Illuminate\Http\Request;

class FatwaController extends Controller
{
    public function index(Request $request)
    {
        $query = Fatwa::with(['question.category', 'muftiAnswer.mufti', 'muftiAnswer.supportingReferences'])
                      ->latest('published_at');

        // Filter by Madhab
        if ($request->filled('madhab')) {
            $query->whereHas('question', fn($q) => $q->where('madhab', $request->madhab));
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->whereHas('question', fn($q) => $q->where('category_id', $request->category));
        }

        // Universal fatwa search: Question, Answer, Category, References, Madhab
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                // Search in Question
                $q->whereHas('question', function ($sq) use ($term) {
                    $sq->where('body', 'like', "%{$term}%")
                       ->orWhere('title', 'like', "%{$term}%")
                       ->orWhere('question_id', 'like', "%{$term}%")
                       ->orWhere('madhab', 'like', "%{$term}%");
                });
                // Search in Answer
                $q->orWhereHas('muftiAnswer', function ($aq) use ($term) {
                    $aq->where('full_answer', 'like', "%{$term}%")
                       ->orWhere('summary', 'like', "%{$term}%");
                });
                // Search in Category
                $q->orWhereHas('question.category', function ($cq) use ($term) {
                    $cq->where('name', 'like', "%{$term}%");
                });
                // Search in Supporting References
                $q->orWhereHas('muftiAnswer.supportingReferences', function ($rq) use ($term) {
                    $rq->where('arabic_text', 'like', "%{$term}%")
                       ->orWhere('book_name', 'like', "%{$term}%");
                });
            });
        }

        $allCategories = \App\Models\Category::whereNull('parent_id')
            ->withCount(['questions' => fn($q) => $q->where('status', 'published')])
            ->orderBy('sort_order')
            ->get();
            
        $fatwas = $query->paginate(12)->withQueryString();
        return view('fatwa-archive', compact('fatwas', 'allCategories'));
    }

    public function show(Fatwa $fatwa)
    {
        $fatwa->load([
            'question.category', 
            'muftiAnswer.mufti', 
            'muftiAnswer.supportingReferences', 
            'publisher',
            'verifiedBy',
            'checkedApprovedBy'
        ]);

        $question = $fatwa->question;
        
        // Restrict access if not published
        if ($question->status !== 'published') {
            $user = auth()->user();
            if (!$user) {
                abort(404);
            }
            if (!$user->hasRole(['admin', 'mufti']) && $question->user_id !== $user->id) {
                abort(404);
            }
        }

        // Fetch related fatwas from the same category
        $relatedFatwas = Fatwa::whereHas('question', function($q) use ($question) {
                $q->where('category_id', $question->category_id);
            })
            ->where('id', '!=', $fatwa->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        // New sidebar data
        $recentFatwas = Fatwa::latest('published_at')
            ->where('id', '!=', $fatwa->id)
            ->take(5)
            ->get();

        $allCategories = \App\Models\Category::whereNull('parent_id')
            ->withCount(['questions' => fn($q) => $q->where('status', 'published')])
            ->orderBy('sort_order')
            ->get();

        $fatwa->incrementViews(); // Public reads counter

        // Track questioner's own views (logged-in user only)
        if (auth()->check()) {
            $existed = FatwaUserView::where('fatwa_id', $fatwa->id)
                ->where('user_id', auth()->id())
                ->exists();

            FatwaUserView::updateOrCreate(
                ['fatwa_id' => $fatwa->id, 'user_id' => auth()->id()],
                ['views'    => 1]
            );
            FatwaUserView::where('fatwa_id', $fatwa->id)
                ->where('user_id', auth()->id())
                ->increment('views');

            // Log first read event in timeline
            if (!$existed && $fatwa->question->user_id === auth()->id()) {
                FatwaActivityLog::log(
                    $fatwa->question_id, 'questioner_read', 'Questioner Read Their Fatwa (First Open)',
                    'bi-eye', 'info',
                    auth()->user()->name . ' opened their published fatwa for the first time.',
                    auth()->id(), $fatwa->id
                );
            }
        }

        return view('fatwa-show', compact('fatwa', 'relatedFatwas', 'recentFatwas', 'allCategories'));
    }
}
