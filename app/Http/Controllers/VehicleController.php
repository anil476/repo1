<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use Carbon\Carbon;
use Log;
use Redirect;
use App\Vehicle;
use App\VehicleType;
use App\Activity;
use App\ActivityCategory;
use App\Fuel;
use App\Client;
use App\Traits\VehicleAverage;
use App\Traits\VehicleDetails;


class VehicleController extends Controller
{
  // to rgister vehicle function
    public function registerVehicles(Request $request){

      $user_id = $request->query('user_id');
      $fuel_id = $request->query('fuel_id');
      $vehicle_id = $request->query('vehicle_id');
      if($fuel_id){
        $fuel_id = $fuel_id;
      }else {
        $fuel = Fuel::where('fuel','=','Petrol')->first();
      $fuel_id = $fuel->id;
      }
      // check if vehicle id then we need to update the vehicle details,if not then insert
      if(!$vehicle_id){
      if($user_id && $request['vehicle_type_id'] != ''){
        $user_check = User::where('id','=',$user_id)->first();
        if($user_check){
          $vehicle = new Vehicle();
          $vehicle->user_id = $user_id;
          $vehicle->vehicle_type_id = $request['vehicle_type_id'];
          $vehicle->fuel_id = $fuel_id;
          $vehicle->vehicle = $request['vehicle'];
          $vehicle->vehicle_number = $request['vehicle_number'];
          $vehicle->save();
          $last_vehicle_id = $vehicle->id;

          // Fill fuel entry
            $activity = new Activity();
            $activity->vehicle_id = $last_vehicle_id;
            $activity->title = "Fuel filling";
            $activity->description = "Reminder for fuel filling";
            $activity->due_date = NULL;
            $activity->recurring = 'yes';
            $activity->every = 'monday';
            $activity->month_day = 'null';
            $activity->week_day = 'null';
            $activity->year_day = 'null';
            $activity->save();

            // Servicing entry
            $activity = new Activity();
            $activity->vehicle_id = $last_vehicle_id;
            $activity->title = "Servicing";
            $activity->description = "Reminder for servicing";
            $activity->due_date = NULL;
            $activity->recurring = 'yes';
            $activity->every = 'monday';
            $activity->month_day = 'null';
            $activity->week_day = 'null';
            $activity->year_day = 'null';
            $activity->save();

            // Insurance entry
            $activity = new Activity();
            $activity->vehicle_id = $last_vehicle_id;
            $activity->title = "Insurance";
            $activity->description = "Reminder for insurance";
            $activity->due_date = NULL;
            $activity->recurring = 'yes';
            $activity->every = 'monday';
            $activity->month_day = 'null';
            $activity->week_day = 'null';
            $activity->year_day = 'null';
            $activity->save();


            $vehicle_info = DB::table('vehicles')
                            ->join('users','users.id','=','vehicles.user_id')
                            ->select('vehicles.id as vehicle_id','vehicles.vehicle_type_id','vehicles.fuel_id','vehicles.vehicle as vehicle_name','vehicles.vehicle_number')
                            ->where('users.id','=',$user_id)
                            ->get();

              if($vehicle_info->isEmpty()){
                return Response::json('No records available',400);
              }else {
                  return Response::json($vehicle_info);
              }

          }else {
            return Response::json('User does not exist');
          }
      }else {
        // Log::info(" not vehicle tye id: ".$request['vehicle_type_id']);
        // furzy entry
        $user_check = User::where('id','=',$user_id)->first();
        $vehicle_type = VehicleType::where('type','=','2-wheeler')->first();
        $fuel = Fuel::where('fuel','=','Petrol')->first();
        if($user_check){
          $vehicle = new Vehicle();
          $vehicle->user_id = $user_id;
          $vehicle->vehicle_type_id = $vehicle_type->id;
          $vehicle->fuel_id = $fuel->id;
          $vehicle->vehicle = 'Yamaha';
          $vehicle->vehicle_number = 'MP09-A4141';
          $vehicle->save();
          $last_vehicle_id = $vehicle->id;

          // Fill fuels
            $activity = new Activity();
            $activity->vehicle_id = $last_vehicle_id;
            $activity->title = "Fuel filling";
            $activity->description = "Reminder for fuel filling";
            $activity->due_date = NULL;
            $activity->recurring = 'yes';
            $activity->every = 'monday';
            $activity->month_day = 'null';
            $activity->week_day = 'null';
            $activity->year_day = 'null';
            $activity->save();

            // Servicing
              $activity = new Activity();
              $activity->vehicle_id = $last_vehicle_id;
              $activity->title = "Servicing";
              $activity->description = "Reminder for servicing";
              $activity->due_date = NULL;
              $activity->recurring = 'yes';
              $activity->every = 'monday';
              $activity->month_day = 'null';
              $activity->week_day = 'null';
              $activity->year_day = 'null';
              $activity->save();

              // Insurance entry
              $activity = new Activity();
              $activity->vehicle_id = $last_vehicle_id;
              $activity->title = "Insurance";
              $activity->description = "Reminder for insurance";
              $activity->due_date = NULL;
              $activity->recurring = 'yes';
              $activity->every = 'monday';
              $activity->month_day = 'null';
              $activity->week_day = 'null';
              $activity->year_day = 'null';
              $activity->save();

            $vehicle_info = DB::table('vehicles')
                            ->join('users','users.id','=','vehicles.user_id')
                            ->select('vehicles.id as vehicle_id','vehicles.vehicle_type_id','vehicles.fuel_id','vehicles.vehicle as vehicle_name','vehicles.vehicle_number')
                            ->where('users.id','=',$user_id)
                            ->get();
              if($vehicle_info->isEmpty()){
                return Response::json('No records available',400);
              }else {
                  return Response::json($vehicle_info);
              }

          }else {
            return Response::json('User does not exist');
          }
      }
    }else {
      $vehicle = Vehicle::find($vehicle_id);
      $vehicle->vehicle_type_id = $request['vehicle_type_id'];
      $vehicle->fuel_id = $fuel_id;
      $vehicle->vehicle = $request['vehicle'];
      $vehicle->vehicle_number = $request['vehicle_number'];
      $vehicle->save();
      return Response::json('Updated successfully');
    }
  }

