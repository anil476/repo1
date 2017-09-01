<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
  protected $fillable = [
      'activity_id','client_id','action_status_id','fuel_id','quantity','last_fuel','last_service','last_insurance',
      'meter_reading','average','agent_contact','remark',
  ];
}
