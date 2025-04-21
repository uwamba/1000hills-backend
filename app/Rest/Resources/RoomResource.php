<?php

namespace App\Rest\Resources;

use App\Rest\Resource as RestResource;
use App\Models\Room;
use Lomkit\Rest\Http\Requests\RestRequest;

class RoomResource extends RestResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    public static $model = Room::class;

    /**
     * The default value for the pagination limit.
     *
     * @var int
     */
    public int $defaultLimit = 50;

    /**
     * The exposed fields that could be provided
     *
     * @param RestRequest $request
     * @return array
     */
    public function fields(RestRequest $request): array
    {
        return [
            'id',
            'name',
            'type',
            'has_wireless',
            'bed_size',
            'has_bathroom',
            'price',
            'currency',
            'number_of_people',
            'has_ac',
            'hotel_id', // Assuming we want to expose the hotel_id
        ];
    }

    /**
     * The exposed relations that could be provided
     *
     * @param RestRequest $request
     * @return array
     */
    public function relations(RestRequest $request): array
    {
        return [
            'hotel' => [
                'type' => 'relationship',
                'resource' => HotelResource::class, // Assuming you have a HotelResource
            ],
        ];
    }

    /**
     * The exposed limits that could be provided
     *
     * @param RestRequest $request
     * @return array
     */
    public function limits(RestRequest $request): array
    {
        return [
            10,
            25,
            50,
            100,
            200,
            500,
            1000,
        ];
    }

    /**
     * The actions that should be linked
     *
     * @param RestRequest $request
     * @return array
     */
    public function actions(RestRequest $request): array
    {
        return [];
    }

    /**
     * The instructions that should be linked
     *
     * @param RestRequest $request
     * @return array
     */
    public function instructions(RestRequest $request): array
    {
        return [];
    }
    
}
