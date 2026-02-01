<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'admin']);
    }

    public function index(Request $request): JsonResponse
    {
        $per_page = $request->get('per_page', 15);
        $search = $request->get('search', '');

        $query = Role::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        }

        $roles = $query->orderBy('name')->paginate($per_page);

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

    public function show($id): JsonResponse
    {
        $role = Role::withTrashed()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $role,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255|unique:roles,name,' . $id,
            'slug' => 'string|max:255|unique:roles,slug,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $role->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $role,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ]);
    }

    public function restore($id): JsonResponse
    {
        $role = Role::withTrashed()->findOrFail($id);

        if (!$role->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'Role is not deleted',
            ], 400);
        }

        $role->restore();

        return response()->json([
            'success' => true,
            'message' => 'Role restored successfully',
            'data' => $role,
        ]);
    }

    public function forceDelete($id): JsonResponse
    {
        $role = Role::withTrashed()->findOrFail($id);
        $role->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Role permanently deleted',
        ]);
    }
}
