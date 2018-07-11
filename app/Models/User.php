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
        $lang_code=static::getLangCode();
        return $this->hasOne(Vehicle::class, 'delegate_id', 'id')->join('vehicle_types','vehicle_types.id','vehicles.vehicle_type_id')
                ->join('vehicle_types_translations','vehicle_types.id','vehicle_types_translations.vehicle_type_id')
                ->join('vehicle_weights','vehicle_weights.id','vehicles.vehicle_weight_id')
                ->join('vehicle_weights_translations','vehicle_weights.id','vehicle_weights_translations.vehicle_weight_id')
                ->where('vehicle_types_translations.locale',$lang_code)
                ->where('vehicle_weights_translations.locale',$lang_code)
                ->select('vehicles.*','vehicle_types.id as vehicleTypeId','vehicle_types_translations.title as vehicleTypeTitle','vehicle_weights.id as vehicleWeightId','vehicle_weights_translations.title as vehicleWeightTitle');
    }

    public static function transform($item)
    {
        $transformer = new \stdClass();
        $transformer->username = $item->username;
        $transformer->email = $item->email;
        $transformer->mobile = $item->mobile;
        $transformer->image = url('public/uploads/users').'/'.$item->image;
        if($item->type==2){
             $transformer->vehicle = Vehicle::transform($item->vehicle);
        }
        return $transformer;
    }

    protected static function boot() {
        parent::boot();

        static::deleted(function($user) {
            if ($user->user_image != 'default.png') {
                User::deleteUploaded('users',$user->user_image);
            }
            
        });
    }
   

}
