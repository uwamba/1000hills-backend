<?php
// App\Scopes\AdminHotelScope.php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AdminHotelScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admins')->user();

            // Get the list of hotel_ids the admin manages
            $hotelIds = $admin->manages()
                ->where('object', 'hotel')
                ->pluck('object_id');

            $builder->whereIn('hotel_id', $hotelIds);
        }
    }
}
