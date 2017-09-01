<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegisterOtp extends Model
{
  protected $fillable = [
      'user_id', 'otp_code',
  ];


}
