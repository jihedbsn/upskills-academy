<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementService
{
    public function index(Request $request): array
    {
        $query = Admin::with('roleRelation');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = $request->get('per_page', 15);
        $users   = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        $roles   = Role::where('is_active', true)->orderBy('display_name')->get();

        return compact('users', 'roles');
    }

    public function getRoles()
    {
        return Role::where('is_active', true)->orderBy('display_name')->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:admins,email',
            'password'   => 'required|string|min:8|confirmed',
            'phone'      => 'nullable|string|max:20',
            'role_id'    => 'required|exists:roles,id',
            'is_active'  => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Admin::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'phone'      => $request->phone,
            'role_id'    => $request->role_id,
            'is_active'  => $request->has('is_active'),
        ]);

        $role       = Role::find($request->role_id);
        $user->role = $role->name;
        $user->save();

        return redirect()->route('user-management.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, Admin $user)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:admins,email,' . $user->id,
            'password'   => 'nullable|string|min:8|confirmed',
            'phone'      => 'nullable|string|max:20',
            'role_id'    => 'required|exists:roles,id',
            'is_active'  => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $currentAdminId = Auth::id();

        $data = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'role_id'    => $request->role_id,
        ];

        if ($user->isSuperAdmin() || ($currentAdminId && $user->id === $currentAdminId)) {
            $data['is_active'] = $user->is_active;
        } else {
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $role         = Role::find($request->role_id);
        $data['role'] = $role->name;

        $user->update($data);

        return redirect()->route('user-management.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Admin $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete super admin user.');
        }

        $currentAdminId = Auth::id();
        if ($currentAdminId && $user->id === $currentAdminId) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('user-management.index')->with('success', 'User deleted successfully.');
    }

    public function assignRole(Request $request, Admin $user)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $role          = Role::find($request->role_id);
        $user->role_id = $request->role_id;
        $user->role    = $role->name;
        $user->save();

        return response()->json([
            'success'   => true,
            'message'   => 'Role assigned successfully',
            'role_name' => $role->display_name,
        ]);
    }

    public function toggleStatus(Admin $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'success'   => true,
            'is_active' => $user->is_active,
            'message'   => $user->is_active ? 'User activated' : 'User deactivated',
        ]);
    }
}
