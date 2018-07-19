<?php

namespace App\Models;

class RejectionReason extends MyModel {
    
    protected $table = "rejection_reasons";


    public static function getAllApi() {
        return static::join('rejection_reasons_translations as trans', 'rejection_reasons.id', '=', 'trans.rejection_reason_id')
                        ->orderBy('rejection_reasons.this_order', 'ASC')
                        ->where('rejection_reasons.active',true)
                        ->where('trans.locale', static::getLangCode())
                        ->select('rejection_reasons.id','trans.title')
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
        return $this->hasMany(RejectionReasonTranslation::class, 'rejection_reason_id');
    }
    protected static function boot() {
        parent::boot();

        static::deleting(function($bath) {
            foreach ($bath->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($bath) {
           
        });
    }
   

}
