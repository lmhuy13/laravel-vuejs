<?php

namespace App\OpenApi\Endpoints;

/**
 * @OA\Get(
 *     path="/api/admin/teams",
 *     summary="Get all teams",
 *     tags={"Teams"},
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
 *         description="Search by name",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Paginated teams list",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
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
 *     path="/api/admin/teams",
 *     summary="Create new team",
 *     tags={"Teams"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Development Team"),
 *             @OA\Property(property="description", type="string", example="Team description"),
 *             @OA\Property(property="is_active", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=201, description="Team created")
 * )
 */

/**
 * @OA\Get(
 *     path="/api/admin/teams/{id}",
 *     summary="Get team by ID",
 *     tags={"Teams"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Team data")
 * )
 */

/**
 * @OA\Put(
 *     path="/api/admin/teams/{id}",
 *     summary="Update team",
 *     tags={"Teams"},
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
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="is_active", type="boolean")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Team updated")
 * )
 */

/**
 * @OA\Delete(
 *     path="/api/admin/teams/{id}",
 *     summary="Soft delete team",
 *     tags={"Teams"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="Team deleted")
 * )
 */

/**
 * @OA\Get(
 *     path="/api/admin/teams/{id}/members",
 *     summary="Get team members",
 *     tags={"Teams"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Team members list",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="email", type="string"),
 *                 @OA\Property(property="roles", type="array")
 *             )
 *         )
 *     )
 * )
 */

/**
 * @OA\Post(
 *     path="/api/admin/teams/{id}/members",
 *     summary="Add member to team",
 *     tags={"Teams"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id"},
 *             @OA\Property(property="user_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Member added")
 * )
 */

/**
 * @OA\Delete(
 *     path="/api/admin/teams/{teamId}/members/{userId}",
 *     summary="Remove member from team",
 *     tags={"Teams"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="teamId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="Member removed")
 * )
 */

/**
 * @OA\Post(
 *     path="/api/admin/teams/{id}/activate",
 *     summary="Activate team",
 *     tags={"Teams"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Team activated")
 * )
 */

/**
 * @OA\Post(
 *     path="/api/admin/teams/{id}/deactivate",
 *     summary="Deactivate team",
 *     tags={"Teams"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Team deactivated")
 * )
 */

class TeamDocumentation
{
}
