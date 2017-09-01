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
use Redirect;
use GuzzleHttp\Client;
use App\RegisterOtp;
use JWTAuth;

class RegisterController extends Controller
{
  public function registerUsers(Request $request){
    $name = $request['name'];
    $mobile_number = $request['mobile_number'];
    $number_verified = 'no';
    $password = \Hash::make($request['mobile_number']);

    $user = User::where('mobile_number','=',$mobile_number)->first();

                if(!$user){
                    $reg = new User();
                    $reg->name = $name;
                    $reg->mobile_number = $mobile_number;
                    $reg->number_verified = $number_verified;
                    $reg->password = $password;
                    $reg->save();

                    $last_inserted_user_id = $reg->id;
                    $otp_code = rand(100000 , 999999);
                    $message = $otp_code." is the One-Time Password(OTP) for Registration. This OTP is usable only once and valid for 15 minutes from the request.";
                    $encodedMessage = urlencode($message);
                    $sender = 'GoFuel';

                    $api = "https://control.msg91.com/api/sendhttp.php?authkey=105634AYs9x8KTHk56cb1eff&mobiles=" . $mobile_number . "&message=" . $encodedMessage. "&route=4&sender=" . $sender;

                    $client = new Client();
                    $res = $client->request('GET', $api);
                      // Log::info(" for new user status code: ".$res->getStatusCode());
                      //   Log::info($mobile_number);
                     if($res->getStatusCode() == 200){
                       $otp = new RegisterOtp();
                       $otp->user_id = $last_inserted_user_id;
                       $otp->otp_code = $otp_code;
                       $otp->expire_at = Carbon::now()->addMinutes(15);
                       $otp->save();
                      return Response::json($reg,200);
                     }else {
                     return Response::json('Please try again',404);
                     }
                  }else {

                    $otp_code = rand(100000 , 999999);
                    $message = $otp_code." is the One-Time Password(OTP) for Registration. This OTP is usable only once and valid for 15 minutes from the request.";
                    $encodedMessage = urlencode($message);
                    $sender = 'GoFuel';
                    try{
                    $api = "https://control.msg91.com/api/sendhttp.php?authkey=105634AYs9x8KTHk56cb1eff&mobiles=" . $mobile_number . "&message=" . $encodedMessage. "&route=4&sender=" . $sender;

                    $client = new Client();
                    $res = $client->request('GET', $api);
                      // Log::info("for existing user status code: ".$res->getStatusCode());
                      // Log::info($mobile_number);

                     if($res->getStatusCode() == 200){
                       $user = User::where('mobile_number','=',$mobile_number)->first();
                       $update_user = User::find($user->id);
                       $update_user->name = $name;
                       $update_user->save();
                       $user1 = User::where('mobile_number','=',$mobile_number)->first();

                       $otp = DB::table('register_otps')
                                  ->where('register_otps.user_id','=',$user1->id)
                                  ->update(['register_otps.otp_code'=>$otp_code,'register_otps.expire_at'=>Carbon::now()->addMinutes(15)]);
                      return Response::json($user1,200);
                     }else {
                     return Response::json('Internal server error',404);
                     }
                   }catch(\Exception $e){
                     return Response::json('Internal server error',404);
                   }

                  }
  }

