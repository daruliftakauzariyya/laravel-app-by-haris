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
        if (!Schema::hasTable('portal_visitor_logs')) {
            Schema::create('portal_visitor_logs', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('page', 500);
                $table->string('user_agent', 500)->nullable();
                $table->string('device_type', 50)->default('Web');
                $table->string('operating_system', 50)->default('Other');
                $table->string('browser', 50)->default('Other');
                $table->string('country', 100)->default('Unknown');
                $table->string('country_code', 10)->nullable();
                $table->string('region', 100)->nullable();
                $table->string('city', 100)->nullable();
                $table->timestamps();

                $table->index(['created_at']);
                $table->index(['ip_address']);
                $table->index(['user_id']);
                $table->index(['page']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_visitor_logs');
    }
};

