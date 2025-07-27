<?php

namespace App\Rest\Resources;

use App\Models\Room;
use App\Rest\Resource as RestResource;
    use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
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
     * The exposed fields that could be provided.
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
            'view',
            'has_bathroom',
            'price',
            'currency',
            'number_of_people',
            'has_ac',
            'hotel_id',
            'status',

            // New feature fields
            'has_swimming_pool',
            'has_laundry',
            'has_gym',
            'has_room_service',
            'has_sauna_massage',
            'has_kitchen',
            'has_fridge',
            'bookings' => fn () => $this->activeBookings->map(function ($booking) {
                return [
                    'from' => $booking->from_date_time,
                    'to' => $booking->to_date_time,
                ];
            }),
            // System fields
            'updated_by',
            'deleted_by',
            'deleted_on',
        ];
    }

    /**
     * The exposed relations that could be provided.
     *
     * @param RestRequest $request
     * @return array
     */
    public function relations(RestRequest $request): array
    {
        return [
            'hotel' => [
                'type' => 'relationship',
                'resource' => HotelResource::class,
            ],
        ];
    }

    /**
     * The exposed limits that could be provided.
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
     * The actions that should be linked.
     *
     * @param RestRequest $request
     * @return array
     */
    public function actions(RestRequest $request): array
    {
        return [];
    }

    /**
     * The instructions that should be linked.
     *
     * @param RestRequest $request
     * @return array
     */
    public function instructions(RestRequest $request): array
    {
        return [];
    }



}
