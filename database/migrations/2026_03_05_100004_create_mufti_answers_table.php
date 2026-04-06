<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mufti_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('mufti_id')->constrained('users')->onDelete('cascade');
            $table->text('summary'); // short question summary
            $table->longText('full_answer');
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'correction_requested'])->default('draft');
            $table->text('admin_notes')->nullable(); // notes from admin during review
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mufti_answers');
    }
};
