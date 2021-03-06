<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends MyModel {

    protected $table = 'settings';
    protected $fillable = ['name', 'value'];
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );

    public static function transform($item) {
        //dd($item->name);
        $transformer = new \stdClass();
        if ($item->name == 'about') {
            $value = json_decode($item->value);
        }
        $transformer->{$item->name} = $item->value;

        //$transformer->$item->name=$item->value;
        return $transformer;
    }

}
