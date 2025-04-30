<?php

namespace App\Rest\Resources;

use App\Models\TransportRoute;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportRouteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'from' => $this->from,
            'to' => $this->to,
            'price' => $this->price,
            'status' => $this->status,           // New field
            'updated_by' => $this->updated_by,   // New field
            'deleted_by' => $this->deleted_by,   // New field
            'deleted_on' => $this->deleted_on,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
