<?php

namespace App\OpenApi\Endpoints;

/**
 * @OA\Get(
 *     path="/api/admin/users",
 *     summary="Get paginated users with filters",
 *     tags={"Users"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         required=false,
 *         @OA\Schema(type="integer", default=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Records per page (max 100)",
 *         required=false,
 *         @OA\Schema(type="integer", default=15)
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search by name or email",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="team_id",
 *         in="query",
 *         description="Filter by team ID",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="role_ids",
 *         in="query",
 *         description="Filter by role IDs (comma-separated)",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="sort_by",
 *         in="query",
 *         description="Sort field (name, email, team, role)",
 *         required=false,
 *         @OA\Schema(type="string", default="name")
 *     ),
 *     @OA\Parameter(
 *         name="sort_dir",
 *         in="query",
 *         description="Sort direction (asc, desc)",
 *         required=false,
 *         @OA\Schema(type="string", default="asc")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Paginated users list",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="email", type="string"),
 *                     @OA\Property(property="team", type="object"),
 *                     @OA\Property(property="roles", type="array"),
 *                     @OA\Property(property="status", type="string")
 *                 )
 *             ),
 *             @OA\Property(property="current_page", type="integer"),
 *             @OA\Property(property="last_page", type="integer"),
 *             @OA\Property(property="total", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */

/**
 * @OA\Post(
 *     path="/api/admin/users",
 *     summary="Create new user",
 *     tags={"Users"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="team_id", type="integer", example=1),
 *             @OA\Property(property="status", type="string", enum={"active","inactive"}, example="active")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string")
 *         )
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/api/admin/users/{id}",
 *     summary="Get user by ID",
 *     tags={"Users"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User data",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="team", type="object"),
 *             @OA\Property(property="roles", type="array"),
 *             @OA\Property(property="profile", type="object")
 *         )
 *     ),
 *     @OA\Response(response=404, description="User not found")
 * )
 */

/**
 * @OA\Put(
 *     path="/api/admin/users/{id}",
 *     summary="Update user",
 *     tags={"Users"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="team_id", type="integer"),
 *             @OA\Property(property="status", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="User updated")
 * )
 */

/**
 * @OA\Delete(
 *     path="/api/admin/users/{id}",
 *     summary="Soft delete user",
 *     tags={"Users"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="User deleted")
 * )
 */

/**
 * @OA\Post(
 *     path="/api/admin/users/{id}/activate",
 *     summary="Activate user",
 *     tags={"Users"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="User activated")
 * )
 */

/**
 * @OA\Post(
 *     path="/api/admin/users/{id}/deactivate",
 *     summary="Deactivate user",
 *     tags={"Users"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="User deactivated")
 * )
 */

/**
 * @OA\Delete(
 *     path="/api/admin/users/{id}/force",
 *     summary="Force delete user (permanent)",
 *     tags={"Users"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="User permanently deleted")
 * )
 */

class UserDocumentation
{
}
