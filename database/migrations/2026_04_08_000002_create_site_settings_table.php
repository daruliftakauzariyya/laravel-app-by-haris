<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $row) {
            $row->id();
            $row->string('key')->unique();
            $row->text('value')->nullable();
            $row->string('type')->default('string'); // string, boolean, integer
            $row->timestamps();
        });

        // Insert initial Hijri settings
        DB::table('site_settings')->insert([
            ['key' => 'hijri_adjustment', 'value' => '0', 'type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'show_hijri_public', 'value' => '1', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
