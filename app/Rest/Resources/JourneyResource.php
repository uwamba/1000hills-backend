<?php

namespace App\Rest\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JourneyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'route_id' => $this->route_id,
            'from' => $this->from,
            'to' => $this->to,
            'departure' => $this->departure,
            'return' => $this->return,
            'bus_id' => $this->bus_id,
            'status' => $this->status,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'deleted_on' => $this->deleted_on,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
