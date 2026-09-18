<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('question_and_answer', function (Blueprint $table) {
            if (!Schema::hasColumn('question_and_answer', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('status')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_and_answer', function (Blueprint $table) {
            if (Schema::hasColumn('question_and_answer', 'is_public')) {
                $table->dropColumn('is_public');
            }
        });
    }
};
