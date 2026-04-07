<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Fatwa Activity Logs ────────────────────────────────────────
        Schema::create('fatwa_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('fatwa_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Actor
            $table->string('event');           // e.g. 'question_created', 'assigned', 'answer_submitted'
            $table->string('event_label');     // Human-readable: "Question Submitted", "Assigned to Mufti"
            $table->text('notes')->nullable(); // Optional admin/mufti notes
            $table->string('icon')->default('bi-circle'); // Bootstrap icon class
            $table->string('color')->default('secondary'); // success, warning, danger, info, primary
            $table->timestamp('event_at');     // When the event actually happened
            $table->timestamps();
        });

        // ─── Fatwa View Snapshots (every 45 days) ─────────────────────
        Schema::create('fatwa_view_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fatwa_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('views');
            $table->date('snapshot_date');
            $table->timestamps();
            $table->unique(['fatwa_id', 'snapshot_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fatwa_view_snapshots');
        Schema::dropIfExists('fatwa_activity_logs');
    }
};
