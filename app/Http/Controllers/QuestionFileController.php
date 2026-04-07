<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionFileController extends Controller
{
    /** Handle AJAX file upload */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            
            // Generate a unique filename to avoid collisions before question_id is set
            $filename = time() . '_' . Str::random(5) . '_' . $originalName;
            $path = $file->storeAs('question_files/temp', $filename, 'local');

            return response()->json([
                'success'       => true,
                'path'          => $path,
                'original_name' => $originalName,
                'file_type'     => $file->getClientOriginalExtension(),
                'file_size'     => $file->getSize(),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
    }

    /** Handle AJAX file deletion */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);

        if (Storage::disk('local')->exists($request->path)) {
            Storage::disk('local')->delete($request->path);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'File not found.'], 404);
    }
}
