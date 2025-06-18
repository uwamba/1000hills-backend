<?php

namespace App\Rest\Resources;

use App\Rest\Resource as RestResource;
use Lomkit\Rest\Http\Requests\RestRequest;

class ApartmentOwnerResource extends RestResource
{
    // Specify the underlying model
    public static $model = \App\Models\ApartmentOwner::class;

    // Default pagination limit
    public int $defaultLimit = 50;

    /**
     * Define which fields are returned for ApartmentOwner.
     * Include actual column names: id, name, address, contract_path, status,
     * created_by, updated_by, plus timestamps if desired.
     */
    public function fields(RestRequest $request): array
    {
        return [
            'id',
            'name',
            'address',
            'contract_path',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Define relationships that can be included/eager-loaded.
     * Here: apartments (one-to-many).
     */
    public function relations(RestRequest $request): array
    {
        return [
            'apartments',
        ];
    }

    /**
     * Define filter scopes. 
     * If you have a local scope in the model, e.g. scopeStatus($query, $status),
     * you could add 'status' here to allow filtering by status via query param.
     * Otherwise leave empty or adjust to your model scopes.
     */
    public function scopes(RestRequest $request): array
    {
        return [
            // Example if you have scopeStatus in ApartmentOwner model:
            // 'status',
        ];
    }

    /**
     * Define allowed per-page limits.
     */
    public function limits(RestRequest $request): array
    {
        return [10, 25, 50];
    }

    /**
     * Define any custom actions (e.g., activate, deactivate) if needed.
     */
    public function actions(RestRequest $request): array
    {
        return [
            // e.g. [
            //     'name' => 'activate',
            //     'label' => 'Activate Owner',
            //     'method' => 'POST',
            //     'uri' => 'apartment-owners/{id}/activate',
            //     // ...callback or handler...
            // ],
        ];
    }

    /**
     * Any instructions or metadata for front-end.
     */
    public function instructions(RestRequest $request): array
    {
        return [
            // e.g. ['field' => 'status', 'instruction' => 'Allowed values: active, inactive']
        ];
    }
}
