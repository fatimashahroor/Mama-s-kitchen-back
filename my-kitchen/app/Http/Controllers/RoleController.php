<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use DB;

class RoleController extends Controller
{

    /**
     * Constructor
     *
     * Setup middleware for this controller.
     * 
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:role-list');
         $this->middleware('permission:role-create', ['only' => ['store']]);
         $this->middleware('permission:role-edit', ['only' => ['update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $roles = Role::orderBy('id','DESC')->paginate(5);
        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        $role->permissions;

        return response()->json(['role' => $role]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->update(['name' => $request->input('name')]);
        $role->givePermissionTo($request->input('permission'));

        return response()->json(['message' => 'Role updated successfully', 'role' => $role]);
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
