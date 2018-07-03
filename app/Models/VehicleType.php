<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends MyModel {

    protected $table = "vehicle_types";
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );


    public function translations() {
        return $this->hasMany(VehicleTypeTranslation::class, 'vehicle_type_id');
    }

    public static function transform($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;

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
