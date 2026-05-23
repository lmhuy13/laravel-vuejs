<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected UserService $userService;

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
        $cachedTime = Cache::get($cacheKey);
        $cacheHit = $cachedTime !== null;

        \Log::info("🔍 Attempting to fetch user {$id} with cache key {$cacheKey}, cache hit: " . ($cacheHit ? 'YES' : 'NO'));

        $user = $this->userService->getUserDetails($id);

        if (!$cacheHit) {
            Cache::put($cacheKey, now()->timestamp, 3600);
            \Log::info("⚠️ Cache MISS for user {$id}. Fetched from database and timestamp cached.", [
                'user_id' => $id,
                'cache_key' => $cacheKey,
                'user_name' => $user->name,
                'roles_count' => count($user->roles),
            ]);
        } else {
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

        DB::beginTransaction();
        try {
            $user = $this->userService->createUserWithRelations($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully. Welcome email sent.',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error creating user: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user. Please try again.',
            ], 500);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, $id): JsonResponse
    {
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
            $user = $this->userService->updateUserWithRelations($id, $validated);

        Cache::forget("user_roles_{$id}");

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
        } catch (\Exception $e) {
            \Log::error("Error updating user {$id}: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user. Please try again.',
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
            $this->userService->deleteUser($id);
            Cache::forget("user_roles_{$id}");
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error deleting user {$id}: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user. Please try again.',
            ], 500);
        }

    }

    /**
     * Restore soft deleted user
     */
    public function restore($id): JsonResponse
    {
        $user = $this->userService->restoreUser($id);

        return response()->json([
            'success' => true,
            'message' => 'User restored successfully',
            'data' => $user,
        ]);
    }

    /**
     * Force delete user
     */
    public function forceDelete($id): JsonResponse
    {
        $this->userService->forceDeleteUser($id);

        return response()->json([
            'success' => true,
            'message' => 'User permanently deleted',
        ]);
    }

    /**
     * Activate user
     */
    public function activate($id): JsonResponse
    {
        $user = $this->userService->activateUser($id);

        return response()->json([
            'success' => true,
            'message' => 'User activated',
            'data' => $user,
        ]);
    }

    /**
     * Deactivate user
     */
    public function deactivate($id): JsonResponse
    {
        $user = $this->userService->deactivateUser($id);

        return response()->json([
            'success' => true,
            'message' => 'User deactivated',
            'data' => $user,
        ]);
    }
}
