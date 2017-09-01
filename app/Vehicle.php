<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
  protected $fillable = [
      'user_id','vehicle', 'vehicle_number','last_fuel_action','last_service_action', 'km_reading_action',
  ];
}
