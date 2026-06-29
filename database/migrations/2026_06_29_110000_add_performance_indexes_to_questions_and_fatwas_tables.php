<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->index('status', 'idx_questions_status');
        });

        Schema::table('fatwas', function (Blueprint $table) {
            $table->index('published_at', 'idx_fatwas_published_at');
            $table->index('views', 'idx_fatwas_views');
            $table->index('likes', 'idx_fatwas_likes');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_questions_status');
        });

        Schema::table('fatwas', function (Blueprint $table) {
            $table->dropIndex('idx_fatwas_published_at');
            $table->dropIndex('idx_fatwas_views');
            $table->dropIndex('idx_fatwas_likes');
        });
    }
};
