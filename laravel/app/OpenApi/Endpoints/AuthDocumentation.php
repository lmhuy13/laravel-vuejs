<?php

namespace App\OpenApi\Endpoints;

/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Login user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="user", type="object")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Invalid credentials")
 * )
 */

/**
 * @OA\Get(
 *     path="/api/me",
 *     summary="Get authenticated user",
 *     tags={"Authentication"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User data",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="team_id", type="integer"),
 *             @OA\Property(property="status", type="string"),
 *             @OA\Property(property="roles", type="array")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */

/**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Logout user",
 *     tags={"Authentication"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logout successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */

class AuthDocumentation
{
}
