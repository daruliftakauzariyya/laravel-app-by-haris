<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('page')->nullable();           // URL path visited
            $table->foreignId('fatwa_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
            // Index for fast dashboard queries
            $table->index('country');
            $table->index('country_code');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_visitor_logs');
    }
};
