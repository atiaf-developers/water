<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plate_letter_ar',255);
            $table->string('plate_letter_en',255);
            $table->string('plate_num_ar',255);
            $table->string('plate_num_en',255);
            $table->string('license_number',255)->nullable();
            $table->decimal('price',11,2)->nullable();
            $table->text('vehicle_image');
            $table->text('license_image');
            $table->double('lat',11,8);
            $table->double('lng',11,8);


            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('vehicle_weight_id')->unsigned()->nullable();
            $table->foreign('vehicle_weight_id')->references('id')->on('vehicle_weights');

            $table->integer('vehicle_type_id')->unsigned()->nullable();
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
