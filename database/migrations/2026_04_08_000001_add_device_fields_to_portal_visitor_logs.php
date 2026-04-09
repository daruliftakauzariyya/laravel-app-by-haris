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
        if (!Schema::hasColumn('portal_visitor_logs', 'device_type')) {
            Schema::table('portal_visitor_logs', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('fatwa_id')->constrained()->onDelete('set null');
                $table->string('device_type')->nullable()->after('user_agent');       // Web, Tab, Mobile
                $table->string('operating_system')->nullable()->after('device_type'); // Windows, Android, iOS, Ubuntu, Linux
                $table->string('browser')->nullable()->after('operating_system');    // Chrome, Safari, etc
                
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portal_visitor_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'device_type', 'operating_system', 'browser']);
        });
    }
};
