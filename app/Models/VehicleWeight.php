<?php

namespace App\Models;

class VehicleWeight extends MyModel {
    
    protected $table = "vehicle_weights";


    public static function getAll($paginate = false) {
        $data =  static::join('vehicle_weights_translations as trans', 'vehicle_weights.id', '=', 'trans.vehicle_weight_id')
                        ->orderBy('vehicle_weights.this_order', 'ASC')
                        ->where('vehicle_weights.active',true)
                        ->where('trans.locale', static::getLangCode())
                        ->select('vehicle_weights.id','trans.title','vehicle_weights.image');
                        if (!$paginate) {
                            $data->get();
                        }else{
                            $data->paginate(static::$limit);
                        }
        return $data;
                        
    }

    public static function getAllAdmin() {
        return static::join('vehicle_weights_translations as trans', 'vehicle_weights.id', '=', 'trans.vehicle_weight_id')
                        ->orderBy('vehicle_weights.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->select('vehicle_weights.id','trans.title')
                        ->get();
    }
  
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
