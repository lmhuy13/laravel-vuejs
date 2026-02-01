<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     title="Laravel Vue Admin API",
 *     version="1.0.0",
 *     description="API Documentation for Laravel Vue Admin Application",
 *     contact={
 *         "name": "Support",
 *         "email": "support@example.com"
 *     },
 *     license={
 *         "name": "MIT"
 *     }
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="sanctumAuth"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication endpoints"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="User management endpoints"
 * )
 * @OA\Tag(
 *     name="Teams",
 *     description="Team management endpoints"
 * )
 * @OA\Tag(
 *     name="Roles",
 *     description="Role management endpoints"
 * )
 */
class ApiDocumentation
{
    //
}
