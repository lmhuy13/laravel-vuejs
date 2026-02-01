<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SwaggerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\RoleController;

// Authentication routes - no auth required
Route::post('login', [AuthController::class, 'login']);

// Protected routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

// Admin routes - require authentication and admin role
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Role management
    Route::apiResource('roles', RoleController::class);
    Route::delete('roles/{id}/restore', [RoleController::class, 'restore']);
    Route::delete('roles/{id}/force', [RoleController::class, 'forceDelete']);

    // User management
    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/activate', [UserController::class, 'activate']);
    Route::post('users/{id}/deactivate', [UserController::class, 'deactivate']);
    Route::post('users/{id}/restore', [UserController::class, 'restore']);
    Route::delete('users/{id}/force', [UserController::class, 'forceDelete']);

    // Team management
    Route::apiResource('teams', TeamController::class);
    Route::get('teams/{id}/members', [TeamController::class, 'getMembers']);
    Route::post('teams/{id}/members', [TeamController::class, 'addMember']);
    Route::delete('teams/{teamId}/members/{userId}', [TeamController::class, 'removeMember']);
    Route::post('teams/{id}/activate', [TeamController::class, 'activate']);
    Route::post('teams/{id}/deactivate', [TeamController::class, 'deactivate']);
    Route::delete('teams/{id}/restore', [TeamController::class, 'restore']);
    Route::delete('teams/{id}/force', [TeamController::class, 'forceDelete']);
});

// Swagger/API Documentation
Route::get('openapi.json', [SwaggerController::class, 'getOpenApiSpec']);
Route::get('docs', function () {
    return view('swagger');
})->name('api.docs');
