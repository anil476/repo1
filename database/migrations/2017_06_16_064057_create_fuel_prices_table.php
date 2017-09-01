<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFuelPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fuel_id');
            $table->integer('fuel_category_id');
            $table->integer('company_id');
            $table->integer('city_id');
            $table->string('price')->nullable();
            $table->timestamps();
        });

        Schema::table('fuel_prices',function($table){
          $table->foreign('fuel_id')->references('id')->on('fuels');
          $table->foreign('fuel_category_id')->references('id')->on('fuel_categories');
          $table->foreign('company_id')->references('id')->on('companies');
          $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fuel_prices');
    }
}
