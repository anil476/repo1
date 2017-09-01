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
            $table->integer('user_id')->unsigned();
            $table->integer('vehicle_type_id');
            $table->integer('fuel_id')->default(1);
            $table->string('vehicle');
            $table->string('vehicle_number');
            $table->timestamps();
        });

        Schema::table('vehicles',function($table){
          $table->foreign('user_id')->references('id')->on('users');
          $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types');
          $table->foreign('fuel_id')->references('id')->on('fuels');
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