  // get vehicle type function
    public function getVehicleType(){
      // Log::info('vehicle type');
      $vehicle = VehicleType::all();
      if($vehicle){
          return Response::json($vehicle);
      }else {
          return Response::json('vehicle type does not exist',400);
      }

    }

// get vehicle info function
    public function getVehicleDetails(Request $request,$user_id = NULL){
      // Log::info('user id'.$user_id);
      // Log::info('user id : '.$request->query('user_id'));
      $user_id = $request->query('user_id');
      $vehicle_name = $request->query('vehicle_name');
      $vehicle_array = array();
      if($user_id){
        $query = Vehicle::join('users','users.id','=','vehicles.user_id')
                  ->join('vehicle_types','vehicle_types.id','=','vehicles.vehicle_type_id')
                  ->join('fuels','fuels.id','=','vehicles.fuel_id')
                  ->select('vehicles.id','vehicles.vehicle as vehicle_name','vehicles.vehicle_number','vehicle_types.type','fuels.fuel','vehicle_types.id as type_id','fuels.id as fuel_id');

                  $query->where('users.id','=',$user_id);

                  if($vehicle_name){
                   $query->where('vehicles.vehicle','like', '%' .$vehicle_name. '%');
                   }
                   $query1 = $query->distinct()->get();
    if($query1){

    foreach ($query1 as $qry) {
      $query2 = DB::table('activities')
                ->select('activities.id as activity_id')
                ->where('activities.vehicle_id','=',$qry->id)
                ->get();

// $last = DB::select(DB::raw("SELECT last_fuel,last_service FROM actions WHERE last_fuel = (SELECT MAX(last_fuel) FROM actions  WHERE activity_id = $qry3->activity_id) OR last_service = (SELECT MAX(last_service) FROM actions  WHERE activity_id = $qry3->activity_id)"));
foreach ($query2 as $key => $qry2) {
  $last_fuel = DB::table('actions')
                  ->select('actions.last_fuel')
                  ->where('actions.activity_id',$qry2->activity_id)
                  ->max('actions.last_fuel');
  if(!is_NULL($last_fuel)){
    $action = DB::table('actions')
                  ->select('actions.id as action_id')
                  ->where('actions.activity_id','=',$qry2->activity_id)
                  ->where('actions.last_fuel','=',$last_fuel)
                  ->first();
    $action_id = $action->action_id;
    $action = DB::table('actions')
              ->select('actions.activity_id','actions.quantity','actions.meter_reading','actions.remark')
              ->where('actions.last_fuel','=',$last_fuel)
              ->where('actions.id','=',$action_id)
              ->first();
              $last_fuel = Carbon::parse($last_fuel)->format('Y-m-d');
              $qry->last_fuel = $last_fuel;
              $qry->meter_reading = $action->meter_reading;
              $qry->quantity = $action->quantity;
              $qry->remark = $action->remark;
            }
            $last_service = DB::table('actions')
                            ->select('actions.last_service')
                            ->where('actions.activity_id',$qry2->activity_id)
                            ->max('actions.last_service');
            if(!is_NULL($last_service)){
              $action = DB::table('actions')
                            ->select('actions.id as action_id')
                            ->where('actions.activity_id','=',$qry2->activity_id)
                            ->where('actions.last_service','=',$last_service)
                            ->first();
              $action_id = $action->action_id;
              $action = DB::table('actions')
                        ->select('actions.activity_id','actions.meter_reading','actions.remark')
                        ->where('actions.last_service','=',$last_service)
                        ->where('actions.id','=',$action_id)
                        ->first();
                        $last_service = Carbon::parse($last_service)->format('Y-m-d');
                        $qry->last_service = $last_service;
                        $qry->service_reading = $action->meter_reading;
                      }
        }
      }
      return Response::json($query1);
    }

  }else {
  return Response::json('No records available',400);
  }

}

