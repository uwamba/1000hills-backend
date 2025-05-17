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
        'price' => $this->price,
        'bus' => [
            'id' => $this->bus?->id,
            'name' => $this->bus?->name,
            'agency' => [
                'id' => $this->bus?->agency?->id,
                'name' => $this->bus?->agency?->name,
                'address' => $this->bus?->agency?->address,
                'description' => $this->bus?->agency?->description,
            ],
            
            'layout' => [
                'id' => $this->bus?->seatType?->id,
                'name' => $this->bus?->seatType?->name,
                'row' => $this->bus?->seatType?->row,
                'column' => $this->bus?->seatType?->column,
                'seat_row' => $this->bus?->seatType?->seat_row,
                'seat_column' => $this->bus?->seatType?->seat_column,
                'exclude' => $this->bus?->seatType?->exclude,
            ],
        ],
        'status' => $this->status,
        'updated_by' => $this->updated_by,
        'deleted_by' => $this->deleted_by,
        'deleted_on' => $this->deleted_on,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}

}
