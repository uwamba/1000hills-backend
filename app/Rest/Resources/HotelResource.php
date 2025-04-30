<?php

namespace App\Rest\Resources;

use App\Rest\Resource as RestResource;
use Lomkit\Rest\Http\Requests\RestRequest;

class HotelResource extends RestResource
{
    public static $model = \App\Models\Hotel::class;

    public int $defaultLimit = 50;

    public function fields(RestRequest $request): array
    {
        return [
            'id',
            'name',
            'address',
            'coordinate',
            'description',
            'stars',
            'working_time',
            'status',        // New field
            'updated_by',    // New field
            'deleted_by',    // New field
            'deleted_on',    // New field
        ];
    }

    public function relations(RestRequest $request): array
    {
        return [
            // 'rooms' if Hotel hasMany Rooms
        ];
    }

    public function scopes(RestRequest $request): array
    {
        return [];
    }

    public function limits(RestRequest $request): array
    {
        return [10, 25, 50];
    }

    public function actions(RestRequest $request): array
    {
        return [];
    }

    public function instructions(RestRequest $request): array
    {
        return [];
    }
    
}