    public function getHistory(Request $request){
      $user_id = $request->query('user_id');
      $vehicle_id = $request->query('vehicle_id');
      $title = $request->query('title');

       if($user_id && $vehicle_id){
        $query = DB::table('vehicles')
                        ->join('activities','activities.vehicle_id','=','vehicles.id')
                        ->join('actions','actions.activity_id','=','activities.id')
                        ->select('vehicles.id as vehicle_id','vehicles.vehicle as vehicle_name','vehicles.vehicle_number',
                        'actions.last_fuel as filled_fuel','actions.last_service as service_on','actions.meter_reading',
                        'actions.fuel_id','actions.client_id','actions.quantity','actions.last_insurance')
                        ->where('vehicles.user_id','=',$user_id)
                        ->where('vehicles.id','=',$vehicle_id);
                    if($title != 'All categories' && $title != ''){
                          $query->where('activities.title','=',$title);
                        }
                        $final = $query->distinct()->orderBy('actions.last_fuel','desc')->get();
                        $month_array = array("Jan"=>1,"Feb"=>2,"March"=>3,"April"=>4,"May"=>5,"June"=>6,"July"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);
                        foreach ($final as $key => $fnl) {
                          // to get fuel type and client companies below query
                          if(!is_NULL($fnl->fuel_id)){
                              $fuel = Fuel::where('id','=',$fnl->fuel_id)->first();
                              $fnl->fuel_type = $fuel->fuel;
                          }

                          $client = Client::where('clients.id','=',$fnl->client_id)->first();
                          $fnl->company = $client->company;
                          $fnl->location = $client->location;
                          $last_day = date_parse_from_format('Y-m-d', $fnl->filled_fuel)['day'];
                          $last_month = date_parse_from_format('Y-m-d', $fnl->filled_fuel)['month'];
                          $last_year = date_parse_from_format('Y-m-d', $fnl->filled_fuel)['year'];

                          $last_service_day = date_parse_from_format('Y-m-d', $fnl->service_on)['day'];
                          $last_service_month = date_parse_from_format('Y-m-d', $fnl->service_on)['month'];
                          $last_service_year = date_parse_from_format('Y-m-d', $fnl->service_on)['year'];

                          $last_insurance_day = date_parse_from_format('Y-m-d', $fnl->last_insurance)['day'];
                          $last_insurance_month = date_parse_from_format('Y-m-d', $fnl->last_insurance)['month'];
                          $last_insurance_year = date_parse_from_format('Y-m-d', $fnl->last_insurance)['year'];

                          foreach ($month_array as $key => $value) {
                          if($value == $last_month){
                            $month = $key;
                            $fnl->filled_fuel = $last_day . " " . $month. " " .$last_year;
                            }
                            if($value == $last_service_month){
                              $month = $key;
                              $fnl->service_on = $last_service_day . " " . $month. " " .$last_service_year;
                            }
                            if($value == $last_insurance_month){
                              $month = $key;
                              $fnl->last_insurance = $last_insurance_day . " " . $month. " " .$last_insurance_year;
                            }
                          }
                        }
                        return Response::json($final);

                }else {
                  return Response::json('No records available for user',400);
                }
              }

