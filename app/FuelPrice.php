<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FuelPrice extends Model
{
  protected $fillable = [
      'fuel_id', 'fuel_category_id','city_id','price',
  ];
}
