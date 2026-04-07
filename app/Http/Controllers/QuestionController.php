<?php

namespace App\Http\Controllers;

use App\Mail\NewQuestionMail;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function create()
    {
        return view('ask-question');
    }

    public function store(Request $request)
    {
        $request->validate([
            'body'        => ['required', 'string', 'min:20'],
            'madhab'      => ['required', 'in:Hanafi,Shafii,Maliki,Hanbali,Other'],
            'title'       => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'files.*'     => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $question = Question::create([
            'question_id' => Question::generateQuestionId(),
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'body'        => $request->body,
            'madhab'      => $request->madhab,
            'status'      => 'pending',
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('question_files', 'local');
                QuestionFile::create([
                    'question_id'   => $question->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path'     => $path,
                    'file_type'     => $file->getClientOriginalExtension(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        // Email admin
        $admin = User::role('admin')->first();
        if ($admin) {
            Mail::to($admin->email)->send(new \App\Mail\NewQuestionMail($question));
        }

        // Email the user
        Mail::to($request->user()->email)->send(new \App\Mail\UserQuestionSubmittedMail($question));

        return redirect()->route('home')->with('success', 'Your question has been submitted successfully! Question ID: ' . $question->question_id);
    }
}
