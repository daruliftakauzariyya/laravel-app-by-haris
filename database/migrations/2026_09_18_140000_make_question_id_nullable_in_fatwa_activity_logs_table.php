<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fatwa_activity_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('question_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fatwa_activity_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('question_id')->nullable(false)->change();
        });
    }
};
