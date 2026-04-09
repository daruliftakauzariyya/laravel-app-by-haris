<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fatwa_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fatwa_id');
            $table->string('ip_address', 45);
            $table->timestamps();

            // One like per IP per fatwa
            $table->unique(['fatwa_id', 'ip_address']);
            $table->foreign('fatwa_id')->references('id')->on('fatwas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fatwa_likes');
    }
};
