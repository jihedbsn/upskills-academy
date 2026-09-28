<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\UserManagementService;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct(private UserManagementService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('user-management.index', $data);
    }

    public function create()
    {
        $roles = $this->service->getRoles();
        return view('user-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(Admin $user)
    {
        $user->load('roleRelation', 'activities');
        return view('user-management.show', compact('user'));
    }

    public function edit(Admin $user)
    {
        $roles = $this->service->getRoles();
        return view('user-management.edit', compact('user', 'roles'));
    }

    public function update(Request $request, Admin $user)
    {
        return $this->service->update($request, $user);
    }

    public function destroy(Admin $user)
    {
        return $this->service->destroy($user);
    }

    public function assignRole(Request $request, Admin $user)
    {
        return $this->service->assignRole($request, $user);
    }

    public function toggleStatus(Admin $user)
    {
        return $this->service->toggleStatus($user);
    }
}
