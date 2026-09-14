<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Madhab;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MadhabController extends Controller
{
    public function index()
    {
        $madhabs = Madhab::withCount([
            'questions',
            'questions as published_questions_count' => fn($q) => $q->where('status_id', 6)->orWhere('status', 'published'),
            'users',
            'muftis'
        ])->orderBy('id')->get();

        $stats = [
            'total_madhabs'   => $madhabs->count(),
            'total_questions' => $madhabs->sum('questions_count'),
            'total_published' => $madhabs->sum('published_questions_count'),
            'total_muftis'    => $madhabs->sum('muftis_count'),
            'total_users'     => $madhabs->sum('users_count'),
        ];

        return view('admin.madhabs.index', compact('madhabs', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:madhabs,name',
        ]);

        Madhab::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Madhab created successfully.');
    }

    public function update(Request $request, Madhab $madhab)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:madhabs,name,' . $madhab->id,
        ]);

        $madhab->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Madhab updated successfully.');
    }

    public function destroy(Madhab $madhab)
    {
        if ($madhab->questions()->count() > 0 || $madhab->users()->count() > 0 || $madhab->muftis()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete Madhab because it is assigned to questions or users.');
        }

        $madhab->delete();
        return redirect()->back()->with('success', 'Madhab deleted successfully.');
    }
}
