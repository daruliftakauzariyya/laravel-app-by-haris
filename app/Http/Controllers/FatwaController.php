<?php

namespace App\Http\Controllers;

use App\Models\QuestionAndAnswer;
use App\Models\FatwaActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class FatwaController extends Controller
{
    public function index(Request $request)
    {
        $query = QuestionAndAnswer::with(['category', 'assignedMufti', 'references'])->where('status', 'published');

        // Filter by Madhab
        if ($request->filled('madhab_id')) {
            $query->where('madhab_id', $request->madhab_id);
        }

        // Filter by Category
        if ($request->filled('category')) {
            $catId = $request->category;
            $childIds = \App\Models\Category::where('parent_id', $catId)->pluck('id')->toArray();
            $allCatIds = array_merge([$catId], $childIds);
            $query->whereIn('category_id', $allCatIds);
        }

        // Universal fatwa search: Question, Answer, Category, References, Madhab
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('body', 'like', "%{$term}%")
                   ->orWhere('title', 'like', "%{$term}%")
                   ->orWhere('question_id', 'like', "%{$term}%")
                   ->orWhere('answer', 'like', "%{$term}%")
                   ->orWhereHas('madhab', fn($mq) => $mq->where('name', 'like', "%{$term}%"))
                   ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', "%{$term}%"))
                   ->orWhereHas('references', function ($rq) use ($term) {
                       $rq->where('arabic_text', 'like', "%{$term}%")
                          ->orWhere('book_name', 'like', "%{$term}%");
                   });
            });
        }

        // Apply Sorting / Ordering
        $sort = $request->query('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest('published_at');
                break;
            case 'most_read':
                $query->orderByDesc('views');
                break;
            case 'most_liked':
                $query->orderByDesc('likes');
                break;
            case 'most_starred':
                $query->orderByDesc('stars');
                break;
            case 'latest':
            default:
                $query->latest('published_at');
                break;
        }

        $allCategories = $this->getCategoryHierarchy();
            
        $fatwas = $query->paginate(12)->withQueryString();
        $madhabs = \App\Models\Madhab::all();
        return view('fatwa-archive', compact('fatwas', 'allCategories', 'madhabs'));
    }

    public function show(QuestionAndAnswer $fatwa)
    {
        $fatwa->load([
            'category', 
            'assignedMufti', 
            'references', 
            'publisher'
        ]);
        
        // Restrict access if not published
        if ($fatwa->status !== 'published') {
            $user = auth()->user();
            if (!$user) {
                abort(404);
            }
            if ($user->role?->name !== 'admin' && $user->role?->name !== 'mufti' && $fatwa->user_id !== $user->id) {
                abort(404);
            }
        }

        // Fetch related fatwas from the same category
        $relatedFatwas = QuestionAndAnswer::where('status', 'published')
            ->where('category_id', $fatwa->category_id)
            ->where('id', '!=', $fatwa->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        // New sidebar data
        $recentFatwas = QuestionAndAnswer::where('status', 'published')
            ->where('id', '!=', $fatwa->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        $allCategories = $this->getCategoryHierarchy();

        $fatwa->incrementViews(); // Public reads counter

        // Track questioner's own views (logged-in user only, session protected)
        if (auth()->check()) {
            $sessionKey = 'viewed_fatwa_' . $fatwa->id;
            $existed = session()->has($sessionKey);

            if (!$existed) {
                session()->put($sessionKey, true);
                if ($fatwa->user_id === auth()->id()) {
                    FatwaActivityLog::log(
                        $fatwa->id, 'questioner_read', 'Questioner Read Their Fatwa (First Open)',
                        'bi-eye', 'info',
                        auth()->user()->name . ' opened their published fatwa for the first time.',
                        auth()->id(), $fatwa->id
                    );
                }
            }
        }

        $hasLiked = session()->has('liked_fatwa_' . $fatwa->id);

        return view('fatwa-show', compact('fatwa', 'relatedFatwas', 'recentFatwas', 'allCategories', 'hasLiked'));
    }

    public function muftiArchive(User $user)
    {
        $user->loadCount([
            'assignedQuestions as assigned_questions_count',
            'assignedQuestions as published_fatwas_count' => fn($q) => $q->where('status', 'published')
        ]);
        
        $fatwasQuery = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->where('status', 'published')
            ->with(['category', 'assignedMufti']);

        $totalLikes = (clone $fatwasQuery)->sum('likes');
        $totalViews = (clone $fatwasQuery)->sum('views');
        $totalTopics = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->where('status', 'published')
            ->distinct('category_id')
            ->count('category_id');
        $totalAnswers = $user->published_fatwas_count;

        $fatwas = $fatwasQuery->latest('published_at')->paginate(12);

        $allCategories = $this->getCategoryHierarchy();

        return view('mufti-archive', compact(
            'user', 'fatwas', 'totalLikes', 'totalViews', 'totalTopics', 'totalAnswers', 'allCategories'
        ));
    }

    public function like(Request $request, QuestionAndAnswer $fatwa)
    {
        $alreadyLiked = session()->has('liked_fatwa_' . $fatwa->id);

        if ($alreadyLiked) {
            return response()->json([
                'success' => false,
                'already_liked' => true,
                'likes' => $fatwa->likes,
                'message' => 'You have already liked this fatwa.'
            ]);
        }

        // Record the like and increment counter
        $fatwa->increment('likes');
        session()->put('liked_fatwa_' . $fatwa->id, true);

        return response()->json([
            'success' => true,
            'already_liked' => false,
            'likes' => $fatwa->fresh()->likes,
        ]);
    }

    public function madhabArchive(Request $request)
    {
        $selectedMadhab = null;

        // Try to match query parameter keys (e.g. /fatwa/madhab?Hanafi)
        $queryParams = array_keys($request->all());
        if (!empty($queryParams)) {
            foreach ($queryParams as $param) {
                $selectedMadhab = \App\Models\Madhab::where('slug', strtolower($param))
                    ->orWhere('name', 'like', $param)
                    ->first();
                if ($selectedMadhab) {
                    break;
                }
            }
        }

        // If not matched by keys, check standard query value (e.g. ?madhab=Hanafi)
        if (!$selectedMadhab && $request->filled('madhab')) {
            $mParam = $request->query('madhab');
            $selectedMadhab = \App\Models\Madhab::where('slug', strtolower($mParam))
                ->orWhere('name', 'like', $mParam)
                ->first();
        }

        // Default fallback: if no madhab matched, default to Hanafi
        if (!$selectedMadhab) {
            $selectedMadhab = \App\Models\Madhab::where('slug', 'hanafi')->first();
        }

        // If still no madhab found (e.g. empty database table), redirect to standard archive
        if (!$selectedMadhab) {
            return redirect()->route('fatwa.archive');
        }

        // Set madhab_id in request so the index method handles the filter seamlessly
        $request->merge(['madhab_id' => $selectedMadhab->id]);

        return $this->index($request);
    }

    private function getCategoryHierarchy()
    {
        return \Illuminate\Support\Facades\Cache::remember('category_hierarchy', 3600, function () {
            $categoriesList = \App\Models\Category::withCount(['questions' => fn($q) => $q->where('status', 'published')])->get();
            
            $allCategories = $categoriesList->whereNull('parent_id')->sortBy('sort_order');
            foreach ($allCategories as $cat) {
                $cat->setRelation('children', $categoriesList->where('parent_id', $cat->id)->sortBy('sort_order'));
                // Calculate parent total including children
                $parentCount = $cat->questions_count;
                foreach ($cat->children as $child) {
                    $parentCount += $child->questions_count;
                }
                $cat->questions_count = $parentCount;
            }
            
            return $allCategories;
        });
    }
}
