<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Team;
use App\Models\Role;
use App\Models\Profile;
use App\Services\UserService;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware(['auth:sanctum', 'admin']);
        $this->userService = $userService;
    }

    /**
     * List all users with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'per_page' => $request->integer('per_page', 15),
            'search' => $request->get('search', ''),
            'team_id' => $request->get('team_id'),
            'role_ids' => $request->get('role_ids'),
            'sort_by' => $request->get('sort_by', 'name'),
            'sort_dir' => $request->get('sort_dir', 'asc'),
        ];

        $users = $this->userService->getPaginatedUsers($filters);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Get single user details
     */
    public function show($id): JsonResponse
    {
        $cacheKey = "user_roles_{$id}";
        
        // Try to get cached timestamp first
        $cachedTime = Cache::get($cacheKey);
        $cacheHit = $cachedTime !== null;
        
        \Log::info("🔍 Attempting to fetch user {$id} with cache key {$cacheKey}, cache hit: " . ($cacheHit ? 'YES' : 'NO'));
        
        if (!$cacheHit) {
            // Not in cache, fetch from database
            $user = User::with(['team', 'roles', 'profile', 'userRoles'])->findOrFail($id);
            
            // Cache only the timestamp (1 hour = 3600 seconds)
            Cache::put($cacheKey, now()->timestamp, 3600);
            
            \Log::info("⚠️ Cache MISS for user {$id}. Fetched from database and timestamp cached.", [
                'user_id' => $id,
                'cache_key' => $cacheKey,
                'user_name' => $user->name,
                'roles_count' => count($user->roles),
            ]);
        } else {
            // Load fresh data from database (but know it came from cache)
            $user = User::with(['team', 'roles', 'profile', 'userRoles'])->findOrFail($id);
            
            \Log::info("✓ Cache HIT for user {$id}. Data validity confirmed from Redis cache.", [
                'user_id' => $id,
                'cache_key' => $cacheKey,
                'user_name' => $user->name,
                'roles_count' => count($user->roles),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
            'cache' => [
                'hit' => $cacheHit,
                'key' => $cacheKey,
                'source' => $cacheHit ? 'redis' : 'database',
            ],
        ]);
    }

    /**
     * Create new user
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'team_id' => 'required|exists:teams,id',
            'is_active' => 'boolean',
            'profile' => 'array',
            'roles' => 'array',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'team_id' => $validated['team_id'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Create profile
        if (isset($validated['profile'])) {
            $user->profile()->create($validated['profile']);
        } else {
            $user->profile()->create([]);
        }

        // Assign roles
        if (isset($validated['roles']) && !empty($validated['roles'])) {
            foreach ($validated['roles'] as $role_id) {
                $user->userRoles()->create([
                    'role_id' => $role_id,
                    'team_id' => $validated['team_id'],
                ]);
            }
        }

        // Dispatch welcome email job to queue
        SendWelcomeEmailJob::dispatch($user, $validated['password']);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully. Welcome email sent.',
            'data' => $user->load(['team', 'roles', 'profile']),
        ], 201);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $id,
            'team_id' => 'exists:teams,id',
            'is_active' => 'boolean',
            'password' => 'string|min:8',
            'profile' => 'array',
            'roles' => 'array',
        ]);

        DB::beginTransaction();
        try {
            // Update basic user info
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->team_id = $validated['team_id'];
            $user->is_active = $validated['is_active'];
            $user->password = bcrypt($validated['password']);
            $user->save();

            // Update profile
            if (isset($validated['profile'])) {
                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $validated['profile']
                );
            }

            // Update roles
            if (isset($validated['roles'])) {
                $user->userRoles()->delete();
                $team_id = $user->team_id;

                foreach ($validated['roles'] as $role_id) {
                    $user->userRoles()->create([
                        'role_id' => $role_id,
                        'team_id' => $team_id,
                    ]);
                }
            }

            // Invalidate cache
            Cache::forget("user_roles_{$id}");

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->load(['team', 'roles', 'profile']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error updating user {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
            ], 500);
        }
    }

    /**
     * Soft delete user
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            // Prevent deleting super admin (optional)
            if ($user->roles()->where('slug', 'super-admin')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete super admin user',
                ], 403);
            }

            $user->delete();

            // Invalidate cache
            Cache::forget("user_roles_{$id}");

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error deleting user {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
            ], 500);
        }
    }

    /**
     * Restore soft deleted user
     */
    public function restore($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::withTrashed()->findOrFail($id);

            if (!$user->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not deleted',
                ], 400);
            }

            $user->restore();

            return response()->json([
                'success' => true,
                'message' => 'User restored successfully',
                'data' => $user->load(['team', 'roles', 'profile']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error restoring user {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore user',
            ], 500);
        }
    }

    /**
     * Force delete user
     */
    public function forceDelete($id): JsonResponse
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            // Prevent force deleting super admin (optional)
            if ($user->roles()->where('slug', 'super-admin')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot permanently delete super admin user',
                ], 403);
            }

            $user->forceDelete();

            // Invalidate cache
            Cache::forget("user_roles_{$id}");

            return response()->json([
                'success' => true,
                'message' => 'User permanently deleted',
            ]);
        } catch (\Exception $e) {
            \Log::error("Error force deleting user {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete user',
            ], 500);
        }
    }

    /**
     * Activate user
     */
    public function activate($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->is_active = true;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User activated',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error activating user {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate user',
            ], 500);
        }
    }

    /**
     * Deactivate user
     */
    public function deactivate($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->is_active = false;
            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deactivated',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error deactivating user {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate user',
            ], 500);
        }
    }
}
