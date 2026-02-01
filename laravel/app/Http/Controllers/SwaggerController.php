<?php

namespace App\Http\Controllers;

class SwaggerController extends Controller
{
    /**
     * Get OpenAPI specification
     * 
     * Loads OpenAPI spec from public/openapi.json
     * Documentation annotations are in app/OpenApi/ directory
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOpenApiSpec()
    {
        $spec = json_decode(file_get_contents(public_path('openapi.json')), true);
        return response()->json($spec);
    }
}
