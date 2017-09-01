<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  protected $fillable = [
      'fuel_price_id', 'company_name',
  ];
}
