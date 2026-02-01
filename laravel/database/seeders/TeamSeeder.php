<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            [
                'name' => 'Default Team',
                'slug' => 'default-team',
                'description' => 'Default team for organization',
                'is_active' => true,
            ],
            [
                'name' => 'Development',
                'slug' => 'development',
                'description' => 'Development team',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'slug' => 'marketing',
                'description' => 'Marketing team',
                'is_active' => true,
            ],
            [
                'name' => 'Sales',
                'slug' => 'sales',
                'description' => 'Sales team',
                'is_active' => true,
            ],
            [
                'name' => 'Support',
                'slug' => 'support',
                'description' => 'Customer Support team',
                'is_active' => true,
            ],
        ];

        foreach ($teams as $team) {
            Team::firstOrCreate(
                ['slug' => $team['slug']],
                $team
            );
        }
    }
}
