<?php

namespace Database\Seeders;

use App\Models\Visibility;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisibilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $visibilities = ['public', 'private', 'followers'];

        foreach ($visibilities as $visibility) {
            Visibility::firstOrCreate(['name' => $visibility]);
        }
    }
}
