<?php

namespace App\Models;

class RejectionReason extends MyModel {
    
    protected $table = "rejection_reasons";
  
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
