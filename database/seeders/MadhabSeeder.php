<?php

namespace Database\Seeders;

use App\Models\Madhab;
use Illuminate\Database\Seeder;

class MadhabSeeder extends Seeder
{
    public function run(): void
    {
        $madhabs = [
            'Hanafi',
            'Shafii',
            'Maliki',
            'Hanbali',
            'General/Other',
        ];

        foreach ($madhabs as $name) {
            Madhab::firstOrCreate(['name' => $name]);
        }
    }
}
