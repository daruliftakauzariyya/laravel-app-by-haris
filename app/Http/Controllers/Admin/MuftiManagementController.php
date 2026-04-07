<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class MuftiManagementController extends Controller
{
    /** List all scholars with the 'mufti' role */
    public function index()
    {
        $muftis = User::role('mufti')->latest()->get();
        return view('admin.muftis.index', compact('muftis'));
    }

    /** Show the form for creating a new scholar */
    public function create()
    {
        return view('admin.muftis.create');
    }

    /** Store a newly created scholar in storage */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'is_active'         => $request->has('is_active'),
            'can_check_approve' => $request->has('can_check_approve'),
            'profile_completed' => true,
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

        return view('admin.muftis.edit', compact('user'));
    }

    /** Update the specified scholar in storage */
    public function update(Request $request, User $user)
    {
        if (!$user->hasRole('mufti')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name'              => $request->name,
            'email'             => $request->email,
            'is_active'         => $request->has('is_active'),
            'can_check_approve' => $request->has('can_check_approve'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
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
}
