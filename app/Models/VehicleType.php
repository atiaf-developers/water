<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends MyModel {

    protected $table = "vehicle_types";
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );

    public static function getAll($paginate = false) {
        $data =  static::join('vehicle_types_translations as trans', 'vehicle_types.id', '=', 'trans.vehicle_type_id')
                        ->orderBy('vehicle_types.this_order', 'ASC')
                        ->where('vehicle_types.active',true)
                        ->where('trans.locale', static::getLangCode())
                        ->select('vehicle_types.id','trans.title','vehicle_types.image');
                         if (!$paginate) {
                            $data->get();
                        }else{
                            $data->paginate(static::$limit);
                        }
        return $data;               
    }

    public static function getAllAdmin() {
        return static::join('vehicle_types_translations as trans', 'vehicle_types.id', '=', 'trans.vehicle_type_id')
                        ->orderBy('vehicle_types.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->select('vehicle_types.id','trans.title')
                        ->get();
    }


    public function translations() {
        return $this->hasMany(VehicleTypeTranslation::class, 'vehicle_type_id');
    }

    public static function transform($item) {
        $transformer = new \stdClass();
        
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        $transformer->image = url('public/uploads/vehicle_types').'/m_'.$item->image;
        return $transformer;
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($vehicle_type) {
            foreach ($vehicle_type->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($vehicle_type) {
           static::deleteUploaded('vehicle_types', $vehicle_type->image);
        });
    }

}
