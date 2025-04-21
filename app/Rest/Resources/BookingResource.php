<?php

namespace App\Rest\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'from_date_time' => $this->from_date_time,
            'to_date_time' => $this->to_date_time,
            'object_type' => $this->object_type,
            'object_id' => $this->object_id,
            'client_id' => $this->client_id,
            'amount_to_pay' => $this->amount_to_pay,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
