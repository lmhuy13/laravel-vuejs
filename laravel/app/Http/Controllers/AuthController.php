<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->load(['team', 'roles', 'profile']);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $user->createToken('api-token')->plainTextToken,
                'user' => $user,
            ],
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $userId = $request->user()->id;
        $cacheKey = "user_roles_{$userId}";
        
        // Check if data is in cache
        $isCacheHit = Cache::has($cacheKey);
        
        \Log::info("1 👤 Fetching authenticated user {$userId} with cache key {$cacheKey}, cache hit: " . ($isCacheHit ? 'YES' : 'NO'));
        
        // Load fresh user data
        $user = $request->user()->load(['team', 'roles', 'profile']);
        
        // If not cached yet, store it
        if (!$isCacheHit) {
            Cache::put($cacheKey, now()->timestamp, 3600);
            \Log::info("⚠️ Cache MISS - storing timestamp for user {$userId}");
        } else {
            \Log::info("✓ Cache HIT - user {$userId} data validated from cache");
        }

        return response()->json([
            'success' => true,
            'data' => $user,
            'cache' => [
                'hit' => $isCacheHit,
                'key' => $cacheKey,
                'message' => $isCacheHit ? 'Fetched from Redis cache' : 'Fetched from database',
            ],
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
