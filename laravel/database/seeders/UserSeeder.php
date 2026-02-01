<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get default team
        $defaultTeam = Team::where('slug', 'default-team')->first();

        // Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'team_id' => $defaultTeam->id,
            ]
        );

        // Create profile for super admin
        Profile::firstOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'phone' => '+1-555-0100',
                'bio' => 'Super Administrator',
                'city' => 'New York',
                'country' => 'USA',
            ]
        );

        // Assign super admin role
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        UserRole::firstOrCreate(
            [
                'user_id' => $superAdmin->id,
                'role_id' => $superAdminRole->id,
                'team_id' => null,
            ]
        );

        // Create Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'team_id' => $defaultTeam->id,
            ]
        );

        // Create profile for admin
        Profile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'phone' => '+1-555-0101',
                'bio' => 'Team Administrator',
                'city' => 'New York',
                'country' => 'USA',
            ]
        );

        // Assign admin role
        $adminRole = Role::where('slug', 'admin')->first();
        UserRole::firstOrCreate(
            [
                'user_id' => $admin->id,
                'role_id' => $adminRole->id,
                'team_id' => $defaultTeam->id,
            ]
        );

        // Create Manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'team_id' => $defaultTeam->id,
            ]
        );

        // Create profile for manager
        Profile::firstOrCreate(
            ['user_id' => $manager->id],
            [
                'phone' => '+1-555-0102',
                'bio' => 'Team Manager',
                'city' => 'Los Angeles',
                'country' => 'USA',
            ]
        );

        // Assign manager role
        $managerRole = Role::where('slug', 'manager')->first();
        UserRole::firstOrCreate(
            [
                'user_id' => $manager->id,
                'role_id' => $managerRole->id,
                'team_id' => $defaultTeam->id,
            ]
        );

        // Create Developer user
        $developer = User::firstOrCreate(
            ['email' => 'developer@example.com'],
            [
                'name' => 'Developer',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'team_id' => $defaultTeam->id,
            ]
        );

        // Create profile for developer
        Profile::firstOrCreate(
            ['user_id' => $developer->id],
            [
                'phone' => '+1-555-0103',
                'bio' => 'Full Stack Developer',
                'city' => 'San Francisco',
                'country' => 'USA',
            ]
        );

        // Assign developer role
        $developerRole = Role::where('slug', 'developer')->first();
        UserRole::firstOrCreate(
            [
                'user_id' => $developer->id,
                'role_id' => $developerRole->id,
                'team_id' => $defaultTeam->id,
            ]
        );

        // Create regular user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'team_id' => $defaultTeam->id,
            ]
        );

        // Create profile for user
        Profile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => '+1-555-0104',
                'bio' => 'Regular user',
                'city' => 'Chicago',
                'country' => 'USA',
            ]
        );

        // Assign user role
        $userRole = Role::where('slug', 'user')->first();
        UserRole::firstOrCreate(
            [
                'user_id' => $user->id,
                'role_id' => $userRole->id,
                'team_id' => $defaultTeam->id,
            ]
        );
    }
}
