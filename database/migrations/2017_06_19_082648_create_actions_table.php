<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id');
            $table->integer('client_id');
            $table->integer('fuel_id')->nullable();
            $table->string('quantity')->nullable();
            $table->string('last_fuel')->nullable();
            $table->string('last_service')->nullable();
            $table->string('last_insurance')->nullable();
            $table->string('meter_reading')->nullable();
            $table->string('average')->nullable();
            $table->string('agent_contact')->nullable();
            $table->string('remark');
            $table->timestamps();
        });

        Schema::table('actions',function($table){
          $table->foreign('activity_id')->references('id')->on('activities');
          $table->foreign('client_id')->references('id')->on('clients');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions');
    }
}
