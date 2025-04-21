<?php

namespace App\Rest\Resources;

use App\Models\Bus;
use Illuminate\Http\Resources\Json\JsonResource;

class BusResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'seat_type' => $this->seat_type,
            'number_of_seat' => $this->number_of_seat,
            'agency_id' => $this->agency_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
