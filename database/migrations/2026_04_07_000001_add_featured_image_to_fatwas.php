<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fatwas', function (Blueprint $row) {
            $row->string('featured_image')->nullable()->after('fatwa_number');
        });
    }

    public function down(): void
    {
        Schema::table('fatwas', function (Blueprint $row) {
            $row->dropColumn('featured_image');
        });
    }
};
