<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Admin;
use App\Rest\Resources\AdminResource;
use Illuminate\Http\Request;

class AdminController extends RestController
{
    public function index()
    {
        $admins = Admin::all();
        return AdminResource::collection($admins);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'role' => 'required|string|max:50',
        ]);

        $admin = Admin::create($validated);
        return new AdminResource($admin);
    }

    public function show(Admin $admin)
    {
        return new AdminResource($admin);
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:admins,email,' . $admin->id,
            'address' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'role' => 'sometimes|required|string|max:50',
        ]);

        $admin->update($validated);
        return new AdminResource($admin);
    }

    public function destroy($admin)
    {
        $admin->delete();
        return response()->json(null, 204);
    }
}
