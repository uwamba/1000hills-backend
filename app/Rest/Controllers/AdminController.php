<?php

namespace App\Rest\Controllers;

use App\Models\Admin;
use App\Models\AdminManage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Rest\Controller as RestController;

class AdminController extends RestController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if (!$admin->is_active) {
            return response()->json(['error' => 'Account is deactivated'], 403);
        }

        // Normally generate token here
        return response()->json(['admin' => $admin]);
    }

    public function store(Request $request)
{
    Log::debug('Admin creation request received.', $request->all());

    $request->validate([
        'names'     => 'required|string',
        'email'     => 'required|email|unique:admins',
        'address'   => 'required|string',
        'phone'     => 'required|string',
        'role'      => 'required|string',
        'password'  => 'required|string|min:6',
        'object'    => 'required|string',
        'object_id' => 'required|string',
        'is_active' => 'required|string',
    ]);

    try {
        $admin = Admin::create([
            'names'     => $request->names,
            'email'     => $request->email,
            'address'   => $request->address,
            'phone'     => $request->phone,
            'role'      => $request->role,
            'password'  => Hash::make($request->password), // convert string to bool
        ]);

        Log::debug('Admin created successfully.', ['admin_id' => $admin->id]);

        $adminManage = AdminManage::create([
            'admin_id'  => $admin->id,
            'object'    => $request->object,
            'object_id' => $request->object_id,
        ]);

        Log::debug('AdminManage record created.', ['admin_manage_id' => $adminManage->id]);

        return response()->json([
            'message' => 'Admin created and linked to object',
            'admin'   => $admin
        ]);
    } catch (\Exception $e) {
        Log::error('Error creating admin or linking object.', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'message' => 'Failed to create admin',
            'error'   => $e->getMessage()
        ], 500);
    }
}
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $admin->update($request->only(['names', 'email', 'address', 'phone', 'role']));

        return response()->json(['message' => 'Admin updated', 'admin' => $admin]);
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return response()->json(['message' => 'Admin deleted']);
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        $admin = Admin::findOrFail($id);
        $admin->password = Hash::make($request->password);
        $admin->save();

        return response()->json(['message' => 'Password reset successfully']);
    }

    public function activate($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->is_active = true;
        $admin->save();

        return response()->json(['message' => 'Admin account activated']);
    }

    public function deactivate($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->is_active = false;
        $admin->save();

        return response()->json(['message' => 'Admin account deactivated']);
    }
}