        public function getDashboardHistory(Request $request){
          $user_id = $request->query('user_id');
          $vehicle_id = $request->query('vehicle_id');
           try{
          $activity_fuel = DB::table('activities')
                          ->select('activities.id as activity_id','activities.due_date as next_fuel')
                          ->where('activities.vehicle_id','=',$vehicle_id)
                          ->where('activities.title','=','Fuel filling')
                          ->first();
          $next_fuel = $activity_fuel->next_fuel;

          $activity_service = DB::table('activities')
                          ->select('activities.id as activity_id','activities.due_date as next_service')
                          ->where('activities.vehicle_id','=',$vehicle_id)
                          ->where('activities.title','=','Servicing')
                          ->first();
          $next_service = $activity_service->next_service;

          $activity_insurance = DB::table('activities')
                          ->select('activities.id as activity_id','activities.due_date as next_insurance')
                          ->where('activities.vehicle_id','=',$vehicle_id)
                          ->where('activities.title','=','Insurance')
                          ->first();
          $next_insurance = $activity_insurance->next_insurance;

          // max action_id for last_fuel
          $latest_action_fuel_id =  DB::table('actions')
                               ->select('actions.id')
                               ->where('actions.activity_id',$activity_fuel->activity_id)
                               ->max('actions.id');

          $last_fuel =  DB::table('actions')
                               ->select(\DB::raw('DATE_FORMAT(actions.last_fuel,\'%d-%m-%Y\') as last_fuel'),'actions.average','actions.quantity','actions.meter_reading','actions.fuel_id',\DB::raw('DATE_FORMAT(actions.last_fuel,\'%d-%m-%Y\') as last_fuel'))
                               ->where('actions.id',$latest_action_fuel_id)
                               ->first();
            if($last_fuel){
                $fuel = Fuel::select('fuels.fuel as fuel_type')->where('fuels.id','=',$last_fuel->fuel_id)->first();
                $latest_last_fuel = $last_fuel->last_fuel;
                $fuel_type = $fuel->fuel_type;
                $latest_average = $last_fuel->average;
                $fuel_reading = $last_fuel->meter_reading;
                $quantity = $last_fuel->quantity;
            }else {
            $latest_average = NULL;
            $fuel_reading = NULL;
            $quantity = NULL;
            $fuel_type = NULL;
            $latest_last_fuel = NULL;
            }

            // max action_id for last_service
            $latest_action_service_id =  DB::table('actions')
                                 ->select('actions.id')
                                 ->where('actions.activity_id',$activity_service->activity_id)
                                 ->max('actions.id');

         $last_service = DB::table('actions')
                              ->select(\DB::raw('DATE_FORMAT(actions.last_service,\'%d-%m-%Y\') as last_service'),'actions.meter_reading')
                              ->where('actions.id',$latest_action_service_id)
                              ->first();
          if($last_service){
              $service_reading = $last_service->meter_reading;
              $latest_last_service = $last_service->last_service;
          }else {
          $service_reading = NULL;
          $latest_last_service = NULL;
          }

          // max action_id for last_insurance
          $latest_action_insurance_id = DB::table('actions')
                                  ->select('actions.id')
                                  ->where('actions.activity_id','=',$activity_insurance->activity_id)
                                  ->max('actions.id');

         $last_insurance = DB::table('actions')
                                 ->select(\DB::raw('DATE_FORMAT(actions.last_insurance,\'%d-%m-%Y\') as last_insurance'))
                                 ->where('actions.id','=',$latest_action_insurance_id)
                                 ->first();
            if($last_insurance){
                $latest_last_insurance = $last_insurance->last_insurance;
            }else {
            $latest_last_insurance = NULL;
            }



            $month_array = array("Jan"=>1,"Feb"=>2,"March"=>3,"April"=>4,"May"=>5,"June"=>6,"July"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);

            // for latest_last_fuel
            if($latest_last_fuel){

            $last_day = date_parse_from_format('d-m-Y', $latest_last_fuel)['day'];
            $last_month = date_parse_from_format('d-m-Y', $latest_last_fuel)['month'];
            $last_year = date_parse_from_format('d-m-Y', $latest_last_fuel)['year'];
            foreach ($month_array as $key => $value) {
            if($value == $last_month){
              $month = $key;
              $latest_last_fuel = $last_day . " " . $month;
              }
            }
          }else {
            $latest_last_fuel = NULL;
          }

            // for latest_last_service
            if($latest_last_service){
            $service_last_day = date_parse_from_format('d-m-Y', $latest_last_service)['day'];
            $service_last_month = date_parse_from_format('d-m-Y', $latest_last_service)['month'];
            $service_last_year = date_parse_from_format('d-m-Y', $latest_last_service)['year'];

              foreach ($month_array as $key => $value) {
              if($value == $service_last_month){
                $service_month = $key;
                $latest_last_service_final = $service_last_day . " " . $service_month;
                }
              }

            }else {
              $latest_last_service_final = NULL;
            }

            // for latest_last_insurance
            if($latest_last_insurance){
            $insurance_last_day = date_parse_from_format('d-m-Y', $latest_last_insurance)['day'];
            $insurance_last_month = date_parse_from_format('d-m-Y', $latest_last_insurance)['month'];
            $insurance_last_year = date_parse_from_format('d-m-Y', $latest_last_insurance)['year'];

              foreach ($month_array as $key => $value) {
              if($value == $insurance_last_month){
                $insurance_month = $key;
                $latest_last_insurance_final = $insurance_last_day . " " . $insurance_month;
                }
              }

            }else {
              $latest_last_insurance_final = NULL;
            }

            if($next_fuel){
              $day = date_parse_from_format('Y-m-d', $next_fuel)['day'];
              $month = date_parse_from_format('Y-m-d', $next_fuel)['month'];
              $year = date_parse_from_format('Y-m-d', $next_fuel)['year'];
              foreach ($month_array as $key => $value) {
                if($value == $month){
                  $month = $key;
                  $next_fuel = $day . " " . $month. " " .$year;
                  }
              }
            }
            if($next_service){
              $now = Carbon::now();
              $next_service1 = Carbon::parse($next_service);
              $diff = $next_service1->diffInDays($now);

              if($diff <= 15){
              $day = date_parse_from_format('Y-m-d', $next_service)['day'];
              $month = date_parse_from_format('Y-m-d', $next_service)['month'];
              $year = date_parse_from_format('Y-m-d', $next_service)['year'];
              foreach ($month_array as $key => $value) {
                if($value == $month){
                  $month = $key;
                  $next_service = $day . " " . $month. " " .$year;
                  }
              }
            }else {
              $next_service = NULL;
            }
          }
            if($next_insurance){
              $now = Carbon::now();
              $next_insurance1 = Carbon::parse($next_insurance);
              $diff = $next_insurance1->diffInDays($now);

              if($diff <= 15){
              $day = date_parse_from_format('Y-m-d', $next_insurance)['day'];
              $month = date_parse_from_format('Y-m-d', $next_insurance)['month'];
              $year = date_parse_from_format('Y-m-d', $next_insurance)['year'];
              foreach ($month_array as $key => $value) {
                if($value == $month){
                  $month = $key;
                  $next_insurance = $day . " " . $month. " " .$year;
                  }
              }
            }else {
              $next_insurance = NULL;
            }
          }
            $obj = new VehicleAverage();
            $obj->last_fuel = $latest_last_fuel;
            $obj->last_service = $latest_last_service_final;
            $obj->last_insurance = $latest_last_insurance_final;
            $obj->quantity = $quantity;
            $obj->service_reading = $service_reading;
            $obj->fuel_reading = $fuel_reading;
            $obj->average = $latest_average;
            $obj->next_fuel = $next_fuel;
            $obj->next_service = $next_service;
            $obj->next_insurance = $next_insurance;
            $obj->fuel_type = $fuel_type;
          return Response::json($obj);
        }catch(\Exception $e){
          return Response::json('No data available'.$e->getMessage());
          }
        }

