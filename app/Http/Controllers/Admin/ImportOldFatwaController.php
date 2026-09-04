<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FatwaActivityLog;
use App\Models\Madhab;
use App\Models\QuestionAndAnswer;
use App\Models\QuestionStatusHistory;
use App\Models\Reference;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ImportOldFatwaController extends Controller
{
    /**
     * Show the Single Page form to import/add old published fatwas.
     */
    public function create()
    {
        // 1. Madhabs
        $madhabs = Madhab::all();

        // 2. Categories (with children)
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        // 3. Muftis
        $muftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))
                      ->where('is_active', true)
                      ->orderBy('name')
                      ->get();

        // 4. Questioners
        $questioners = User::whereHas('role', fn($q) => $q->where('name', 'questioner'))
                           ->orderBy('name')
                           ->get();

        // Ensure default Questioner Demo user exists
        $defaultQuestioner = User::where('email', 'user@daruliftaportal.com')->first();
        if (!$defaultQuestioner) {
            $questionerRole = Role::firstOrCreate(['name' => 'questioner']);
            $defaultQuestioner = User::create([
                'name'              => 'Questioner Demo',
                'despl_name'        => 'Questioner Demo',
                'email'             => 'user@daruliftaportal.com',
                'password'          => bcrypt('User@123'),
                'role_id'           => $questionerRole->id,
                'profile_completed' => true,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);
        }

        // 5. Admins for approval selection
        $admins = User::whereHas('role', fn($q) => $q->whereIn('name', ['admin', 'mufti']))->get();

        // 6. Next Suggested Question ID
        $suggestedQuestionId = QuestionAndAnswer::generateQuestionId();

        // 7. Recently Imported Fatwas
        $recentFatwas = QuestionAndAnswer::with(['category', 'assignedMufti', 'madhab'])
                                         ->latest('id')
                                         ->take(15)
                                         ->get();

        return view('admin.fatwas.import-old', compact(
            'madhabs',
            'categories',
            'muftis',
            'questioners',
            'defaultQuestioner',
            'admins',
            'suggestedQuestionId',
            'recentFatwas'
        ));
    }

    /**
     * Store the old published fatwa and its references.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_id'        => ['required', 'string', 'max:50', 'unique:question_and_answer,question_id'],
            'title'              => ['required', 'string', 'max:500'],
            'body'               => ['required', 'string'],
            'answer'             => ['required', 'string'],
            'category_id'        => ['required', 'exists:categories,id'],
            'madhab_id'          => ['nullable', 'exists:madhabs,id'],
            'user_id'            => ['nullable', 'exists:users,id'],
            'new_questioner_name'=> ['nullable', 'string', 'max:150'],
            'assigned_mufti_id'  => ['nullable', 'exists:users,id'],
            'checked_approved_by'=> ['nullable', 'exists:users,id'],
            'language'           => ['required', 'string'],
            'status'             => ['required', 'in:published,answered,assigned,pending'],
            'published_at'       => ['nullable', 'string'],
            'views'              => ['nullable', 'integer', 'min:0'],
            'likes'              => ['nullable', 'integer', 'min:0'],
            'stars'              => ['nullable', 'integer', 'min:0'],
            'tags'               => ['nullable', 'string'],
            'featured_image_cropped' => ['nullable', 'string'],
        ]);

        // Enforce minimum 30 characters in answer for published fatwa
        $plainAnswer = trim(strip_tags($request->answer ?? ''));
        if (mb_strlen($plainAnswer) < 30) {
            return back()->withErrors([
                'answer' => 'The fatwa answer must contain at least 30 characters before it can be published (currently ' . mb_strlen($plainAnswer) . ' characters).',
            ])->withInput();
        }

        // Determine Questioner User
        $questionerId = $request->user_id;
        if (!empty($request->new_questioner_name)) {
            $questionerRole = Role::firstOrCreate(['name' => 'questioner']);
            $dummyEmail = 'user_' . time() . '_' . Str::random(4) . '@daruliftaportal.com';
            $newQuestioner = User::create([
                'name'              => $request->new_questioner_name,
                'despl_name'        => $request->new_questioner_name,
                'email'             => $dummyEmail,
                'password'          => bcrypt(Str::random(16)),
                'role_id'           => $questionerRole->id,
                'profile_completed' => true,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);
            $questionerId = $newQuestioner->id;
        }

        if (!$questionerId) {
            $defaultQuestioner = User::where('email', 'user@daruliftaportal.com')->first();
            $questionerId = $defaultQuestioner?->id ?? Auth::id();
        }

        // Parse Published Date
        $publishedAt = now();
        if ($request->filled('published_at')) {
            try {
                $publishedAt = Carbon::parse($request->published_at);
            } catch (\Exception $e) {
                $publishedAt = now();
            }
        }

        // Parse tags
        $tagsArray = null;
        if ($request->filled('tags')) {
            $tagsArray = array_map('trim', explode(',', $request->tags));
            $tagsArray = array_filter($tagsArray);
        }

        // Handle Image
        $imagePath = null;
        if ($request->filled('featured_image_cropped')) {
            $imagePath = $this->saveCroppedImage($request->featured_image_cropped);
        }

        // Create Question & Answer Record
        $fatwa = QuestionAndAnswer::create([
            'question_id'         => trim($request->question_id),
            'user_id'             => $questionerId,
            'category_id'         => $request->category_id,
            'madhab_id'           => $request->madhab_id,
            'assigned_mufti_id'   => $request->assigned_mufti_id,
            'checked_approved_by' => $request->checked_approved_by ?? $request->assigned_mufti_id ?? Auth::id(),
            'published_by'        => Auth::id(),
            'title'               => $request->title,
            'body'                => $request->body,
            'answer'              => $request->answer,
            'draft_answer'        => ($request->status !== 'published') ? $request->answer : null,
            'language'            => $request->language,
            'status'              => $request->status,
            'published_at'        => ($request->status === 'published') ? $publishedAt : null,
            'views'               => (int) ($request->views ?? 0),
            'likes'               => (int) ($request->likes ?? 0),
            'stars'               => (int) ($request->stars ?? 0),
            'tags'                => $tagsArray,
            'featured_image'      => $imagePath,
            'created_at'          => $publishedAt,
            'updated_at'          => $publishedAt,
        ]);

        // Save References
        if ($request->filled('arabic_text') && is_array($request->arabic_text)) {
            foreach ($request->arabic_text as $i => $text) {
                $text = trim($text);
                $bookName = trim($request->book_name[$i] ?? '');
                if (!empty($text) || !empty($bookName)) {
                    Reference::create([
                        'question_and_answer_id' => $fatwa->id,
                        'arabic_text'            => $text ?: '—',
                        'book_name'              => $bookName ?: 'Islamic Classical Text',
                        'language'               => $request->ref_language[$i] ?? 'Arabic',
                        'sort_order'             => $i,
                        'is_draft'               => ($fatwa->status !== 'published'),
                    ]);
                }
            }
        }

        // Log Activity
        FatwaActivityLog::log(
            $fatwa->id,
            'fatwa_imported',
            'Old Fatwa Imported to Portal',
            'bi-cloud-arrow-up-fill',
            'success',
            'Imported as ' . ucfirst($fatwa->status) . ' by ' . Auth::user()->name,
            Auth::id()
        );

        QuestionStatusHistory::create([
            'question_and_answer_id' => $fatwa->id,
            'previous_status'        => null,
            'new_status'             => $fatwa->status,
            'user_id'                => Auth::id(),
            'remarks'                => 'Old platform fatwa imported and recorded.',
        ]);

        $viewUrl = route('fatwa.show', $fatwa->id);

        return redirect()->route('admin.fatwas.import-old')
            ->with('success', "✅ Fatwa <strong>{$fatwa->question_id}</strong> imported successfully!")
            ->with('last_created_id', $fatwa->id)
            ->with('last_view_url', $viewUrl);
    }

    /**
     * AJAX quick-create Questioner user from the popup modal.
     */
    public function quickCreateQuestioner(Request $request)
    {
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:150'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email'  => ['nullable', 'email', 'max:150'],
        ]);

        $name = trim($request->name);
        $mobile = $request->filled('mobile') ? trim($request->mobile) : null;
        $email = $request->filled('email') ? trim($request->email) : null;

        // Check if email already exists
        if ($email) {
            $existing = User::where('email', $email)->first();
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $existing->id,
                        'name' => $existing->name,
                        'email' => $existing->email,
                        'mobile' => $existing->mobile,
                        'display' => "👤 {$existing->name} (" . ($existing->email ?: $existing->mobile) . ")",
                    ],
                    'message' => 'Existing questioner selected.',
                ]);
            }
        }

        // Check if mobile already exists
        if ($mobile && !$email) {
            $existing = User::where('mobile', $mobile)->first();
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $existing->id,
                        'name' => $existing->name,
                        'email' => $existing->email,
                        'mobile' => $existing->mobile,
                        'display' => "👤 {$existing->name} (" . ($existing->mobile ?: $existing->email) . ")",
                    ],
                    'message' => 'Existing questioner selected.',
                ]);
            }
        }

        // Generate email fallback if none provided
        if (!$email) {
            $email = 'user_' . time() . '_' . Str::random(4) . '@daruliftaportal.com';
        }

        $questionerRole = Role::firstOrCreate(['name' => 'questioner']);

        $user = User::create([
            'name'              => $name,
            'despl_name'        => $name,
            'mobile'            => $mobile,
            'email'             => $email,
            'password'          => bcrypt(Str::random(16)),
            'role_id'           => $questionerRole->id,
            'profile_completed' => true,
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        $displayLabel = "👤 {$user->name} (" . ($user->email ?: $user->mobile) . ")";

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'display' => $displayLabel,
            ],
            'message' => 'Questioner created successfully!',
        ]);
    }

    /**
     * Helper to decode base64 cropped image and save to disk
     */
    private function saveCroppedImage($base64Data)
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);

                if ($data === false) return null;

                $filename = 'fatwa_' . time() . '_' . Str::random(6) . '.' . $type;
                $directory = public_path('uploads/fatwas');

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                file_put_contents($directory . '/' . $filename, $data);
                return 'uploads/fatwas/' . $filename;
            }
        } catch (\Exception $e) {
            \Log::error("Cropped Image upload error: " . $e->getMessage());
        }
        return null;
    }
}
