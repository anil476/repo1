<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vehicle_id');
            $table->string('title');
            $table->string('description',500)->nullable();
            $table->date('due_date')->nullable();
            $table->string('recurring');
            $table->string('every')->nullable();
            $table->string('month_day')->nullable();
            $table->string('week_day')->nullable();
            $table->string('year_day')->nullable();
            $table->timestamps();
        });

        Schema::table('activities',function($table){
          $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
