<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('commission',11,2);
            $table->decimal('price',11,2);
            $table->decimal('taxes',11,2);
            $table->decimal('delivery_cost',11,2);
            $table->decimal('total_cost',11,2);
            $table->integer('status');
            $table->double('lat',11,8);
            $table->double('lng',11,8);
            $table->boolean('payment_method');
            

            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('users');

            $table->integer('vehicle_id')->unsigned();
            $table->foreign('vehicle_id')->references('id')->on('vehicles');


            $table->integer('rejection_reason_id')->unsigned()->nullable();
            $table->foreign('rejection_reason_id')->references('id')->on('rejection_reasons');

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
        Schema::dropIfExists('orders');
    }
}
