<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Vehicle extends MyModel {

    protected $table = "vehicles";

    
    public static function transform($item) {
        $lang_code = static::getLangCode();
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->plateLetter = $lang_code == 'ar' ? $item->plate_letter_ar : $item->plate_letter_en;
        $transformer->plateNumber = $lang_code == 'ar' ? $item->plate_num_ar : $item->plate_num_en;
        $transformer->licenseNumber = $item->license_number;
        $transformer->vehicleImage = url('public/uploads/vehicles') . '/' . $item->vehicle_image;
        $transformer->licenseImage = url('public/uploads/vehicles') . '/' . $item->license_image;
        $transformer->vehicleTypeId = $item->vehicleTypeId;
        $transformer->vehicleWeightId = $item->vehicleWeightId;
        $transformer->vehicleTypeTitle = $item->vehicleTypeTitle;
        $transformer->vehicleWeightTitle = $item->vehicleWeightTitle;
        $transformer->licenseNumber = $item->license_number;
        $transformer->vehicleRating = $item->vehicleRating;
        $transformer->totalRates = $item->total_rates;
        $transformer->price = $item->price;
        return $transformer;
    }

}
