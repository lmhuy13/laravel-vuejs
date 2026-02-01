<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Super Administrator with full access',
                'is_active' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with team access',
                'is_active' => true,
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manager role for team management',
                'is_active' => true,
            ],
            [
                'name' => 'Team Lead',
                'slug' => 'team-lead',
                'description' => 'Team Lead role',
                'is_active' => true,
            ],
            [
                'name' => 'Developer',
                'slug' => 'developer',
                'description' => 'Developer role',
                'is_active' => true,
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Standard user role',
                'is_active' => true,
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Viewer role with read-only access',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
