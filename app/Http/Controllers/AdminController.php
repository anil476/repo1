<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use App\Fuel;
use App\FuelCategory;
use App\FuelPrice;
use App\Company;
use App\City;
use Log;
use DB;
use GuzzleHttp\Client;
use App\User;
use App\Traits\FuelList;

class AdminController extends Controller
{
  public function getUser(Request $request){
    // Log::info("include Deleted : ".$request->query('includeDeleted'));
    // Log::info("current url: ".URL::current());
    if($request->query('includeDeleted') == 'false'){
      $users = User::where('deleted','=','no')->get();
      return Response::json(['users'=>$users],200);
  }else if($request->query('includeDeleted') == 'true'){
    $users = User::where('deleted','=','yes')->get();
    return Response::json(['users'=>$users],200);
  }else {
    $user = User::all();
      return Response::json(['users'=>$user],200);
  }

}

  public function editUser(Request $request){
    $user_id = $request['id'];
    if($user_id != ''){
      $update_user = User::find($user_id);
      $update_user->name = $request['name'];
      $update_user->mobile_number = $request['mobile_number'];
      $update_user->number_verified = $request['number_verified'];
      $update_user->save();
      return Response::json(['user'=>$update_user],200);
    }else {
        return Response::json(['failed'=>'User info not updated'],404);
    }

  }

  public function deleteUser($user_id = NULL){
    if($user_id){
      $delete_user = User::find($user_id);
      $delete_user->deleted = 'yes';
      $delete_user->save();
      return Response::json(['user'=>$delete_user],200);
    }else {
        return Response::json(['failed'=>'User is not deleted'],404);
    }

  }



}
