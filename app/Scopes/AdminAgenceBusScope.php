<?php
// App\Scopes\AdminHotelScope.php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AdminAgenceBusScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();

            // Get the list of hotel_ids the admin manages
            $busIds = $admin->manages()
                ->where('object', 'agence')
                ->pluck('object_id');

            $builder->whereIn('agency_id', $busIds);
        }
    }
}
