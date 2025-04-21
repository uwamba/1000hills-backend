<?php

namespace App\Rest\Resources;

use App\Models\SeatType;
use Illuminate\Http\Resources\Json\JsonResource;

class SeatTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'row' => $this->row,
            'column' => $this->column,
            'name' => $this->name,
            'seat_row' => $this->seat_row,
            'seat_column' => $this->seat_column,
            'exclude' => $this->exclude,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
