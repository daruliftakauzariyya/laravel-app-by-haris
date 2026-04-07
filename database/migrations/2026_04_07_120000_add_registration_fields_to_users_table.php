<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('country_code')->nullable()->after('mobile');
            $table->text('address_line_1')->nullable()->after('country');
            $table->text('address_line_2')->nullable()->after('address_line_1');
            $table->string('state')->nullable()->after('address_line_2');
            $table->string('district')->nullable()->after('state');
            $table->enum('madhab', ['Hanafi', 'Shafii', 'Maliki', 'Hanbali', 'Other', 'No Preference'])->nullable()->after('district');
            $table->string('otp')->nullable()->after('remember_token');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'country_code',
                'address_line_1',
                'address_line_2',
                'state',
                'district',
                'madhab',
                'otp',
                'otp_expires_at'
            ]);
        });
    }
};
