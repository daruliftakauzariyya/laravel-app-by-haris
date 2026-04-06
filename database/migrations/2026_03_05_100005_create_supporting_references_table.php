<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supporting_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mufti_answer_id')->constrained()->onDelete('cascade');
            $table->longText('arabic_text');
            $table->string('book_name');
            $table->string('language')->default('Arabic'); // Arabic or Urdu
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supporting_references');
    }
};
