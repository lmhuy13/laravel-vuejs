<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'admin']);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', '');

        $query = Role::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        }

        $roles = $query->orderBy('name')->paginate($perPage);

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

        DB::beginTransaction();
        try {
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
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error creating role: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role',
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        if($role->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update a deleted role',
            ], 400);
        }

        $validated = $request->validate([
            'name' => 'string|max:255|unique:roles,name,' . $id,
            'slug' => 'string|max:255|unique:roles,slug,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
         try {
             $role->update($validated);

             return response()->json([
                 'success' => true,
                 'message' => 'Role updated successfully',
                 'data' => $role,
             ]);
         } catch (\Exception $e) {
             DB::rollBack();
             \Log::error("Error updating role {$id}: " . $e->getMessage());
             return response()->json([
                 'success' => false,
                 'message' => 'Failed to update role',
             ], 500);
         }
    }

    public function destroy($id): JsonResponse
    {
        try {
             DB::beginTransaction();
             $role = Role::findOrFail($id);
             if($role->trashed()) {
                 return response()->json([
                     'success' => false,
                     'message' => 'Role is already deleted',
                 ], 400);
             }
             $role->delete();
             DB::commit();

             return response()->json([
                 'success' => true,
                 'message' => 'Role deleted successfully',
             ]);
         } catch (\Exception $e) {
             DB::rollBack();
             \Log::error("Error deleting role {$id}: " . $e->getMessage());
             return response()->json([
                 'success' => false,
                 'message' => 'Failed to delete role',
             ], 500);
        }
    }

    public function restore($id): JsonResponse
    {
        DB::beginTransaction();
        try {
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
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error restoring role {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore role',
            ], 500);
        }
    }

    public function forceDelete($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $role = Role::withTrashed()->findOrFail($id);
            $role->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Role permanently deleted',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error permanently deleting role {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete role',
            ], 500);
        }
    }
}
