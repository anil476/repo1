<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
  protected $fillable = [
      'vehicle_id', 'category_id','title','description','due_date','recurring','every','month_day','week_day',
      'year_day',
  ];
}
