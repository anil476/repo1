<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Log;

class CityController extends Controller
{
  public function getStates($state = NULL){

            $query = DB::table('cities')
                        ->select('cities.city_state as state')
                        ->where('cities.city_state','like', '%' .$state. '%')
                        ->distinct()->orderBy('cities.city_state')->get();

          return Response::json($query);

  } //getStates function close

  public function getCities($city = NULL){

    $query = DB::table('cities')
                ->select('cities.city_name as city')
                ->where('cities.city_name','like', '%' .$city. '%')
                ->distinct()->orderBy('cities.city_name')->get();

  return Response::json($query);


  } //getCities function close


}
