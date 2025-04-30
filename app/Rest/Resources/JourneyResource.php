<?php

namespace App\Rest\Resources;

use App\Models\Journey;
use Illuminate\Http\Resources\Json\JsonResource;

class JourneyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'route_id' => $this->route_id,
            'time' => $this->time,
            'status' => $this->status,           // New field
            'updated_by' => $this->updated_by,   // New field
            'deleted_by' => $this->deleted_by,   // New field
            'deleted_on' => $this->deleted_on,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
