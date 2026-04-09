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
        Schema::table('fatwas', function (Blueprint $table) {
            $table->unsignedInteger('likes')->default(0)->after('views');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('stars')->default(5)->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fatwas', function (Blueprint $table) {
            $table->dropColumn('likes');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('stars');
        });
    }
};
