<?php
// App\Scopes\AdminHotelScope.php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AdminAgenceScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();

            // Get the list of hotel_ids the admin manages
            $hotelIds = $admin->manages()
                ->where('object', 'agence')
                ->pluck('object_id');

            $builder->whereIn('agence_id', $hotelIds);
        }
    }
}
