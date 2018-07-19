<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\ModelTrait;
use DB;

class User extends Authenticatable {

    use Notifiable;
    use ModelTrait;

    protected $casts = array(
        'id' => 'integer',
        'mobile' => 'string',
        'account_type_id' => 'integer',
    );
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );

    public function vehicle() {
        $lang_code = static::getLangCode();
        return $this->hasOne(Vehicle::class, 'driver_id', 'id')
                        ->join('vehicle_types', 'vehicle_types.id', 'vehicles.vehicle_type_id')
                        ->join('vehicle_types_translations', 'vehicle_types.id', 'vehicle_types_translations.vehicle_type_id')
                        ->join('vehicle_weights', 'vehicle_weights.id', 'vehicles.vehicle_weight_id')
                        ->join('vehicle_weights_translations', 'vehicle_weights.id', 'vehicle_weights_translations.vehicle_weight_id')
                        ->where('vehicle_types_translations.locale', $lang_code)
                        ->where('vehicle_weights_translations.locale', $lang_code)
                        ->select('vehicles.*', 'vehicle_types.id as vehicleTypeId', 'vehicle_types_translations.title as vehicleTypeTitle', 'vehicle_weights.id as vehicleWeightId', 'vehicle_weights_translations.title as vehicleWeightTitle');
    }
    
      public function orders() {
        $lang_code = static::getLangCode();
        return $this->belongsToMany(Order::class, 'orders_drivers', 'driver_id', 'order_id');
                       
    }

    public static function getAllNearestDriversApi($where_array = array()) {
        $map_range = Setting::where('name', 'map_range')->first();
        $data = Vehicle::join('users', 'users.id', '=', 'vehicles.driver_id')
                ->leftJoin('orders_drivers', 'users.id', '=', 'orders_drivers.driver_id')
                ->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id')
                ->join('vehicle_types_translations', 'vehicle_types.id', '=', 'vehicle_types_translations.vehicle_type_id')
                ->join('vehicle_weights', 'vehicle_weights.id', '=', 'vehicles.vehicle_weight_id')
                ->join('vehicle_weights_translations', 'vehicle_weights.id', '=', 'vehicle_weights_translations.vehicle_weight_id')
                ->where(function ($query) {
                    $query->whereNull('orders_drivers.id')
                    ->orWhereIn('orders_drivers.status',[0,2,3,4,7]);
                })
                ->where('vehicle_types_translations.locale', static::getLangCode())
                ->where('vehicle_weights_translations.locale', static::getLangCode())
                ->where('vehicle_types.id', $where_array['vehicle_type'])
                ->select('users.id', 'vehicles.vehicle_image as vehicleImage', 'vehicles.price', 'vehicle_types_translations.title as vehicleTypeTitle', 'vehicle_weights_translations.title as vehicleWeightTitle', 'vehicles.lat', 'vehicles.lng', DB::raw(static::iniDiffLocations('vehicles', $where_array['lat'], $where_array['lng'])))
                ->groupBy('vehicles.id')
                ->orderBy('distance', 'ASC')
                ->having('distance', '<=', $map_range->value)
                ->get();
        // dd($data);
        $data = static::transformCollection($data, 'NearestDriversApi', ['lat' => $where_array['lat'], 'lng' => $where_array['lng']]);
        return $data;
    }

    public static function transformNearestDriversApi($item, $extra_params) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->vehicleImage = url('public/uploads/vehicles/' . $item->vehicleImage);
        $transformer->vehicleTypeTitle = $item->vehicleTypeTitle;
        $transformer->vehicleWeightTitle = $item->vehicleWeightTitle;
        $transformer->price = $item->price;
        $transformer->distance = $item->distance;  //to kilo
        $duration = GetDrivingDistance($extra_params['lat'], $extra_params['lng'], $item->lat, $item->lng);
        $transformer->time = $duration['time'];
        return $transformer;
    }

    public static function transform($item) {
        $transformer = new \stdClass();
        if ($item->type == 2) {
            $transformer->name = $item->name;
        }
        $transformer->username = $item->username;
        $transformer->email = $item->email;
        $transformer->mobile = $item->mobile;
        $transformer->image = url('public/uploads/users') . '/' . $item->image;

        if ($item->type == 2) {
            $transformer->vehicle = $item->vehicle ? Vehicle::transform($item->vehicle) : new \stdClass();
        }
        return $transformer;
    }

    protected static function boot() {
        parent::boot();

        static::deleted(function($user) {
            if ($user->user_image != 'default.png') {
                User::deleteUploaded('users', $user->user_image);
            }
        });
    }

}
