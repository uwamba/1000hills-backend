<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AdminAgencyJourneyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Only apply when an admin is authenticated
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();

            // Get all agency IDs the admin manages
            $agencyIds = $admin->manages()
                ->where('object', 'agence')
                ->pluck('object_id');
            
            // If no agencies, ensure no journeys are returned
            if ($agencyIds->isEmpty()) {
                // Filter to an impossible condition
                $builder->whereRaw('0 = 1');
            } else {
                // Filter journeys whose bus belongs to one of these agencies
                $builder->whereHas('bus', function (Builder $q) use ($agencyIds) {
                    $q->whereIn('agency_id', $agencyIds);
                });
            }
        }
    }
}
