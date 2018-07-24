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
                        ->leftJoin('rating', 'vehicles.id', 'rating.entity_id')
                        ->join('vehicle_types', 'vehicle_types.id', 'vehicles.vehicle_type_id')
                        ->join('vehicle_types_translations', 'vehicle_types.id', 'vehicle_types_translations.vehicle_type_id')
                        ->join('vehicle_weights', 'vehicle_weights.id', 'vehicles.vehicle_weight_id')
                        ->join('vehicle_weights_translations', 'vehicle_weights.id', 'vehicle_weights_translations.vehicle_weight_id')
                        ->where('vehicle_types_translations.locale', $lang_code)
                        ->where('vehicle_weights_translations.locale', $lang_code)
                        ->select('vehicles.*', 'rating.total_rates', 'vehicle_types.id as vehicleTypeId', 'vehicle_types_translations.title as vehicleTypeTitle', 'vehicle_weights.id as vehicleWeightId', "vehicles.rating as vehicleRating", 'vehicle_weights_translations.title as vehicleWeightTitle');
    }

    public function orders() {
        $lang_code = static::getLangCode();
        return $this->belongsToMany(Order::class, 'orders_drivers', 'driver_id', 'order_id');
    }

    public static function getAllNearestDriversApi($where_array = array()) {
        $setting = Setting::where('name', 'map_range')->orWhere('name', 'commission')->get()->keyBy('name');
        $commission = $setting['commission']->value;
        $data = Vehicle::join('users', 'users.id', '=', 'vehicles.driver_id');
        $data->leftJoin('orders_drivers', 'users.id', '=', 'orders_drivers.driver_id');
        $data->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id');
        $data->join('vehicle_types_translations', 'vehicle_types.id', '=', 'vehicle_types_translations.vehicle_type_id');
        $data->join('vehicle_weights', 'vehicle_weights.id', '=', 'vehicles.vehicle_weight_id');
        $data->join('vehicle_weights_translations', 'vehicle_weights.id', '=', 'vehicle_weights_translations.vehicle_weight_id');
        $data->where(function ($query) {
            $query->whereNull('orders_drivers.id')
                    ->orWhereIn('orders_drivers.status', [2, 3, 4, 7]);
        });
        $data->where('vehicle_types_translations.locale', static::getLangCode());
        $data->where('vehicle_weights_translations.locale', static::getLangCode());
        //$data->where('vehicles.is_ready', 1);

        $data->where('vehicle_types.id', $where_array['vehicle_type']);
        $data->select('users.id', 'vehicles.vehicle_image as vehicleImage', DB::RAW("(vehicles.price+((vehicles.price*$commission)/100)) as price"), 'vehicle_types_translations.title as vehicleTypeTitle', "vehicles.rating as vehicleRating", 'vehicle_weights_translations.title as vehicleWeightTitle', 'vehicles.lat', 'vehicles.lng', DB::raw(static::iniDiffLocations('vehicles', $where_array['lat'], $where_array['lng'])));
        $data->groupBy('vehicles.id');
        $data->orderBy('distance', 'ASC');
        $data->having('distance', '<=', $setting['map_range']->value);
        if (isset($where_array['price_from'])) {
            $data->having('price', '>=', $where_array['price_from']);
        }
        if (isset($where_array['price_to'])) {
            $data->having('price', '<=', $where_array['price_to']);
        }
        $data = $data->get();
        // dd($data);
        $data = static::transformCollection($data, 'NearestDriversApi', ['commission' => $setting['commission']->value, 'lat' => $where_array['lat'], 'lng' => $where_array['lng']]);
        return $data;
    }

    public static function transformNearestDriversApi($item, $extra_params) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->vehicleImage = url('public/uploads/vehicles/' . $item->vehicleImage);
        $transformer->vehicleTypeTitle = $item->vehicleTypeTitle;
        $transformer->vehicleWeightTitle = $item->vehicleWeightTitle;
        $transformer->vehicleRating = $item->vehicleRating;
        $transformer->price = $item->price + (($item->price * $extra_params['commission']) / 100);
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
            $count = Order::getOrdersCountWithDistinctClients(['driver' => $item->id, 'status' => OrderDriver::$user_status['driver']['latest']]);
            $transformer->vehicle = $item->vehicle ? Vehicle::transform($item->vehicle) : new \stdClass();
            if ($transformer->vehicle) {
                $transformer->vehicle->showRating = $count >= 10 ? true : false;
            }
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
