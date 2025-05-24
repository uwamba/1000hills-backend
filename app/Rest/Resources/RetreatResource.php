<?php

namespace App\Rest\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RetreatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'address' => $this->address,
            'capacity' => $this->capacity,
            'status' => $this->status,
            'type' => $this->type,
            'wifi' => $this->wifi,
            'projector' => $this->projector,
            'theater' => $this->theater,
            'flip_chart' => $this->flip_chart,
            'whiteboard' => $this->whiteboard,
            'pricing_type' => $this->pricing_type,
            'price_per_person' => $this->price_per_person,
            'package_price' => $this->package_price,
            'package_size' => $this->package_size,
            'viewed' => $this->viewed,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'deleted_on' => $this->deleted_on,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'photos' => $this->photos->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'name' => $photo->name,
                    'path' => $photo->path,
                    'status' => $photo->status,
                ];
            }),
        ];
    }
}
