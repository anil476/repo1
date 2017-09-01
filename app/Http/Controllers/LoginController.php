<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Log;
use JWTAuth;

class LoginController extends Controller
{
  public function login(Request $request){
    // Log::info("login method: ".Carbon::now()->addYears(1)->timestamp);
    // Log::info("mobile:".$request['mobile_number']);
    // Log::info("password:".$request['password']);
    //return 'wait checking API';
    $mobile_number = $request['mobile_number'];
    $password = $request['password'];
    $user_verify1 = User::where('mobile_number','=',$mobile_number)->where('number_verified','=','yes')->first();
    if($user_verify1){
      if(!$token = JWTAuth::attempt(['mobile_number'=> $mobile_number,'password'=> $password],['exp' => Carbon::now()->addYears(1)->timestamp])){
        return Response::json(['failed'=>'Incorrect mobile number or password'],404);
        }else {
          $user = JWTAuth::toUser($token);
          $user->token = $token;
          return Response::json(['user'=>$user],202);

        }
    }else {
      return Response::json(['failed'=>'Incorrect mobile number or password'],404);
    }
  }

  public function logout(Request $request){
    $token = $request->query('token');
    JWTAuth::invalidate($token);
    Auth::logout();
    Session::flush();
   return Response::json('logout successfully',200);
  }
}
