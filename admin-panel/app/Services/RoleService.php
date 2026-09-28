<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleService
{
    public function index(Request $request): array
    {
        $query = Role::with('permissions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = $request->get('per_page', 15);
        $roles   = $query->orderBy('name')->paginate($perPage)->appends($request->except('page'));

        return compact('roles');
    }

    public function getCreateData(): array
    {
        return ['permissions' => Permission::getGrouped()];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255|unique:roles,name',
            'display_name'   => 'required|string|max:255',
            'description'    => 'nullable|string',
            'is_active'      => 'boolean',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role = Role::create([
            'name'         => $request->name,
            'display_name' => $request->display_name,
            'description'  => $request->description,
            'is_active'    => $request->has('is_active'),
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function show(Role $role): Role
    {
        $role->load('permissions', 'admins');
        return $role;
    }

    public function getEditData(Role $role): array
    {
        return [
            'role'            => $role,
            'permissions'     => Permission::getGrouped(),
            'rolePermissions' => $role->permissions->pluck('id')->toArray(),
        ];
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name'   => 'required|string|max:255',
            'description'    => 'nullable|string',
            'is_active'      => 'boolean',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role->update([
            'name'         => $request->name,
            'display_name' => $request->display_name,
            'description'  => $request->description,
            'is_active'    => $request->has('is_active'),
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->admins()->count() > 0 || $role->webMasters()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role that is assigned to users.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function assignToAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:admins,id',
            'role_id'  => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $admin          = Admin::findOrFail($request->admin_id);
        $admin->role_id = $request->role_id;
        $admin->save();

        return response()->json(['success' => true, 'message' => 'Role assigned successfully']);
    }
}
