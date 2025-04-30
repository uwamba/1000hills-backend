<?php

namespace App\Rest\Resources;

use App\Models\Agency;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'description' => $this->description,
            'status' => $this->status,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'deleted_on' => $this->deleted_on,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
