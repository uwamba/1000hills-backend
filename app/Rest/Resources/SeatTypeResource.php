<?php
// app/Http/Resources/SeatTypeResource.php

namespace App\Rest\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeatTypeResource extends JsonResource
{
 
        // app/Http/Resources/SeatTypeResource.php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'seat_layout' => [
            'row' => $this->row,
            'seats_per_row' => $this->seat_column,  // Assuming this is the seats per row
        ],
        'exclude' => $this->exclude,
    ];

    }
}
