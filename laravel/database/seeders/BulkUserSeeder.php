<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class BulkUserSeeder extends Seeder
{
    public function run(): void
    {
        $count = (int) env('BULK_USERS_COUNT', 10000);
        if ($count <= 0) {
            return;
        }

        $teamIds = Team::query()->pluck('id')->all();
        if (empty($teamIds)) {
            $this->command?->warn('BulkUserSeeder: no teams found; skipping bulk users.');
            return;
        }

        // Exclude super-admin from random assignment
        $roleIds = Role::query()
            ->where('slug', '!=', 'super-admin')
            ->pluck('id')
            ->all();

        $chunkSize = 1000;
        $remaining = $count;

        while ($remaining > 0) {
            $batchCount = min($chunkSize, $remaining);

            $users = User::factory()
                ->count($batchCount)
                ->state(function () use ($teamIds) {
                    return [
                        'team_id' => $teamIds[array_rand($teamIds)],
                        'is_active' => fake()->boolean(90),
                    ];
                })
                ->create();

            if (!empty($roleIds)) {
                $userRoleRows = [];
                $now = now();

                foreach ($users as $user) {
                    // Assign 1-2 random roles
                    $rolesToAssign = (int) fake()->numberBetween(1, 2);
                    $pickedRoleIds = collect($roleIds)->random($rolesToAssign)->all();

                    foreach ($pickedRoleIds as $roleId) {
                        $userRoleRows[] = [
                            'user_id' => $user->id,
                            'role_id' => $roleId,
                            'team_id' => $user->team_id,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if (!empty($userRoleRows)) {
                    UserRole::query()->insert($userRoleRows);
                }
            }

            $remaining -= $batchCount;
        }
    }
}
