<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Madhab;
use App\Models\User;
use App\Models\Question;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add columns
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('madhab_id')->nullable()->after('category_id')->constrained('madhabs')->onDelete('set null');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('madhab_id')->nullable()->after('district')->constrained('madhabs')->onDelete('set null');
        });

        // Ensure table is seeded
        if (Madhab::count() === 0) {
            $seeder = new \Database\Seeders\MadhabSeeder();
            $seeder->run();
        }

        // 2. Map existing data
        $hanafi = Madhab::where('name', 'Hanafi')->first();
        $shafii = Madhab::where('name', 'Shafii')->first();
        $maliki = Madhab::where('name', 'Maliki')->first();
        $hanbali = Madhab::where('name', 'Hanbali')->first();
        $general = Madhab::where('name', 'General/Other')->first();

        $mapping = [
            'Hanafi' => $hanafi?->id,
            'Shafii' => $shafii?->id,
            'Maliki' => $maliki?->id,
            'Hanbali' => $hanbali?->id,
            'Other' => $general?->id,
            'No Preference' => $general?->id,
        ];

        // Migrate Questions
        $questions = DB::table('questions')->select('id', 'madhab')->get();
        foreach ($questions as $q) {
            $mid = $mapping[$q->madhab] ?? $general?->id;
            DB::table('questions')->where('id', $q->id)->update(['madhab_id' => $mid]);
        }

        // Migrate Users
        $users = DB::table('users')->select('id', 'madhab')->get();
        foreach ($users as $u) {
            if (empty($u->madhab)) continue;

            $mValue = json_decode($u->madhab, true);
            
            if (is_array($mValue)) {
                // For Muftis (multiple)
                foreach ($mValue as $mName) {
                    $mid = $mapping[$mName] ?? $general?->id;
                    if ($mid) {
                        DB::table('madhab_user')->insert([
                            'user_id' => $u->id,
                            'madhab_id' => $mid,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } else {
                // For regular users (single string)
                $mid = $mapping[$u->madhab] ?? $general?->id;
                DB::table('users')->where('id', $u->id)->update(['madhab_id' => $mid]);
            }
        }

        // 3. Drop old columns
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('madhab');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('madhab');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('madhab')->nullable();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->enum('madhab', ['Hanafi', 'Shafii', 'Maliki', 'Hanbali', 'Other'])->default('Hanafi');
        });

        // Reverting data would be complex and lossy for muftis, usually we don't do it unless necessary
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('madhab_id');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('madhab_id');
        });
    }
};
