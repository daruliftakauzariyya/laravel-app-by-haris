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
        $fatwa->load(['question.user', 'muftiAnswer.mufti', 'muftiAnswer.supportingReferences', 'publisher']);
        $fatwa->incrementViews();
        return view('fatwa-show', compact('fatwa'));
    }
}
