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
        if (!Schema::hasTable('fatwa_featured_rules')) {
            Schema::create('fatwa_featured_rules', function (Blueprint $table) {
                $table->id();
                $table->string('name', 200);
                $table->unsignedTinyInteger('hijri_month')->nullable();
                $table->unsignedTinyInteger('hijri_day_start')->nullable();
                $table->unsignedTinyInteger('hijri_day_end')->nullable();
                $table->json('country_codes')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('madhab_id')->nullable();
                $table->json('fatwa_ids')->nullable();
                $table->string('section_label', 120)->default('Most Useful & Readable Fatwas');
                $table->string('section_sub', 300)->nullable();
                $table->unsignedTinyInteger('limit')->default(6);
                $table->integer('priority')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('category_id');
                $table->index('madhab_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatwa_featured_rules');
    }
};
