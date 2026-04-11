<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fatwa_featured_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Rule label
            $table->tinyInteger('hijri_month')->nullable();  // 1-12
            $table->tinyInteger('hijri_day_start')->nullable();
            $table->tinyInteger('hijri_day_end')->nullable();
            $table->json('country_codes')->nullable();       // ["SA","IN","PK"]
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('madhab_id')->nullable()->constrained('madhabs')->onDelete('set null');
            $table->json('fatwa_ids')->nullable();           // Pin specific fatwa IDs
            $table->string('section_label')->default('Most Useful & Readable Fatwas');
            $table->string('section_sub')->default('Contextually curated Islamic rulings for your needs.');
            $table->tinyInteger('limit')->default(6);
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fatwa_featured_rules');
    }
};
