<?php

namespace App\Rest\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'names' => $this->names,
            'email' => $this->email,
            'address' => $this->address,
            'country' => $this->country,
            'phone' => $this->phone,
            'status' => $this->status,           // New field
            'updated_by' => $this->updated_by,   // New field
            'deleted_by' => $this->deleted_by,   // New field
            'deleted_on' => $this->deleted_on,   // New field
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
