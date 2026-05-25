<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'admin']);
    }

    /**
     * List all teams with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', '');

        $query = Team::with(['users' => function ($q) {
            $q->count();
        }]);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
        }

        $teams = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $teams,
        ]);
    }

    /**
     * Get single team details
     */
    public function show($id): JsonResponse
    {
        $team = Team::with(['users', 'roles'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }

    /**
     * Create new team
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
            'slug' => 'required|string|max:255|unique:teams,slug',
            'description' => 'nullable|string',
            'logo' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $team = Team::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'logo' => $validated['logo'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Team created successfully',
                'data' => $team,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error creating team: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create team',
            ], 500);
        }
    }

    /**
     * Update team
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255|unique:teams,name,' . $id,
            'slug' => 'string|max:255|unique:teams,slug,' . $id,
            'description' => 'nullable|string',
            'logo' => 'nullable|url',
            'is_active' => 'boolean',
        ]);
        $team = Team::findOrFail($id);
        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $team->update($validated);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Team updated successfully',
                'data' => $team,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error updating team {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update team',
            ], 500);
        }
    }

    /**
     * Soft delete team
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $team = Team::findOrFail($id);

            // Check if team has users
            if ($team->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete team with active users. Please remove all users first.',
                ], 400);
            }

            $team->delete();

            return response()->json([
                'success' => true,
                'message' => 'Team deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error deleting team {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete team',
            ], 500);
        }
            
    }

    /**
     * Restore soft deleted team
     */
    public function restore($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $team = Team::withTrashed()->findOrFail($id);

            if (!$team->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team is not deleted',
                ], 400);
            }

            $team->restore();

            return response()->json([
                'success' => true,
                'message' => 'Team restored successfully',
                'data' => $team,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error restoring team {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore team',
            ], 500);
        }
    }

    /**
     * Force delete team
     */
    public function forceDelete($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $team = Team::withTrashed()->findOrFail($id);
            $team->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Team permanently deleted',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error permanently deleting team {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete team',
            ], 500);
        }
    }

    /**
     * Get team members
     */
    public function getMembers($id): JsonResponse
    {
        $team = Team::findOrFail($id);
        $perPage = 15;
        $members = $team->users()->with('profile', 'roles')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }

    /**
     * Add user to team
     */
    public function addMember(Request $request, $id): JsonResponse
    {
        $team = Team::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'array',
        ]);

        DB::beginTransaction();
        try {
            // Check if user already in team
            $user = $team->users()->where('user_id', $validated['user_id'])->first();
            if ($user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already in this team',
                ], 400);
            }

            // Update user's team
            $team->users()->syncWithoutDetaching([$validated['user_id']]);

            // Assign roles if provided
            if (isset($validated['roles']) && !empty($validated['roles'])) {
                foreach ($validated['roles'] as $role_id) {
                    \App\Models\UserRole::create([
                        'user_id' => $validated['user_id'],
                        'role_id' => $role_id,
                        'team_id' => $id,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Member added to team',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error adding member to team {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add member to team',
            ], 500);
        }
    }

    /**
     * Remove user from team
     */
    public function removeMember($teamId, $userId): JsonResponse
    {
        $team = Team::findOrFail($teamId);
        DB::beginTransaction();
        try {

            // Delete user roles in this team
            UserRole::where('user_id', $userId)
                ->where('team_id', $teamId)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Member removed from team',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error removing member from team {$teamId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove member from team',
            ], 500);
        }
    }

    /**
     * Activate team
     */
    public function activate($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $team = Team::findOrFail($id);
            $team->is_active = true;
            $team->save();

            return response()->json([
                'success' => true,
                'message' => 'Team activated',
                'data' => $team,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error activating team {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate team',
            ], 500);
        }
    }

    /**
     * Deactivate team
     */
    public function deactivate($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $team = Team::findOrFail($id);
            $team->is_active = false;
            $team->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Team deactivated',
                'data' => $team,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error deactivating team {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate team',
            ], 500);
        }
    }
}
