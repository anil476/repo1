<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
  protected $fillable = [
      'city_name','city_state','state_code','lng','lat',
  ];
}
