<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Fuel;
use App\FuelCategory;
use App\FuelPrice;
use App\Company;
use Carbon\Carbon;
use Log;
use App\City;

class FuelController extends Controller
{
  public function postFuel(Request $request){

    $city = City::where('city_name','=',$request['city'])->where('city_state','=',$request['state'])->first();

if(!$city){
return Response::json('city does not exist',400);
}

if($city){
  $city_id = $city->id;
}


    $check = DB::table('fuel_prices')
                  ->join('cities','cities.id','=','fuel_prices.city_id')
                  ->join('companies','companies.id','=','fuel_prices.company_id')
                  ->join('fuels','fuels.id','=','fuel_prices.fuel_id')
                  ->join('fuel_categories','fuel_categories.id','=','fuel_prices.fuel_category_id')
                  ->select('fuels.fuel')
                  ->where('fuels.id',$request['fuel_id'])
                  ->where('fuel_categories.id',$request['category_id'])
                  ->where('companies.id',$request['company_id'])
                  ->where('cities.city_name',$request['city'])
                  ->where('cities.city_state',$request['state'])
                  ->get();

    if($check->isEmpty()){
      $fuel = Fuel::find($request['fuel_id']);
      $fuel_last_id = $fuel->id;

      $fuelcat = FuelCategory::find($request['category_id']);
      $fuelcat_last_id = $fuelcat->id;

      $company = Company::find($request['company_id']);
      $company_last_id = $company->id;




      $fuelprice = new FuelPrice();
      $fuelprice->fuel_id = $fuel_last_id;
      $fuelprice->fuel_category_id = $fuelcat_last_id;
      $fuelprice->company_id = $company_last_id;
      $fuelprice->city_id = $city_id;
      $fuelprice->price = $request['price'];
      $fuelprice->save();
      $fuel_price_id = $fuelprice->id;

      $fuel_recent = DB::table('fuel_prices')
                  ->join('cities','cities.id','=','fuel_prices.city_id')
                  ->join('companies','companies.id','=','fuel_prices.company_id')
                  ->join('fuels','fuels.id','=','fuel_prices.fuel_id')
                  ->join('fuel_categories','fuel_categories.id','=','fuel_prices.fuel_category_id')
                  ->select('cities.id as city_id','cities.city_name as city','cities.city_state as state','companies.id as company_id',
                  'companies.company','fuels.id as fuel_id','fuels.fuel','fuel_categories.id as fuel_category_id',
                  'fuel_categories.category','fuel_prices.id as fuel_price_id','fuel_prices.price')
                  ->where('fuel_prices.id',$fuel_price_id)
                  ->get();
      return Response::json($fuel_recent);
    }else {
      $city = City::where('city_name','=',$request['city'])->where('city_state','=',$request['state'])->first();
      $city_id = $city->id;

      $update = DB::table('fuel_prices')
                    ->where('fuel_prices.fuel_id',$request['fuel_id'])
                    ->where('fuel_prices.fuel_category_id',$request['category_id'])
                    ->where('fuel_prices.company_id',$request['company_id'])
                    ->where('fuel_prices.city_id',$city_id)
                    ->update(['fuel_prices.price'=>$request['price']]);

      return Response::json($update);
    }

  }

// when user click on view price button
    public function getFuelPrice(Request $request){
      // Log::info('city : '.$request->query('city'));
      // Log::info('state: '.$request->query('state'));
      // Log::info("fuel  : ".$request->query('fuel'));
      // Log::info("category  : ".$request->query('category'));
      // Log::info("company  : ".$request->query('company'));
      $city = $request->query('city');
      $state = $request->query('state');
      $fuel = $request->query('fuel');
      $category = $request->query('category');
      $company = $request->query('company');
      // DB::connection()->enableQueryLog();
      $query = DB::table('fuel_prices')
              ->join('cities','cities.id','=','fuel_prices.city_id')
              ->join('companies','companies.id','=','fuel_prices.company_id')
              ->join('fuels','fuels.id','=','fuel_prices.fuel_id')
              ->join('fuel_categories','fuel_categories.id','=','fuel_prices.fuel_category_id')
              ->select('cities.city_name','cities.city_state','companies.company','fuels.fuel',
                        'fuel_categories.category','fuel_prices.price');

              $query->where('cities.city_name','like', '%' .$city. '%');
              $query->where('cities.city_state','like', '%' .$state. '%');
              $query->where('companies.company','like','%' .$company. '%');

              if($fuel){
                  $query->where('fuels.fuel','like','%' .$fuel. '%');
              }
              if($category){
                  $query->where('fuel_categories.category','like','%' .$category. '%');
              }

              $fuel = $query->distinct()->get();
              // Log::info($fuel);
              // Log::info(DB::getQueryLog());
              if($fuel->isEmpty()){
                return Response::json('No matching result available',400);
              }else {
                return Response::json($fuel);
              }


    }

    public function getFuels(Request $request){
      $fuel_name = Fuel::all();
      if($fuel_name){
          return Response::json($fuel_name);
      }else {
          return Response::json('fuel does not exist');
      }

    }

    public function getFuelCategories(Request $request){
      $fuelcategory = FuelCategory::all();
      if($fuelcategory){
          return Response::json($fuelcategory);
      }else {
          return Response::json('fuel does not exist');
      }

    }

    public function getCompanies(Request $request){
      $company = Company::all();
      if($company){
          return Response::json($company);
      }else {
          return Response::json('fuel does not exist');
      }

    }
}
