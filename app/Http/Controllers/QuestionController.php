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
        $user = Auth::user();
        if ($user && $user->questions()->count() >= 1) {
            $hasMobile  = !empty($user->mobile);
            $hasGender  = !empty($user->gender) && strtolower($user->gender) !== 'not set';
            $hasMadhab  = !empty($user->madhab_id);
            $hasAddress = !empty($user->address_line_1);

            if (!$hasMobile || !$hasGender || !$hasMadhab || !$hasAddress || !$user->profile_completed) {
                return redirect(route('user.dashboard') . '#profile')
                    ->with('error', 'Completing your profile is mandatory before asking your second question. Please complete your profile details below.');
            }
        }

        $madhabs = \App\Models\Madhab::all();
        return view('ask-question', compact('madhabs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->questions()->count() >= 1) {
            $hasMobile  = !empty($user->mobile);
            $hasGender  = !empty($user->gender) && strtolower($user->gender) !== 'not set';
            $hasMadhab  = !empty($user->madhab_id);
            $hasAddress = !empty($user->address_line_1);

            if (!$hasMobile || !$hasGender || !$hasMadhab || !$hasAddress || !$user->profile_completed) {
                return redirect(route('user.dashboard') . '#profile')
                    ->with('error', 'Completing your profile is mandatory before asking your second question. Please complete your profile details below.');
            }
        }

        // Check for 5-minute patience (Throttling)
        $lastQuestion = Question::where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($lastQuestion && $lastQuestion->created_at->addMinutes(5)->isFuture()) {
            $diff = $lastQuestion->created_at->addMinutes(5)->diffForHumans();
            return redirect()->back()->withInput()->with('error', "Please wait. You can submit another question in {$diff}. A 5-minute patience is required between submissions.");
        }

        $user = Auth::user();
        $profileValidationRules = [];
        $shouldUpdateProfile = false;

        // Ask for profile details if they haven't completed the profile OR if they are missing a Mobile number (e.g. Google auth)
        if (!$user->profile_completed || empty($user->mobile)) {
            if (!$request->boolean('skip_profile')) {
                $shouldUpdateProfile = true;
                $profileValidationRules = [
                    'gender'          => ['required', 'in:Male,Female'],
                    'address_line_1'  => ['required', 'string', 'max:255'],
                    'address_line_2'  => ['nullable', 'string', 'max:255'],
                    'country'         => ['required', 'string', 'max:100'],
                    'state'           => ['required', 'string', 'max:100'],
                    'district'        => ['required', 'string', 'max:100'],
                ];

                if (empty($user->mobile)) {
                    $profileValidationRules['mobile'] = ['required', 'string', 'max:20'];
                }
            }
        }

        $rules = array_merge([
            'body'        => ['required', 'string', 'min:20'],
            'madhab_id'   => ['required', 'exists:madhabs,id'],
            'title'       => ['required', 'string', 'max:200'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'files.*'     => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ], $profileValidationRules);

        $request->validate($rules);

        $question = Question::create([
            'question_id' => Question::generateQuestionId(),
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'body'        => $request->body,
            'madhab_id'   => $request->madhab_id,
            'status'      => 'pending',
        ]);

        if ($shouldUpdateProfile) {
            $updateData = [
                'gender'            => $request->gender,
                'address_line_1'    => $request->address_line_1,
                'address_line_2'    => $request->address_line_2,
                'country'           => $request->country,
                'state'             => $request->state,
                'district'          => $request->district,
                'madhab_id'         => $request->madhab_id,
                'profile_completed' => true,
            ];

            if (empty($user->mobile)) {
                $updateData['mobile'] = $request->mobile;
            }

            $user->update($updateData);
        }

        // Send email if Mobile, Gender, Madhab Preference, or Address are not set in the DB
        $user->refresh();
        $hasMobile  = !empty($user->mobile);
        $hasGender  = !empty($user->gender) && strtolower($user->gender) !== 'not set';
        $hasMadhab  = !empty($user->madhab_id);
        $hasAddress = !empty($user->address_line_1);

        if (!$hasMobile || !$hasGender || !$hasMadhab || !$hasAddress) {
            try {
                Mail::to($user->email)->send(new \App\Mail\CompleteProfileRequestMail($user));
            } catch (\Exception $e) {
                \Log::error("Failed to send CompleteProfileRequestMail: " . $e->getMessage());
            }
        }

        $madhabName = \App\Models\Madhab::find($request->madhab_id)?->name ?? 'General';

        // Log the first event in the fatwa timeline
        FatwaActivityLog::log(
            $question->id, 'question_submitted', 'Question Submitted by Questioner',
            'bi-send', 'secondary',
            'Submitted in ' . $madhabName . ' madhab.', Auth::id(),
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
