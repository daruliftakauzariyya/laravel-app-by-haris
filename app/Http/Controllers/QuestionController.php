<?php

namespace App\Http\Controllers;

use App\Mail\NewQuestionMail;
use App\Models\Category;
use App\Models\FatwaActivityLog;
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
        // Check for 5-minute patience (Throttling)
        $lastQuestion = Question::where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($lastQuestion && $lastQuestion->created_at->addMinutes(5)->isFuture()) {
            $diff = $lastQuestion->created_at->addMinutes(5)->diffForHumans();
            return redirect()->back()->withInput()->with('error', "Please wait. You can submit another question in {$diff}. A 5-minute patience is required between submissions.");
        }

        $request->validate([
            'body'        => ['required', 'string', 'min:20'],
            'madhab'      => ['required', 'in:Hanafi,Shafii,Maliki,Hanbali,Other'],
            'title'       => ['required', 'string', 'max:200'],
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

        // Log the first event in the fatwa timeline
        FatwaActivityLog::log(
            $question->id, 'question_submitted', 'Question Submitted by Questioner',
            'bi-send', 'secondary',
            'Submitted in ' . $request->madhab . ' madhab.', Auth::id(),
            null, $question->created_at
        );

        // Handle pre-uploaded files
        if ($request->has('uploaded_files')) {
            foreach ($request->uploaded_files as $fileJson) {
                $fileData = json_decode($fileJson, true);
                if ($fileData && isset($fileData['path'])) {
                    // Files are already in question_files/temp
                    // We can keep them there or move them. For now, we'll just record the path.
                    // If we want to move them to a non-temp folder:
                    $oldPath = $fileData['path'];
                    $newPath = str_replace('question_files/temp/', 'question_files/', $oldPath);
                    
                    if (Storage::disk('local')->exists($oldPath)) {
                        Storage::disk('local')->move($oldPath, $newPath);
                        
                        QuestionFile::create([
                            'question_id'   => $question->id,
                            'original_name' => $fileData['name'],
                            'file_path'     => $newPath,
                            'file_type'     => $fileData['type'],
                            'file_size'     => $fileData['size'],
                        ]);
                    }
                }
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
