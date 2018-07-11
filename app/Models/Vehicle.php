<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends MyModel {

    protected $table = "vehicles";

    public static function transform($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->plateLetterAr = $item->plate_letter_ar;
        $transformer->plateLetterEn = $item->plate_letter_en;
        $transformer->plateNumAr = $item->plate_num_ar;
        $transformer->plateNumEn = $item->plate_num_en;
        $transformer->licenseNumber = $item->license_number;
        $transformer->vehicleImage = url('public/uploads/vehicles').'/'.$item->vehicle_image;
        $transformer->licenseImage = url('public/uploads/vehicles').'/'.$item->license_image;
        $transformer->vehicleTypeId = $item->vehicleTypeId;
        $transformer->vehicleWeightId = $item->vehicleWeightId;
        $transformer->vehicleTypeTitle = $item->vehicleTypeTitle;
        $transformer->vehicleWeightTitle = $item->vehicleWeightTitle;
        $transformer->licenseNumber = $item->license_number;
        return $transformer;
    }

}
