<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add missing columns to users and supporting_references tables.
 *
 * - users.mother_language      (string, nullable)
 * - users.mufti_sort_order     (unsignedSmallInt, default 0 — 0 means auto-sort by fatwa count)
 * - supporting_references.sort_order (integer, default 0)
 *
 * NOTE: This migration ONLY adds new columns.
 * It does NOT modify or drop any existing columns/tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── users table ──────────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mother_language')) {
                $table->string('mother_language')->nullable()->after('district');
            }
            if (!Schema::hasColumn('users', 'mufti_sort_order')) {
                // 0 = automatic (sort by fatwa count); 1+ = explicit display order
                $table->unsignedSmallInteger('mufti_sort_order')->default(0)->after('mother_language');
            }
        });

        // ── supporting_references table ───────────────────────────────────────
        if (Schema::hasTable('supporting_references') && !Schema::hasColumn('supporting_references', 'sort_order')) {
            Schema::table('supporting_references', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->after('language');
            });
        }
    }

    public function down(): void
    {
        // Drop only the columns added by this migration
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'mufti_sort_order')) {
                $table->dropColumn('mufti_sort_order');
            }
            if (Schema::hasColumn('users', 'mother_language')) {
                $table->dropColumn('mother_language');
            }
        });

        if (Schema::hasTable('supporting_references') && Schema::hasColumn('supporting_references', 'sort_order')) {
            Schema::table('supporting_references', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
