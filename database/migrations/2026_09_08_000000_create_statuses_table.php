<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('statuses')) {
            Schema::create('statuses', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique()->index();
                $table->string('name', 100);
                $table->string('badge_color', 50)->default('#64748b');
                $table->string('badge_class', 100)->default('badge bg-secondary');
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Seed default canonical statuses with exact ID mapping
        $statuses = [
            [
                'id'          => 1,
                'code'        => 'pending',
                'name'        => 'Pending',
                'badge_color' => '#f59e0b',
                'badge_class' => 'badge bg-warning text-dark',
                'description' => 'Newly submitted inquiry awaiting administrative triage.',
                'sort_order'  => 1,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 2,
                'code'        => 'accepted',
                'name'        => 'Accepted',
                'badge_color' => '#0ea5e9',
                'badge_class' => 'badge bg-info text-white',
                'description' => 'Accepted by admin for processing.',
                'sort_order'  => 2,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 3,
                'code'        => 'assigned',
                'name'        => 'Assigned',
                'badge_color' => '#6366f1',
                'badge_class' => 'badge bg-primary text-white',
                'description' => 'Assigned to a specific Mufti for researching and drafting.',
                'sort_order'  => 3,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 4,
                'code'        => 'answered',
                'name'        => 'Answered',
                'badge_color' => '#06b6d4',
                'badge_class' => 'badge bg-info text-dark',
                'description' => 'Drafted by Mufti and submitted for verification.',
                'sort_order'  => 4,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 5,
                'code'        => 'approved',
                'name'        => 'Approved',
                'badge_color' => '#10b981',
                'badge_class' => 'badge bg-success text-white',
                'description' => 'Verified and approved by Senior Checker / Admin.',
                'sort_order'  => 5,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 6,
                'code'        => 'published',
                'name'        => 'Published',
                'badge_color' => '#059669',
                'badge_class' => 'badge bg-success text-white',
                'description' => 'Officially published on the Darul Ifta public archive.',
                'sort_order'  => 6,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 7,
                'code'        => 'rejected',
                'name'        => 'Rejected',
                'badge_color' => '#ef4444',
                'badge_class' => 'badge bg-danger text-white',
                'description' => 'Inquiry rejected by administration.',
                'sort_order'  => 7,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 8,
                'code'        => 'correction',
                'name'        => 'Needs Correction',
                'badge_color' => '#f97316',
                'badge_class' => 'badge bg-warning text-dark',
                'description' => 'Sent back to assigned Mufti for revisions or additional references.',
                'sort_order'  => 8,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        foreach ($statuses as $st) {
            DB::table('statuses')->updateOrInsert(
                ['id' => $st['id']],
                $st
            );
        }

        // Backfill existing question_and_answer records: map status string -> status_id
        if (Schema::hasColumn('question_and_answer', 'status_id')) {
            DB::statement("
                UPDATE question_and_answer 
                SET status_id = CASE 
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'accepted' THEN 2
                    WHEN status = 'assigned' THEN 3
                    WHEN status = 'answered' THEN 4
                    WHEN status = 'approved' THEN 5
                    WHEN status = 'published' THEN 6
                    WHEN status = 'rejected' THEN 7
                    WHEN status = 'correction' THEN 8
                    ELSE 1
                END
                WHERE status_id IS NULL OR status_id = 0
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
