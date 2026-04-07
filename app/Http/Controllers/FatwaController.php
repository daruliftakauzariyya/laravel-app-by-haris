<?php

namespace App\Http\Controllers;

use App\Models\Fatwa;
use Illuminate\Http\Request;

class FatwaController extends Controller
{
    public function index(Request $request)
    {
        $query = Fatwa::with(['question', 'muftiAnswer.mufti', 'muftiAnswer.supportingReferences'])
                      ->latest('published_at');

        if ($request->filled('madhab')) {
            $query->whereHas('question', fn($q) => $q->where('madhab', $request->madhab));
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->whereHas('question', function ($q) use ($term) {
                $q->where('body', 'like', "%{$term}%")
                  ->orWhere('title', 'like', "%{$term}%")
                  ->orWhere('question_id', 'like', "%{$term}%");
            });
        }

        $fatwas = $query->paginate(10)->withQueryString();
        return view('fatwa-archive', compact('fatwas'));
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

        $fatwa->incrementViews();
        return view('fatwa-show', compact('fatwa', 'relatedFatwas'));
    }
}
