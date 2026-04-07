<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fatwas', function (Blueprint $table) {
            $table->unsignedBigInteger('verified_by')->nullable()->after('published_by');
            $table->unsignedBigInteger('checked_approved_by')->nullable()->after('verified_by');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('checked_approved_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_check_approve')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('fatwas', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['checked_approved_by']);
            $table->dropColumn(['verified_by', 'checked_approved_by']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('can_check_approve');
        });
    }
};
