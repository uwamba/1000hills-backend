<?php

namespace App\Rest\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminManageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'admin_id' => $this->admin_id,
            'object_type' => $this->object_type,
            'object_id' => $this->object_id,
            'role_id' => $this->role_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
