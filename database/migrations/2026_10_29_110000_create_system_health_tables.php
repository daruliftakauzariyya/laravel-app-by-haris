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
        if (!Schema::hasTable('system_health_audit_logs')) {
            Schema::create('system_health_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->string('event', 60)->index();
                $table->string('email', 120);
                $table->string('ip_address', 45)->nullable()->index();
                $table->text('user_agent')->nullable();
                $table->json('details')->nullable();
                $table->timestamps();

                $table->index(['event', 'created_at']);
            });
        }

        if (!Schema::hasTable('system_api_logs')) {
            Schema::create('system_api_logs', function (Blueprint $table) {
                $table->id();
                $table->string('correlation_id', 36)->index();
                $table->string('method', 10);
                $table->string('path', 255)->index();
                $table->unsignedSmallInteger('status_code')->index();
                $table->decimal('duration_ms', 8, 2);
                $table->string('source_category', 50)->default('public_api');
                $table->boolean('is_synthetic')->default(false)->index();
                $table->string('error_category', 100)->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('created_at')->nullable()->index();

                $table->index(['created_at', 'status_code']);
                $table->index(['path', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_api_logs');
        Schema::dropIfExists('system_health_audit_logs');
    }
};

