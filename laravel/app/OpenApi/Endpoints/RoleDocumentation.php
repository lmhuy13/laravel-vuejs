<?php

namespace App\OpenApi\Endpoints;

/**
 * @OA\Get(
 *     path="/api/admin/roles",
 *     summary="Get all roles",
 *     tags={"Roles"},
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
 *         description="Records per page",
 *         required=false,
 *         @OA\Schema(type="integer", default=15)
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search by name or slug",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Paginated roles list",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="slug", type="string"),
 *                     @OA\Property(property="description", type="string"),
 *                     @OA\Property(property="is_active", type="boolean")
 *                 )
 *             ),
 *             @OA\Property(property="current_page", type="integer"),
 *             @OA\Property(property="last_page", type="integer")
 *         )
 *     )
 * )
 */

/**
 * @OA\Post(
 *     path="/api/admin/roles",
 *     summary="Create new role",
 *     tags={"Roles"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","slug"},
 *             @OA\Property(property="name", type="string", example="Editor"),
 *             @OA\Property(property="slug", type="string", example="editor"),
 *             @OA\Property(property="description", type="string", example="Editor role"),
 *             @OA\Property(property="is_active", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=201, description="Role created")
 * )
 */

/**
 * @OA\Get(
 *     path="/api/admin/roles/{id}",
 *     summary="Get role by ID",
 *     tags={"Roles"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Role data"),
 *     @OA\Response(response=404, description="Role not found")
 * )
 */

/**
 * @OA\Put(
 *     path="/api/admin/roles/{id}",
 *     summary="Update role",
 *     tags={"Roles"},
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
 *             @OA\Property(property="slug", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="is_active", type="boolean")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Role updated")
 * )
 */

/**
 * @OA\Delete(
 *     path="/api/admin/roles/{id}",
 *     summary="Soft delete role",
 *     tags={"Roles"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="Role deleted")
 * )
 */

/**
 * @OA\Delete(
 *     path="/api/admin/roles/{id}/force",
 *     summary="Force delete role (permanent)",
 *     tags={"Roles"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="Role permanently deleted")
 * )
 */

class RoleDocumentation
{
}
