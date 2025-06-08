<?php

namespace App\Rest\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'client_id' => $this->client_id,
        'client' => new ClientResource($this->whenLoaded('client')), // 👈 Include client
        'transaction_id' => $this->transaction_id,
        'amount_paid' => $this->amount_paid,
        'account' => $this->account,
        'type' => $this->type,
        'status' => $this->status,
        'updated_by' => $this->updated_by,
        'deleted_by' => $this->deleted_by,
        'deleted_on' => $this->deleted_on,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}

}
