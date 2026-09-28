<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(private RoleService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('roles.index', $data);
    }

    public function create()
    {
        $data = $this->service->getCreateData();
        return view('roles.create', $data);
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(Role $role)
    {
        $role = $this->service->show($role);
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $data = $this->service->getEditData($role);
        return view('roles.edit', $data);
    }

    public function update(Request $request, Role $role)
    {
        return $this->service->update($request, $role);
    }

    public function destroy(Role $role)
    {
        return $this->service->destroy($role);
    }

    public function assignToAdmin(Request $request)
    {
        return $this->service->assignToAdmin($request);
    }
}
