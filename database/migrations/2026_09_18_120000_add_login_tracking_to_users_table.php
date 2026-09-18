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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'login_count')) {
                $table->unsignedInteger('login_count')->default(0)->after('can_check_approve');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('login_count');
            }
            if (!Schema::hasColumn('users', 'last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable()->after('last_login_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'login_count')) {
                $columns[] = 'login_count';
            }
            if (Schema::hasColumn('users', 'last_login_at')) {
                $columns[] = 'last_login_at';
            }
            if (Schema::hasColumn('users', 'last_seen_at')) {
                $columns[] = 'last_seen_at';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
