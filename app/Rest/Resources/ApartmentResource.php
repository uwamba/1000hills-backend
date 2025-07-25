<?php

namespace App\Rest\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray($request)
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
            'status' => $this->status,
            'swimming_pool' => $this->swimming_pool,
            'laundry' => $this->laundry,
            'gym' => $this->gym,
            'room_service' => $this->room_service,
            'sauna_massage' => $this->sauna_massage,
            'price_per_night' => $this->price_per_night,
            'price_per_month' => $this->price_per_month,
            'contract' => $this->contract,
            'view' => $this->view,

            'updated_by' => $this->updatedBy ? $this->updatedBy->only(['id', 'name', 'email']) : null,
            'deleted_by' => $this->deletedBy ? $this->deletedBy->only(['id', 'name', 'email']) : null,
            'deleted_on' => $this->deleted_on,

            // Updated photos with full details
            'photos' => $this->photos->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'name' => $photo->name,
                    'url' => $photo->path,
                    'status' => $photo->status,
                    'deleted_on' => $photo->deleted_on,
                    'updated_by' => $photo->updatedBy ? $photo->updatedBy->only(['id', 'name', 'email']) : null,
                    'deleted_by' => $photo->deletedBy ? $photo->deletedBy->only(['id', 'name', 'email']) : null,
                ];
            }),
            'bookings' => $this->activeBookings->map(function ($booking) {
             return [
               'from' => $booking->from_date_time,
               'to' => $booking->to_date_time,
             ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
