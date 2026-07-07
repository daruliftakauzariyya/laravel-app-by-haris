<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HandleAvatars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class MuftiManagementController extends Controller
{
    use HandleAvatars;
    /** List all scholars with the 'mufti' role */
    public function index()
    {
        $muftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))
            ->withCount([
                'assignedQuestions as total_assigned',
                'assignedQuestions as pending_count' => fn($q) => $q->where('status', 'assigned'),
                'assignedQuestions as answered_count' => fn($q) => $q->whereIn('status', ['answered', 'approved', 'published']),
                'assignedQuestions as published_count' => fn($q) => $q->where('status', 'published'),
                'assignedQuestions as total_answers' => fn($q) => $q->whereIn('status', ['answered', 'approved', 'published']),
            ])
            ->latest()
            ->paginate(10);

        $muftis->getCollection()->each(function ($mufti) {
                $mufti->avg_days = \App\Models\QuestionAndAnswer::where('assigned_mufti_id', $mufti->id)
                    ->whereIn('status', ['answered', 'approved', 'published'])
                    ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg')
                    ->value('avg');
                $mufti->avg_days = $mufti->avg_days ? round($mufti->avg_days, 1) : null;
            });

        return view('admin.muftis.index', compact('muftis'));
    }

    /** Show the form for creating a new scholar */
    public function create()
    {
        $madhabs = \App\Models\Madhab::all();
        return view('admin.muftis.create', compact('madhabs'));
    }

    /** Store a newly created scholar in storage */
    public function store(Request $request)
    {
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'         => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'country'          => ['nullable', 'string', 'max:100'],
            'madhab'           => ['nullable', 'array'],
            'mufti_sort_order' => ['nullable', 'integer', 'min:0', 'max:9998'],
        ]);

        $user = User::create([
            'name'              => $request->name,
            'despl_name'        => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'country'           => $request->country,
            'madhab_id'         => !empty($request->madhab) ? head($request->madhab) : null,
            'is_active'         => $request->has('is_active'),
            'can_check_approve' => $request->has('can_check_approve'),
            'profile_completed' => true,
            'mufti_sort_order'  => (int) $request->input('mufti_sort_order', 0),
        ]);

        $user->assignRole('mufti');

        return redirect()->route('admin.muftis.index')->with('success', 'Mufti profile created successfully.');
    }

    /** Show the form for editing the specified scholar */
    public function edit(User $user)
    {
        // Ensure we are only editing a mufti
        if (!$user->hasRole('mufti')) {
            abort(403, 'Unauthorized action.');
        }

        $madhabs = \App\Models\Madhab::all();
        return view('admin.muftis.edit', compact('user', 'madhabs'));
    }

    /** Update the specified scholar in storage */
    public function update(Request $request, User $user)
    {
        if (!$user->hasRole('mufti')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password'         => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'country'          => ['nullable', 'string', 'max:100'],
            'madhab'           => ['nullable', 'array'],
            'mufti_sort_order' => ['nullable', 'integer', 'min:0', 'max:9998'],
        ]);

        $data = [
            'name'              => $request->name,
            'email'             => $request->email,
            'country'           => $request->country,
            'madhab_id'         => !empty($request->madhab) ? head($request->madhab) : null,
            'is_active'         => $request->has('is_active'),
            'can_check_approve' => $request->has('can_check_approve'),
            'mufti_sort_order'  => (int) $request->input('mufti_sort_order', 0),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle Admin Direct Upload
        if ($request->filled('avatar_data')) {
            $path = $this->processAndStoreAvatar($request->avatar_data, $user->id);
            if ($path) {
                $data['avatar'] = $path;
                // Clear any pending avatar if admin uploads directly
                Storage::disk('public')->delete("avatars/pending_{$user->id}.jpg");
            }
        }

        $user->update($data);

        return redirect()->route('admin.muftis.index')->with('success', 'Mufti profile updated successfully.');
    }

    /** Remove the specified scholar from storage */
    public function destroy(User $user)
    {
        if (!$user->hasRole('mufti')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if mufti has answers or assigned questions
        if ($user->muftiAnswers()->exists() || $user->assignedQuestions()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete Mufti because they have existing fatwas or assignments. Disable their account instead.');
        }

        $user->delete();

        return redirect()->route('admin.muftis.index')->with('success', 'Mufti profile deleted.');
    }

    /** Toggle active/inactive status */
    public function toggleStatus(User $user)
    {
        if (!$user->hasRole('mufti')) {
            abort(403, 'Unauthorized action.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return redirect()->back()->with('success', 'Mufti status updated.');
    }

    /** Display Academic Impact & Contributions */
    public function activity(User $user)
    {
        if (!$user->hasRole('mufti')) {
            abort(403, 'Unauthorized action.');
        }

        // 1. Questions Metrics
        $stats = [
            'assigned'   => $user->assignedQuestions()->count(),
            'answered'   => $user->assignedQuestions()->whereIn('status', ['answered', 'approved', 'published'])->count(),
            'published'  => $user->assignedQuestions()->where('status', 'published')->count(),
            'unassigned' => \App\Models\QuestionAndAnswer::whereNull('assigned_mufti_id')->where('status', 'pending')->count(), 
        ];

        // 2. Topics Covered (Categories)
        $topics_count = \App\Models\Category::whereHas('questions', function($q) use ($user) {
            $q->where('assigned_mufti_id', $user->id)
              ->whereIn('status', ['answered', 'approved', 'published']);
        })->count();

        // 3. Appreciation/Likes & 4. Public Reads
        $engagement = \App\Models\QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->whereIn('status', ['answered', 'approved', 'published'])
            ->selectRaw('SUM(likes) as total_likes, SUM(views) as total_views')
            ->first();

        $likes = $engagement->total_likes ?? 0;
        $views = $engagement->total_views ?? 0;

        // 5. Active Track (Currently assigned)
        $active_track = $user->assignedQuestions()->where('status', 'assigned')->count();

        // 6. Ifta Work Score (Efficiency, Speed & Volume Index)
        // Formula: (Answered * 10) + (Published * 5) + (Likes * 2) - (Avg Days * 5)
        $avg_days = \App\Models\QuestionAndAnswer::where('assigned_mufti_id', $user->id)
            ->whereIn('status', ['answered', 'approved', 'published'])
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg')
            ->value('avg') ?: 0;
        
        $score = ($stats['answered'] * 10) + ($stats['published'] * 5) + ($likes * 2);
        if ($avg_days > 0) {
            // Penalize high avg days, but keep score positive
            $score = max(0, $score - ($avg_days * 5));
        }

        // Normalize score to 100 base if very high, or just show as index
        $work_score = round($score);

        $pendingAvatar = Storage::disk('public')->exists("avatars/pending_{$user->id}.jpg") 
            ? asset("storage/avatars/pending_{$user->id}.jpg") 
            : null;

        return view('admin.muftis.activity', compact('user', 'stats', 'topics_count', 'likes', 'views', 'active_track', 'work_score', 'avg_days', 'pendingAvatar'));
    }

    /**
     * Approve a pending avatar submitted by a Mufti
     */
    public function approveAvatar(User $user)
    {
        $pendingFile = "avatars/pending_{$user->id}.jpg";
        $finalFile = "avatars/{$user->id}.jpg";

        if (Storage::disk('public')->exists($pendingFile)) {
            // Also copy to public folder directly for immediate visibility
            if (!file_exists(public_path('storage/avatars'))) {
                mkdir(public_path('storage/avatars'), 0755, true);
            }
            Storage::disk('public')->move($pendingFile, $finalFile);
            @copy(storage_path("app/public/{$finalFile}"), public_path("storage/{$finalFile}"));
            
            $user->update(['avatar' => "/storage/{$finalFile}"]);
            return back()->with('success', 'Profile photo approved successfully.');
        }

        return back()->with('error', 'No pending photo found.');
    }

    /**
     * Reject/Delete a pending avatar
     */
    public function rejectAvatar(User $user)
    {
        Storage::disk('public')->delete("avatars/pending_{$user->id}.jpg");
        return back()->with('success', 'Pending photo removed.');
    }
}