    public function deleteVehicle(Request $request,$vehicle_id = NULL){
      $vehicle_delete = Vehicle::find($vehicle_id);
      $delete = $vehicle_delete->delete();
      if($delete){
        return Response::json('Deleted successfully',200);
      }else {
        return Response::json('Not deleted',400);
      }
    }

    public function getVehicleAverage(Request $request){
      $user_id = $request->query('user_id');
      $vehicle_id = $request->query('vehicle_id');
      try{
      $activity_fuel = DB::table('activities')
                      ->select('activities.id as activity_id')
                      ->where('activities.vehicle_id','=',$vehicle_id)
                      ->where('activities.title','=','Fuel filling')
                      ->first();

      $activity_service = DB::table('activities')
                      ->select('activities.id as activity_id')
                      ->where('activities.vehicle_id','=',$vehicle_id)
                      ->where('activities.title','=','Servicing')
                      ->first();
      // for last_fuel and average
      $max_fuel_action_id =  DB::table('actions')
                           ->select('actions.id')
                           ->where('actions.activity_id',$activity_fuel->activity_id)
                           ->max('actions.id');

      $last_fuel =  DB::table('actions')
                           ->select('actions.average',\DB::raw('DATE_FORMAT(actions.last_fuel,\'%d-%m-%Y\') as last_fuel'))
                           ->where('actions.id','=',$max_fuel_action_id)
                           ->first();
    if($last_fuel){
      $latest_last_fuel = $last_fuel->last_fuel;
      $latest_average = $last_fuel->average;
    }else {
    $latest_last_fuel = NULL;
    $latest_average = NULL;
    }

    // for last_service
    $max_service_action_id = DB::table('actions')
                             ->select('actions.id')
                             ->where('actions.activity_id',$activity_service->activity_id)
                             ->max('actions.id');

     $last_service = DB::table('actions')
                          ->select(\DB::raw('DATE_FORMAT(actions.last_service,\'%d-%m-%Y\') as last_service'))
                          ->where('actions.id','=',$max_service_action_id)
                          ->first();
      if($last_service){
        $latest_last_service = $last_service->last_service;
      }else {
        $latest_last_service = NULL;
      }


        $month_array = array("Jan"=>1,"Feb"=>2,"March"=>3,"April"=>4,"May"=>5,"June"=>6,"July"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);

        // for last_fuel
        if($latest_last_fuel){
        $last_day = date_parse_from_format('d-m-Y', $latest_last_fuel)['day'];
        $last_month = date_parse_from_format('d-m-Y', $latest_last_fuel)['month'];
        foreach ($month_array as $key => $value) {
        if($value == $last_month){
          $month = $key;
          $latest_last_fuel = $last_day . " " . $month;
          }
        }
      }else {
        $latest_last_fuel = NULL;
      }


        // for last_service
        if($latest_last_service){
        $service_last_day = date_parse_from_format('d-m-Y', $latest_last_service)['day'];
        $service_last_month = date_parse_from_format('d-m-Y', $latest_last_service)['month'];

          foreach ($month_array as $key => $value) {
          if($value == $service_last_month){
            $service_month = $key;
            $latest_last_service_final = $service_last_day . " " . $service_month;
            }
          }
        }else {
          $latest_last_service_final = NULL;
        }
        $obj = new VehicleAverage();
        $obj->last_fuel = $latest_last_fuel;
        $obj->last_service = $latest_last_service_final;
        $obj->average = $latest_average;

      return Response::json($obj);
}catch(\Exception $e){
  return Response::json('Data not available',400);
}
    }
}
