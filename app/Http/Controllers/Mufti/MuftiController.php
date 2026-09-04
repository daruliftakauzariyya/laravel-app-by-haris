<?php

namespace App\Http\Controllers\Mufti;

use App\Http\Controllers\Controller;
use App\Mail\AnswerSubmittedMail;
use App\Models\QuestionAndAnswer;
use App\Models\Reference;
use App\Models\FatwaActivityLog;
use App\Models\User;
use App\Models\Category;
use App\Models\Madhab;
use App\Models\QuestionStatusHistory;
use App\Traits\HandleAvatars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MuftiController extends Controller
{
    use HandleAvatars;

    public function dashboard()
    {
        $user = Auth::user();
        $assigned = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                            ->where('status', '!=', 'published')
                            ->count();
        $answered = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                            ->whereIn('status', ['answered', 'approved', 'published'])
                            ->count();
        $publishedCount = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                            ->where('status', 'published')
                            ->count();

        // Recent Inquiries: EXCLUDE published fatwas, order: Pending 1st, Opened 2nd, Answered 3rd
        $recent = QuestionAndAnswer::with(['madhab', 'category', 'statusHistory'])
            ->where('assigned_mufti_id', $user->id)
            ->where('status', '!=', 'published')
            ->orderByRaw("CASE 
                WHEN status = 'assigned' AND (draft_answer IS NULL OR draft_answer = '') THEN 1 
                WHEN status = 'assigned' AND (draft_answer IS NOT NULL AND draft_answer != '') THEN 2 
                WHEN status = 'answered' THEN 3 
                ELSE 4 END ASC")
            ->latest('updated_at')
            ->take(6)
            ->get();

        // Performance Score Calculation
        $totalEver = QuestionAndAnswer::where('assigned_mufti_id', $user->id)->count();
        $score = 0;
        if ($totalEver > 0) {
            // 1. Base Completion Rate (0-70 pts)
            $completionRate = ($answered / $totalEver) * 70;

            // 2. Speed Factor (0-20 pts)
            $avgDays = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
                ->where('status', '!=', 'assigned')
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                ->value('avg_days') ?: 5;
            
            $speedPoints = 0;
            if ($avgDays <= 2) $speedPoints = 20;
            elseif ($avgDays <= 5) $speedPoints = 15;
            elseif ($avgDays <= 10) $speedPoints = 10;
            else $speedPoints = 5;

            // 3. Volume Bonus (0-10 pts)
            $volumePoints = min(10, ($answered / 5));

            $score = round($completionRate + $speedPoints + $volumePoints);
            $score = min(100, $score);
        }

        return view('mufti.dashboard', compact('assigned', 'answered', 'publishedCount', 'recent', 'score'));
    }

    public function questions(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'assigned');

        // Tab counts for the badge pills using statusHistory for verified actions
        $counts = [
            'assigned'  => QuestionAndAnswer::where('assigned_mufti_id', $user->id)->where('status', '!=', 'published')->count(),
            'verified'  => QuestionAndAnswer::whereHas('statusHistory', fn($h) => $h->where('user_id', $user->id)->where('new_status', 'approved'))->count(),
            'approved'  => QuestionAndAnswer::where('published_by', $user->id)->count(),
            'published' => QuestionAndAnswer::where('assigned_mufti_id', $user->id)->where('status', 'published')->count(),
        ];

        $query = QuestionAndAnswer::with('user', 'category', 'madhab');

        if ($tab == 'verified') {
            $query->whereHas('statusHistory', fn($h) => $h->where('user_id', $user->id)->where('new_status', 'approved'))->latest();
        } elseif ($tab == 'approved') {
            $query->where('published_by', $user->id)->latest();
        } elseif ($tab == 'published') {
            $query->where('assigned_mufti_id', $user->id)->where('status', 'published')->latest('published_at');
        } else {
            // Default active assignments: EXCLUDE published, order by Pending 1st, Opened 2nd, Answered 3rd
            $query->where('assigned_mufti_id', $user->id)
                  ->where('status', '!=', 'published')
                  ->orderByRaw("CASE 
                      WHEN status = 'assigned' AND (draft_answer IS NULL OR draft_answer = '') THEN 1 
                      WHEN status = 'assigned' AND (draft_answer IS NOT NULL AND draft_answer != '') THEN 2 
                      WHEN status = 'answered' THEN 3 
                      ELSE 4 END ASC")
                  ->latest('updated_at');
        }

        $questions = $query->paginate(20)->withQueryString();

        return view('mufti.questions.index', compact('questions', 'tab', 'counts'));
    }

    public function pendingQuestions()
    {
        $user = Auth::user();
        $questions = QuestionAndAnswer::with(['user', 'category', 'statusHistory'])
            ->where('assigned_mufti_id', $user->id)
            ->where('status', 'assigned')
            ->latest()
            ->paginate(20);

        return view('mufti.questions.pending', compact('questions'));
    }

    public function publishedQuestions(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $madhabId = $request->get('madhab_id');

        $query = QuestionAndAnswer::with(['user', 'category', 'madhab', 'publisher', 'references'])
            ->where('assigned_mufti_id', $user->id)
            ->where('status', 'published');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('question_id', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($madhabId)) {
            $query->where('madhab_id', $madhabId);
        }

        $totalPublished = (clone $query)->count();
        $totalViews = (clone $query)->sum('views');
        $totalLikes = (clone $query)->sum('likes');

        $questions = $query->latest('published_at')->paginate(15)->withQueryString();
        $categories = \App\Models\Category::all();
        $madhabs = \App\Models\Madhab::all();

        return view('mufti.questions.published', compact(
            'questions', 'totalPublished', 'totalViews', 'totalLikes', 'categories', 'madhabs', 'search', 'categoryId', 'madhabId'
        ));
    }

    public function showQuestion(QuestionAndAnswer $question)
    {
        abort_unless($question->assigned_mufti_id === Auth::id(), 403);

        FatwaActivityLog::log(
            $question->id,
            'mufti_opened',
            'Mufti ' . Auth::user()->name . ' Opened the Fatwa',
            'bi-eye-fill',
            'info',
            'Mufti ' . Auth::user()->name . ' opened the fatwa @ ' . now()->format('Y-m-d H:i:s'),
            Auth::id()
        );

        $question->load([
            'user', 'madhab', 'references',
            'statusHistory' => fn($q) => $q->orderBy('created_at', 'desc')
        ]);
        return view('mufti.questions.answer', compact('question'));
    }

    public function submitAnswer(Request $request, QuestionAndAnswer $question)
    {
        abort_unless($question->assigned_mufti_id === Auth::id(), 403);

        $request->validate([
            'full_answer'    => ['required', 'string', 'min:30'],
            'arabic_text.*'  => ['nullable', 'string'],
            'book_name.*'    => ['nullable', 'string'],
            'ref_language.*' => ['nullable', 'string'],
            'question_title' => ['nullable', 'string', 'max:200'],
            'question_body'  => ['nullable', 'string'],
        ]);

        $plainAnswer = trim(strip_tags($request->full_answer ?? ''));
        if (mb_strlen($plainAnswer) < 30) {
            return back()->withErrors([
                'full_answer' => 'The answer must contain at least 30 characters before submitting (currently ' . mb_strlen($plainAnswer) . ' characters).',
            ])->withInput();
        }

        // Update the question title and body if provided
        if ($request->has('question_title')) {
            $question->update(['title' => $request->question_title]);
        }
        if ($request->has('question_body')) {
            $question->update(['body' => $request->question_body]);
        }

        $isPublished = $question->status === 'published';
        $previousStatus = $question->status;

        if ($isPublished) {
            // Update live answer directly for typos/immediate edits
            $question->update([
                'answer' => $request->full_answer,
            ]);
            $question->references()->delete();
        } else {
            // Store as draft answer for pending review
            $question->update([
                'draft_answer' => $request->full_answer,
                'status'       => 'answered',
            ]);
            $question->draftReferences()->delete();
        }

        // Save references
        if ($request->filled('arabic_text')) {
            foreach ($request->arabic_text as $i => $text) {
                if ($text) {
                    Reference::create([
                        'question_and_answer_id' => $question->id,
                        'arabic_text'            => $text,
                        'book_name'              => $request->book_name[$i] ?? '',
                        'language'               => $request->ref_language[$i] ?? 'Arabic',
                        'sort_order'             => $i,
                        'is_draft'               => !$isPublished,
                    ]);
                }
            }
        }

        $plainAnswerText = trim(strip_tags($request->full_answer ?? ''));
        $charCount = mb_strlen($plainAnswerText);
        $wordCount = str_word_count($plainAnswerText);
        $sentenceCount = count(array_filter(preg_split('/[.!?\n]+/u', $plainAnswerText), fn($s) => trim($s) !== ''));
        $refCount = count(array_filter($request->arabic_text ?? []));

        // Log the activity
        FatwaActivityLog::log(
            $question->id, 'answer_submitted', 'Answer for Fatwa ' . $question->question_id . ' Submitted by Scholar ' . Auth::user()->name,
            'bi-pencil-square', 'info',
            'Answer for fatwa ' . $question->question_id . ' submitted for verification by ' . Auth::user()->name . ' @ ' . now()->format('Y-m-d H:i:s') . ' (Letters: ' . number_format($charCount) . ', Words: ' . number_format($wordCount) . ', Sentences: ' . number_format($sentenceCount) . ($refCount > 0 ? ', ' . $refCount . ' references' : '') . ')',
            Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $question->id,
            'previous_status'        => $previousStatus,
            'new_status'             => $isPublished ? 'published' : 'answered',
            'user_id'                => Auth::id(),
            'remarks'                => 'Answer submitted by scholar ' . Auth::user()->name . ' (Letters: ' . number_format($charCount) . ', Words: ' . number_format($wordCount) . ').',
        ]);

        // Notify admin
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if ($admin) {
            try {
                Mail::to($admin->email)->send(new AnswerSubmittedMail($question));
            } catch (\Exception $e) {
                // Fail silently if mail server is not configured
            }
        }

        return redirect()->route('mufti.questions')->with('success', 'Answer submitted successfully.');
    }

    public function saveDraft(Request $request, QuestionAndAnswer $question)
    {
        abort_unless($question->assigned_mufti_id === Auth::id(), 403);

        $request->validate([
            'full_answer'    => ['nullable', 'string'],
            'arabic_text.*'  => ['nullable', 'string'],
            'book_name.*'    => ['nullable', 'string'],
            'ref_language.*' => ['nullable', 'string'],
            'question_title' => ['nullable', 'string', 'max:200'],
            'question_body'  => ['nullable', 'string'],
        ]);

        // Update the question title and body if provided
        if ($request->has('question_title')) {
            $question->update(['title' => $request->question_title]);
        }
        if ($request->has('question_body')) {
            $question->update(['body' => $request->question_body]);
        }

        // Save draft answer
        $question->update([
            'draft_answer' => $request->full_answer,
        ]);

        // Save references
        $question->draftReferences()->delete();
        if ($request->filled('arabic_text')) {
            foreach ($request->arabic_text as $i => $text) {
                if ($text) {
                    Reference::create([
                        'question_and_answer_id' => $question->id,
                        'arabic_text'            => $text,
                        'book_name'              => $request->book_name[$i] ?? '',
                        'language'               => $request->ref_language[$i] ?? 'Arabic',
                        'sort_order'             => $i,
                        'is_draft'               => true,
                    ]);
                }
            }
        }

        $plainDraftText = trim(strip_tags($request->full_answer ?? ''));
        $draftCharCount = mb_strlen($plainDraftText);
        $draftWordCount = str_word_count($plainDraftText);
        $draftSentenceCount = count(array_filter(preg_split('/[.!?\n]+/u', $plainDraftText), fn($s) => trim($s) !== ''));
        $draftRefCount = count(array_filter($request->arabic_text ?? []));

        // Log the activity
        FatwaActivityLog::log(
            $question->id, 'draft_saved', 'Fatwa Number ' . $question->question_id . ' Draft Saved by Scholar ' . Auth::user()->name,
            'bi-journal-text', 'secondary',
            'Fatwa Number ' . $question->question_id . ' draft saved by scholar ' . Auth::user()->name . ' @ ' . now()->format('Y-m-d H:i:s') . ' (Letters: ' . number_format($draftCharCount) . ', Words: ' . number_format($draftWordCount) . ', Sentences: ' . number_format($draftSentenceCount) . ($draftRefCount > 0 ? ', ' . $draftRefCount . ' references attached' : '') . ')',
            Auth::id()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Draft saved successfully.',
                'time' => now()->format('H:i:s'),
            ]);
        }

        return redirect()->back()->with('success', 'Draft saved successfully.');
    }

    public function profile()
    {
        return view('mufti.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'mobile'      => ['nullable', 'string', 'max:20'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar_data' => ['nullable', 'string'], // Base64 from Cropper
        ]);

        $user->email = $request->email;
        $user->mobile = $request->mobile;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $msg = 'Profile updated successfully.';

        if ($request->filled('avatar_data')) {
            // Store as PENDING for admin review
            $path = $this->processAndStoreAvatar($request->avatar_data, $user->id, true);
            if ($path) {
                $msg = 'Profile updated. Your new photo has been submitted for admin review.';
            }
        }

        $user->save();

        return redirect()->route('mufti.profile')->with('success', $msg);
    }

    public function reports(Request $request)
    {
        $user = Auth::user();
        $dateRange = $request->get('date_range', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $baseQuery = QuestionAndAnswer::where('assigned_mufti_id', $user->id);

        if ($dateRange === 'today') {
            $baseQuery->whereDate('created_at', \Carbon\Carbon::today());
        } elseif ($dateRange === 'this_week') {
            $baseQuery->whereBetween('created_at', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
        } elseif ($dateRange === 'this_month') {
            $baseQuery->whereMonth('created_at', \Carbon\Carbon::now()->month)->whereYear('created_at', \Carbon\Carbon::now()->year);
        } elseif ($dateRange === 'last_30_days') {
            $baseQuery->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30));
        } elseif ($dateRange === 'this_year') {
            $baseQuery->whereYear('created_at', \Carbon\Carbon::now()->year);
        } elseif ($dateRange === 'custom' && $startDate && $endDate) {
            $baseQuery->whereBetween('created_at', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        }

        $totalAssigned = (clone $baseQuery)->count();
        $totalAnswered = (clone $baseQuery)->whereNotNull('answer')->count();
        $totalPublished = (clone $baseQuery)->where('status', 'published')->count();
        $totalPending = (clone $baseQuery)->where('status', 'assigned')->count();
        $totalViews = (clone $baseQuery)->where('status', 'published')->sum('views');
        $totalLikes = (clone $baseQuery)->where('status', 'published')->sum('likes');

        $completionRate = $totalAssigned > 0 ? round(($totalAnswered / $totalAssigned) * 100, 1) : ($totalAnswered > 0 ? 100 : 0);
        
        $avgDays = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->whereIn('status', ['answered', 'approved', 'published'])
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg')
            ->value('avg');
        $avgDays = $avgDays ? round($avgDays, 1) : null;

        // 12-Month Performance Trend
        $monthlyTrends = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month_year, DATE_FORMAT(created_at, '%Y-%m') as sort_key, count(*) as assigned, sum(case when answer is not null then 1 else 0 end) as answered, sum(case when status = 'published' then 1 else 0 end) as published")
            ->where('created_at', '>=', \Carbon\Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month_year', 'sort_key')
            ->orderBy('sort_key', 'asc')
            ->get();

        // Category Specialization Breakdown
        $categoryBreakdown = \App\Models\Category::whereHas('questions', function ($q) use ($user) {
            $q->where('assigned_mufti_id', $user->id)->whereNotNull('answer');
        })->withCount(['questions' => function ($q) use ($user) {
            $q->where('assigned_mufti_id', $user->id)->whereNotNull('answer');
        }])->orderByDesc('questions_count')->take(6)->get();

        // Top Most-Read Fatwas by this Scholar
        $topFatwas = QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->where('status', 'published')
            ->with(['category', 'madhab'])
            ->orderByDesc('views')
            ->take(5)
            ->get();

        // Recent Inquiries Table
        $recentInquiries = (clone $baseQuery)
            ->with(['category', 'madhab'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('mufti.reports', compact(
            'dateRange', 'startDate', 'endDate',
            'totalAssigned', 'totalAnswered', 'totalPublished', 'totalPending',
            'totalViews', 'totalLikes', 'completionRate', 'avgDays',
            'monthlyTrends', 'categoryBreakdown', 'topFatwas', 'recentInquiries'
        ));
    }

    public function exportReports(Request $request)
    {
        $user = Auth::user();
        $dateRange = $request->get('date_range', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = QuestionAndAnswer::where('assigned_mufti_id', $user->id)->with(['category', 'madhab']);

        if ($dateRange === 'today') {
            $query->whereDate('created_at', \Carbon\Carbon::today());
        } elseif ($dateRange === 'this_month') {
            $query->whereMonth('created_at', \Carbon\Carbon::now()->month)->whereYear('created_at', \Carbon\Carbon::now()->year);
        } elseif ($dateRange === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        }

        $records = $query->latest()->get();
        $filename = 'scholar-report-' . str_replace(' ', '-', strtolower($user->name)) . '-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($records, $user) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['DARUL IFTA KAUZARIYYA — SCHOLAR FATWA DOSSIER']);
            fputcsv($handle, ['Scholar Name', $user->name]);
            fputcsv($handle, ['Export Date', date('Y-m-d H:i:s')]);
            fputcsv($handle, ['Total Records', $records->count()]);
            fputcsv($handle, []);

            fputcsv($handle, ['Question ID', 'Title', 'Category', 'Madhab', 'Status', 'Views', 'Likes', 'Assigned Date', 'Published Date']);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->question_id,
                    $r->title ?: strip_tags(substr($r->body, 0, 70)),
                    $r->category->name ?? 'General',
                    $r->madhab->name ?? 'General',
                    ucfirst($r->status),
                    $r->views,
                    $r->likes,
                    $r->created_at ? $r->created_at->format('Y-m-d H:i') : '',
                    $r->published_at ? $r->published_at->format('Y-m-d H:i') : '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Global scholarly search across all platform fatwas.
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));
        $categoryId = $request->get('category_id');
        $madhabId = $request->get('madhab_id');
        $status = $request->get('status', 'all');
        $scope = $request->get('scope', 'all'); // 'all' or 'my'
        $user = Auth::user();

        $fatwaQuery = QuestionAndAnswer::with(['user', 'category', 'madhab', 'assignedMufti', 'publisher', 'references']);

        if ($scope === 'my') {
            $fatwaQuery->where('assigned_mufti_id', $user->id);
        }

        if (!empty($status) && $status !== 'all') {
            $fatwaQuery->where('status', $status);
        }

        if (!empty($categoryId)) {
            $fatwaQuery->where('category_id', $categoryId);
        }

        if (!empty($madhabId)) {
            $fatwaQuery->where('madhab_id', $madhabId);
        }

        if (!empty($query)) {
            $fatwaQuery->where(function ($q) use ($query) {
                $q->where('question_id', 'like', "%{$query}%")
                  ->orWhere('title', 'like', "%{$query}%")
                  ->orWhere('body', 'like', "%{$query}%")
                  ->orWhere('answer', 'like', "%{$query}%")
                  ->orWhere('draft_answer', 'like', "%{$query}%")
                  ->orWhereHas('references', function ($rq) use ($query) {
                      $rq->where('book_name', 'like', "%{$query}%")
                         ->orWhere('arabic_text', 'like', "%{$query}%")
                         ->orWhere('footnote', 'like', "%{$query}%");
                  });
            });
        }

        $totalFound = (clone $fatwaQuery)->count();
        $publishedCount = (clone $fatwaQuery)->where('status', 'published')->count();
        $inProgressCount = (clone $fatwaQuery)->whereIn('status', ['assigned', 'answered'])->count();

        $questions = $fatwaQuery->latest('updated_at')->paginate(15)->withQueryString();
        $categories = Category::all();
        $madhabs = Madhab::all();

        return view('mufti.search', compact('questions', 'query', 'categories', 'madhabs', 'status', 'scope', 'categoryId', 'madhabId', 'totalFound', 'publishedCount', 'inProgressCount'));
    }

    /**
     * Fast AJAX live autocomplete search for header search box.
     */
    public function liveSearch(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (empty($query) || strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = QuestionAndAnswer::with(['category', 'madhab', 'assignedMufti'])
            ->where(function ($q) use ($query) {
                $q->where('question_id', 'like', "%{$query}%")
                  ->orWhere('title', 'like', "%{$query}%")
                  ->orWhere('body', 'like', "%{$query}%")
                  ->orWhere('answer', 'like', "%{$query}%")
                  ->orWhereHas('references', function ($rq) use ($query) {
                      $rq->where('book_name', 'like', "%{$query}%")
                         ->orWhere('arabic_text', 'like', "%{$query}%");
                  });
            })
            ->latest('updated_at')
            ->take(8)
            ->get()
            ->map(function ($item) {
                $title = $item->title ?: strip_tags(substr($item->body, 0, 75));
                $snippet = strip_tags(substr($item->answer ?: $item->body, 0, 110));

                return [
                    'id' => $item->id,
                    'question_id' => $item->question_id,
                    'title' => $title,
                    'snippet' => $snippet,
                    'status' => $item->status,
                    'status_label' => ucfirst($item->status),
                    'category' => $item->category?->name ?? 'General',
                    'madhab' => $item->madhab?->name ?? 'General',
                    'read_url' => route('mufti.fatwa.read', $item),
                    'workspace_url' => route('mufti.questions.show', $item),
                ];
            });

        return response()->json(['results' => $results]);
    }

    /**
     * Dedicated Fatwa Reading Page in Mufti Portal Theme.
     */
    public function readFatwa(QuestionAndAnswer $question)
    {
        $question->load([
            'user',
            'category',
            'madhab',
            'assignedMufti',
            'publisher',
            'approver',
            'references',
            'statusHistory.user',
            'activityLogs.user',
        ]);

        // Fetch related published scholarly rulings for reference
        $relatedFatwas = QuestionAndAnswer::with(['category', 'madhab'])
            ->where('id', '!=', $question->id)
            ->where('status', 'published')
            ->where(function ($q) use ($question) {
                if ($question->category_id) {
                    $q->where('category_id', $question->category_id);
                }
                if ($question->madhab_id) {
                    $q->orWhere('madhab_id', $question->madhab_id);
                }
            })
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('mufti.questions.read', compact('question', 'relatedFatwas'));
    }
}
