<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Activity;
use App\ActivityCategory;
use App\Client;
use App\ClientCategory;
use App\Action;
use App\ActionStatus;
use Log;
use DB;
use Carbon\Carbon;

class ActionController extends Controller
{
    public function postActions(Request $request){
    // Log::info('activity_id : '.$request->query('activity_id'));
    // Log::info('fuel_id : '.$request->query('fuel_id'));
    // Log::info('company : '.$request['company']);
    // Log::info('location : '.$request['location']);
    // Log::info('quantity : '.$request['quantity']);
    // Log::info('meter_reading : '.$request['meter_reading']);
    // Log::info('remark : '.$request['remark']);
    $activity_id = $request->query('activity_id');

    // start Here
    $activity = Activity::where('id','=',$activity_id)->first();
    if($activity){
        if($activity->title == 'Insurance'){
            $last_insurance = Carbon::parse($request['date']);
            $last_fuel = NULL;
            $last_service = NULL;
            $fuel_id = NULL;
            if($request['agent_contact'] != '')
            $agent_contact = $request['agent_contact'];
            else
            $agent_contact = NULL;
            if($request['renewal_date'] != '')
            $due_date = Carbon::parse($request['renewal_date']);
            else
            $due_date = Carbon::parse($last_insurance)->addYears(1);
            $quantity = NULL;
            $meter_reading = NULL;
            $remark = $request['remark'];
        }
        if($activity->title == 'Fuel filling'){
            $fuel_id = $request->query('fuel_id');
            $last_fuel = Carbon::parse($request['date']);
            $last_service = NULL;
            $last_insurance = NULL;
            $agent_contact = NULL;
            $due_date = Carbon::parse($last_fuel)->addDays(7);
            $quantity = $request['quantity'];
            $meter_reading = $request['meter_reading'];
            $remark = $request['remark'];
          }
        if($activity->title == 'Servicing'){
            $fuel_id = $request->query('fuel_id');
            $last_fuel = NULL;
            $last_insurance = NULL;
            $agent_contact = NULL;
            $last_service = Carbon::parse($request['date']);
            $due_date = Carbon::parse($last_service)->addMonths(3);
            $quantity = NULL;
            $meter_reading = $request['meter_reading'];
            $remark = $request['remark'];
          }

    }// end here

    $check = Client::where('company','=',$request['company'])
                    ->where('location','=',$request['location'])
                    ->first();
    if($check){
      $client_id = $check->id;
    }else {
    $activity = Activity::where('id','=',$activity_id)->first();
    if($activity->title == 'Fuel filling'){
      $station = 'Fuel station';
      $client_category = ClientCategory::where('category','like','%' .$station. '%')->first();
      $client = new Client();
      $client->location = $request['location'];
      $client->company =  $request['company'];
      $client->client_category_id = $client_category->id;
      $client->save();
      $client_id = $client->id;
    }
    if($activity->title == 'Insurance'){
      $station = 'Insurance company';
      $client_category = ClientCategory::where('category','like','%' .$station. '%')->first();
      $client = new Client();
      $client->location = NULL;
      $client->company =  $request['company'];
      $client->client_category_id = $client_category->id;
      $client->save();
      $client_id = $client->id;
    }
    if($activity->title == 'Servicing') {
      $station = 'Service center';
      $client_category = ClientCategory::where('category','like','%' .$station. '%')->first();
      $client = new Client();
      $client->location = $request['location'];
      $client->company =  $request['company'];
      $client->client_category_id = $client_category->id;
      $client->save();
      $client_id = $client->id;
    }

  }

  // get just last records before inserting new record
  $just_last_fuel = DB::table('actions')
            ->select('actions.last_fuel')
            ->where('actions.activity_id','=',$activity_id)
            ->max('actions.last_fuel');
  if($just_last_fuel){
      $just_last_entry = Action::select('actions.meter_reading','actions.quantity')
                          ->where('actions.activity_id','=',$activity_id)
                          ->where('actions.last_fuel','=',$just_last_fuel)
                          ->first();
  }
      // add action
      $action = new Action();
      $action->activity_id = $activity_id;
      $action->client_id = $client_id;
      $action->fuel_id = $fuel_id;
      $action->quantity = $quantity;
      $action->last_fuel = $last_fuel;
      $action->last_insurance = $last_insurance;
      $action->agent_contact = $agent_contact;
      $action->last_service = $last_service;
      $action->meter_reading = $meter_reading;
      $action->remark = $request['remark'];
      $action->save();
      $last_action_id = $action->id;

      // update due_date according to fuel filling,servicing and insurance
      $update_due_date = Activity::find($activity_id);
      $update_due_date->due_date = $due_date;
      $update_due_date->save();


      // get latest record just after inserting the action
      $latest_entry = Action::select('actions.meter_reading','actions.quantity')
                          ->where('actions.activity_id','=',$activity_id)
                          ->where('actions.id','=',$last_action_id)
                          ->first();

      // now calculate the average of vehicle
      if($just_last_fuel){
          $avg = ($latest_entry->meter_reading - $just_last_entry->meter_reading)/$just_last_entry->quantity;
          // Log::info("Average: ".$avg);
      }

      if($just_last_fuel){
        $action_update = Action::find($last_action_id);
        $action_update->average = $avg;
        $action_update->save();
      }

      return Response::json($action);
    }

    public function getActionStatus(Request $request){
      $status = ActionStatus::all();
      if($status){
        return Response::json($status);
      }else {
        return Response::json('Action status does not exist');
      }
    }


}
