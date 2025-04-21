<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\AdminManage;
use App\Rest\Resources\AdminManageResource;
use Illuminate\Http\Request;

class AdminManageController extends RestController
{
    public function index()
    {
        $adminManages = AdminManage::all();
        return AdminManageResource::collection($adminManages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'object_type' => 'required|string|max:255',
            'object_id' => 'required|integer',
            'role_id' => 'required|exists:roles,id',
        ]);

        $adminManage = AdminManage::create($validated);
        return new AdminManageResource($adminManage);
    }

    public function show(AdminManage $adminManage)
    {
        return new AdminManageResource($adminManage);
    }

    public function update(Request $request, AdminManage $adminManage)
    {
        $validated = $request->validate([
            'admin_id' => 'sometimes|required|exists:admins,id',
            'object_type' => 'sometimes|required|string|max:255',
            'object_id' => 'sometimes|required|integer',
            'role_id' => 'sometimes|required|exists:roles,id',
        ]);

        $adminManage->update($validated);
        return new AdminManageResource($adminManage);
    }

    public function destroy( $adminManage)
    {
        $adminManage->delete();
        return response()->json(null, 204);
    }
}
