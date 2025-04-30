<?php
// app/Rest/Resources/ApartmentResource.php

namespace App\Rest\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'number_of_bedroom' => $this->number_of_bedroom,
            'kitchen_inside' => $this->kitchen_inside,
            'kitchen_outside' => $this->kitchen_outside,
            'number_of_floor' => $this->number_of_floor,
            'address' => $this->address,
            'coordinate' => $this->coordinate,
            'annexes' => $this->annexes,
            'description' => $this->description,
            'status' => $this->status,           // New field
            'updated_by' => $this->updated_by,   // New field
            'deleted_by' => $this->deleted_by,   // New field
            'deleted_on' => $this->deleted_on,   // New field
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,   // You might want to include this too
        ];
    }
}
