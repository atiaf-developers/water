<?php

namespace App\Models;

class VehicleWeight extends MyModel {
    
    protected $table = "vehicle_weights";
  
    public static function transform($item)
    {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        return $transformer;
    }

     public function translations() {
        return $this->hasMany(VehicleWeightTranslation::class, 'vehicle_weight_id');
    }
    protected static function boot() {
        parent::boot();

        static::deleting(function($vehicle_weight) {
            foreach ($vehicle_weight->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($vehicle_weight) {
           
        });
    }
   

}