  public function getVerified($user_id = NULL,$otp = NULL,$mobile_number = NULL){
    // Log::info("user id: ".$user_id);
    // Log::info("otp : ".$otp);
    // Log::info("Mobile no: ".$mobile_number);

    $otps = RegisterOtp::where('user_id','=',$user_id)->where('otp_code','=',$otp)->first();
    if($otps){
      if(Carbon::now() <= $otps->expire_at){
        $user = User::find($user_id);
        $user->number_verified = 'yes';
        $user->save();
        $user_verify1 = User::where('mobile_number','=',$mobile_number)->where('number_verified','=','yes')->first();

        if(!is_NULL($user_verify1)){
          $password = $user_verify1->mobile_number;
          $mobile_number = $user_verify1->mobile_number;
          if(!$token = JWTAuth::attempt(['mobile_number'=> $mobile_number,'password'=> $password],['exp' => Carbon::now()->addYears(1)->timestamp])){
            return Response::json('Incorrect mobile number',404);
            }else {
              $user = JWTAuth::toUser($token);
              $vehicle_count = DB::table('vehicles')
                                  ->where('vehicles.user_id','=',$user_id)
                                  ->count();
              $user->vehicle_count = $vehicle_count;
              $user->token = $token;
              return Response::json(['user'=>$user]);

            }
        }
      }else {
        return Response::json('OTP got expired, it is valid only for 15 minutes',404);
      }

    }else {
      return Response::json('You have entered wrong OTP',404);
    }
  }

  public function resendCode($user_id = NULL){
    $user = User::where('id','=',$user_id)->first();
    if($user){
      $mobile_number = $user->mobile_number;
      $otp_code = rand(100000 , 999999);
      $message = $otp_code." is the One-Time Password(OTP) for Registration. This OTP is usable only once and valid for 15 minutes from the request.";
      $encodedMessage = urlencode($message);
      $sender = 'GoFuel';

      $api = "https://control.msg91.com/api/sendhttp.php?authkey=105634AYs9x8KTHk56cb1eff&mobiles=" . $mobile_number . "&message=" . $encodedMessage. "&route=4&sender=" . $sender;

      $client = new Client();
      $res = $client->request('GET', $api);

   if($res->getStatusCode() == 200){
      $otp_update = DB::table('register_otps')
                    ->where('register_otps.user_id','=',$user_id)
                    ->update(['register_otps.otp_code'=>$otp_code,'register_otps.expire_at'=>Carbon::now()->addMinutes(15)]);
      return Response::json('We have resend otp,please verify',200);
       }else {
       return Response::json('Unable to send OTP to the given number',404);
       }
    }else {
      return Response::json('User does not exist,please register',404);
    }
  }

  public function forgotPassword(Request $request){
    $mobile_number = $request['mobile_number'];
    $user = User::where('mobile_number','=',$mobile_number)->first();

    // Log::info("mobile number: ".$mobile_number);
    if($user){
      $user_id = $user->id;
      $otp_code = rand(100000 , 999999);
      $message = $otp_code." is the One-Time Password(OTP) for Registration. This OTP is usable only once and valid for 15 minutes from the request.";
      $encodedMessage = urlencode($message);
      $sender = 'GoFuel';

      $api = "https://control.msg91.com/api/sendhttp.php?authkey=105634AYs9x8KTHk56cb1eff&mobiles=" . $mobile_number . "&message=" . $encodedMessage. "&route=4&sender=" . $sender;

      $client = new Client();
      $res = $client->request('GET', $api);

   if($res->getStatusCode() == 200){
      $otp_update = DB::table('register_otps')
                    ->where('register_otps.user_id','=',$user_id)
                    ->update(['register_otps.otp_code'=>$otp_code,'register_otps.expire_at'=>Carbon::now()->addMinutes(15)]);
      return Response::json($user,200);
       }else {
       return Response::json('Unable to send OTP to the given number',404);
       }
    }else {
      return Response::json('User does not exist,please register',404);
    }
  }

  public function setNewPassword(Request $request,$user_id = NULL){
    $password = $request['password'];
    $otps = RegisterOtp::where('user_id','=',$user_id)->first();
    if($otps){
      if(Carbon::now() <= $otps->expire_at){
        $user = User::find($user_id);
        if($user){
          $user->number_verified = 'yes';
          $user->password = \Hash::make($password);
          $user->save();
          return Response::json('Your password has been updated,please login',200);
        }else {
            return Response::json('Your password is not updated,please try again',404);
        }
      }else {
          return Response::json('OTP is expired,your password is not updated',404);
      }
}else {
  return Response::json('OTP is expired,your password is not updated',404);
    }
  }

}
