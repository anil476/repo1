<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Log;
use App\Activity;
use App\ActivityCategory;
use DB;
use App\User;

class ActivityController extends Controller
{
    // public function registerActivity(Request $request, $vehicle_id = NULL, $category_id = NULL){
    //       if($vehicle_id && $category_id){
    //         $activity = new Activity();
    //         $activity->vehicle_id = $vehicle_id;
    //         $activity->title = $request['title'];
    //         $activity->description = $request['description'];
    //         $activity->due_date = $request['due_date'];
    //         $activity->recurring = $request['recurring'];
    //         $activity->every = $request['every'];
    //         $activity->month_day = $request['month_day'];
    //         $activity->week_day = $request['week_day'];
    //         $activity->year_day = $request['year_day'];
    //         $activity->save();
    //         return Response::json($activity);
    //       }else {
    //         return Response::json('Missing vehicle_id or category_id',400);
    //     }
    // }

    //used to show in drop down all categories
    public function getActivityCategories(){
      $activityCategory = Activity::select('title')->distinct()->get();
      if($activityCategory){
        return Response::json($activityCategory);
      }else {
        return Response::json('Activity category does not exist',400);
      }
    }

    // used when drop down select for Activitycategory and show whole activities
    public function getActivities(Request $request){
      $vehicle_id = $request->query('vehicle_id');
      $user_id = $request->query('user_id');
      $title = $request->query('title');

          $query = DB::table('users')
                        ->join('vehicles','vehicles.user_id','=','users.id')
                        ->join('activities','activities.vehicle_id','=','vehicles.id')
                        ->select('activities.id as activity_id','activities.title','activities.due_date')
                        ->where('users.id','=',$user_id)
                        ->where('activities.vehicle_id','=',$vehicle_id);
                      if($title != 'All categories' && $title != ''){
                            $query->where('activities.title','=',$title);
                        }
                        $activities = $query->distinct()->orderBy('activities.due_date','DESC')->get();
                        if($activities){
                          $month_array = array("Jan"=>1,"Feb"=>2,"March"=>3,"April"=>4,"May"=>5,"June"=>6,"July"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);
                          foreach ($activities as $key => $act) {
                            $day = date_parse_from_format('Y-m-d', $act->due_date)['day'];
                            $month = date_parse_from_format('Y-m-d', $act->due_date)['month'];
                            $year = date_parse_from_format('Y-m-d', $act->due_date)['year'];
                            foreach ($month_array as $key => $value) {
                            if($value == $month){
                              $month = $key;
                              $act->due_date = $day . " " . $month. " " .$year;
                              }
                            }
                          }
                            return Response::json($activities);
                        }else {
                            return Response::json('data not available',400);
                        }


                    }
                  }
