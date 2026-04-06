<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_id')->unique(); // IFTA-YYYYMMDD-XXXX
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_mufti_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title')->nullable();
            $table->text('body');
            $table->enum('madhab', ['Hanafi', 'Shafii', 'Maliki', 'Hanbali', 'Other'])->default('Hanafi');
            $table->string('language')->nullable()->default('English');
            $table->string('question_type')->nullable();
            $table->enum('status', [
                'pending',
                'accepted',
                'assigned',
                'answered',
                'under_review',
                'approved',
                'rejected',
                'published',
            ])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
