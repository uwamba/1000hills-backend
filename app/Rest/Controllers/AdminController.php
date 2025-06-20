<?php

namespace App\Rest\Controllers;

use App\Models\Admin;
use App\Models\AdminManage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Auth;
use App\Rest\Controller as RestController;

class AdminController extends RestController
{
    public function index()
    {
        $perPage = 10; // You can change this to any number or get it from query params
        $hotels = Admin::paginate($perPage);

        return response()->json($hotels, 200);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Fetch admin by email
        $admin = Admin::where('email', $request->email)->first();

        // Check if admin exists and password matches
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create personal access token
        $token = $admin->createToken('Admin API Token')->accessToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $admin,
            'managed_objects' => $admin->manages->map(function ($manage) {
                return [
                    'type' => class_basename($manage->object),
                    'id' => $manage->object_id,
                    'data' => $manage->manageable, // now this works
                ];
            }),
            'token' => $token,
        ]);
    }


    public function store(Request $request)
    {
        Log::debug('Admin creation request received.', $request->all());

        try {
            $validated = $request->validate([
                'names' => 'required|string',
                'email' => 'required|email|unique:admins',
                'address' => 'required|string',
                'phone' => 'required|string',
                'role' => 'required|string',
                'password' => 'required|string|min:6',
                'object' => 'required|string',
                'object_id' => 'required|string',
                'is_active' => 'required|boolean',

            ]);

            $admin = Admin::create([
                'names' => $validated['names'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
                'is_active' => $validated['is_active'] === 'true' || $validated['is_active'] == 1,
            ]);

            Log::debug('Admin created successfully.', ['admin_id' => $admin->id]);

            $adminManage = AdminManage::create([
                'admin_id' => $admin->id,
                'object' => $validated['object'],
                'object_id' => $validated['object_id'],
            ]);

            Log::debug('AdminManage record created.', ['admin_manage_id' => $adminManage->id]);

            return response()->json([
                'message' => 'Admin created and linked to object',
                'admin' => $admin
            ]);
        } catch (ValidationException $ve) {
            Log::warning('Validation failed during admin creation.', [
                'errors' => $ve->errors()
            ]);

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error during admin creation.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to create admin',
                'error' => $e->getMessage()
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
