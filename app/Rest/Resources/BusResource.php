<?php
namespace App\Rest\Resources;

use App\Models\Bus;
use App\Rest\Resource as RestResource;
use Lomkit\Rest\Http\Requests\RestRequest;
use App\Rest\Resources\AgencyResource;
use App\Rest\Resources\SeatTypeResource;

class BusResource extends RestResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    public static $model = Bus::class;

    /**
     * The default pagination limit.
     *
     * @var int
     */
    public int $defaultLimit = 50;

    /**
     * The fields exposed in the resource.
     *
     * @param RestRequest $request
     * @return array
     */
    public function fields(RestRequest $request): array
    {
        return [
            'id',
            'name',
            'seat_type_id',
            'seat_type',  // Added the seat_type here
            'number_of_seat',
            'agency_id',
            'status',        // Newly added
            'updated_by',    // Newly added
            'deleted_by',    // Newly added
            'deleted_on',    // Newly added
            'created_at',
            'updated_at',
        ];
    }

    /**
     * The relations exposed in the resource.
     *
     * @param RestRequest $request
     * @return array
     */
    public function relations(RestRequest $request): array
    {
        return [
            'agency' => [
                'type' => 'relationship',
                'resource' => AgencyResource::class,
            ],
            'seatType' => [
                'type' => 'relationship',
                'resource' => SeatTypeResource::class,
            ],
        ];
    }

    /**
     * The pagination limits that can be used.
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
     * The available actions.
     *
     * @param RestRequest $request
     * @return array
     */
    public function actions(RestRequest $request): array
    {
        return [];
    }

    /**
     * The available instructions.
     *
     * @param RestRequest $request
     * @return array
     */
    public function instructions(RestRequest $request): array
    {
        return [];
    }
}
