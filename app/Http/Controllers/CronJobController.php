<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Psr\Http\Message\ServerRequestInterface;
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
use Excel;

ini_set('max_execution_time', 900);
class CronJobController extends Controller
{
  public function postFuels(){

    $hp = $this->getHPPrice();
    if($hp){
        $essar = $this->getEssarPrice();
        if($essar){
          $iocl = $this->getIOCLPrice();
          if($iocl){
            $done = $this->getBPCLPrice();
               if($done){
                 return 'successfully done..!!';
               }
          }
        }
    }
  }


public function getHPPrice(){
$arr = array();
// Log::info("called by cronjob");
//HP use seperate statecode for AP,BH,ML as API,BR,ME
$statecode = array(
                   "AN"=>"Andaman and Nicobar Islands",
                  // "AP"=>"Andhra Pradesh",
                  "AP1"=>"Andhra Pradesh",
                   "AR"=>"Arunachal Pradesh",
                  "AS"=>"Assam",
                  // "BH"=>"Bihar",
                  "BR"=>"Bihar",
                  "CH"=>"Chandigarh",
                  "CT"=>"Chattisgarh",
                  "DN"=>"Dadra and Nagar Haveli",
                  "DD"=>"Daman and Diu",
                  "DL"=>"Delhi",
                  "GA"=>"Goa",
                  "GJ"=>"Gujarat",
                  "HR"=>"Haryana",
                  "HP"=>"Himachal Pradesh",
                  "JK"=>"Jammu and Kashmir",
                  "JH"=>"Jharkhand",
                  "KA"=>"Karnataka",
                  "KL"=>"Kerala",
                  "LD"=>"Lakshadweep Islands",
                  "MP"=>"Madhya Pradesh",
                  "MH"=>"Maharashtra",
                  "MN"=>"Manipur",
                  "ML"=>"Meghalaya",
                  // "ME"=>"Meghalaya",
                  // "MI"=>"Mizoram",
                  "MZ"=>"Mizoram",
                  "NL"=>"Nagaland",
                  "OR"=>"Orrisa",
                  "PY"=>"Pondicherry",
                  "PB"=>"Punjab",
                  "RJ"=>"Rajasthan",
                  "SK"=>"Sikkim",
                  "TN"=>"Tamil Nadu",
                  // "TS"=>"Telangana",
                  "TG"=>"Telangana",
                  "TR"=>"Tripura",
                  "UP"=>"Uttar Pradesh",
                  "UT"=>"Uttarakhand",
                  "WB"=>"West Bengal"
                );

        $petrol = Fuel::where('fuel','=','Petrol')->first();
        if($petrol){
          $fuel_id_petrol = $petrol->id;
        }


        $diesel = Fuel::where('fuel','=','Diesel')->first();
        if($diesel){
            $fuel_id_diesel = $diesel->id;
        }

        $category = FuelCategory::where('category','=','Normal')->first();
        if($category){
            $fuel_category_id = $category->id;
        }

        $company = Company::where('company','=','Hindustan Petroleum')->first();
        if($company){
              $company_id = $company->id;
        }

foreach ($statecode as $key => $value) {
  try{
  // Log::info("state_code: ".$value);
    $api =  "http://hproroute.hpcl.co.in/StateDistrictMap_4/fetchmshsdprice.jsp?param=T&statecode=" . $key . "?1497245430417";
    $client = new Client();
    $res = $client->request('GET', $api);
    $state = $value;
    $state_code = $key;

    if($res->getStatusCode() == 200){
        $xml = $res->getBody();
        $xml = preg_replace('!^[^>]+>(\r\n|\n|\r|/)!','',$xml);
        $xml = simplexml_load_string($xml);
    //  $arrayData = xmlToArray($xmlNode);
    if(isset($xml->marker)){
    foreach ($xml->marker as $key => $value){
    //  Log::info("xml marker :".$value['townname']);
    $city = City::where('city_name','=',$value['townname'])->first();
    if($city){
    // Log::info("xml marker :".$value['townname']);
     $city_id = $city->id;
     $fuel = FuelPrice::where('city_id','=',$city_id)->where('company_id','=',$company_id)->first();
     if($fuel){
         $fuel_update = DB::table('fuel_prices')
                        ->where('fuel_prices.fuel_id','=',$fuel_id_petrol)
                        ->where('fuel_prices.city_id','=',$city_id)
                        ->where('fuel_prices.company_id','=',$company_id)
                        ->update(['fuel_prices.price'=>$value['ms'],'fuel_prices.created_at'=>Carbon::now()]);

         $fuel_update = DB::table('fuel_prices')
                        ->where('fuel_prices.fuel_id','=',$fuel_id_diesel)
                        ->where('fuel_prices.city_id','=',$city_id)
                        ->where('fuel_prices.company_id','=',$company_id)
                        ->update(['fuel_prices.price'=>$value['hsd'],'fuel_prices.created_at'=>Carbon::now()]);


   }else{
         $fuel_price = new FuelPrice();
         $fuel_price->fuel_id = $fuel_id_petrol;
         $fuel_price->fuel_category_id = $fuel_category_id;
         $fuel_price->company_id = $company_id;
         $fuel_price->city_id = $city_id;
         $fuel_price->price = $value['ms'];
         $fuel_price->save();

         $fuel_price = new FuelPrice();
         $fuel_price->fuel_id = $fuel_id_diesel;
         $fuel_price->fuel_category_id = $fuel_category_id;
         $fuel_price->company_id = $company_id;
         $fuel_price->city_id = $city_id;
         $fuel_price->price = $value['hsd'];
         $fuel_price->save();
   }

}else {
    //  Log::info("xml marker :".$value['townname']);
      $city = new City();
      $city->city_name = $value['townname'];
      $city->city_state = $state;
      $city->state_code = $state_code;
      $city->lng = $value['lng'];
      $city->lat = $value['lat'];
      $city->save();

      $city_id = $city->id;

      $fuel_price = new FuelPrice();
      $fuel_price->fuel_id = $fuel_id_petrol;
      $fuel_price->fuel_category_id = $fuel_category_id;
      $fuel_price->company_id = $company_id;
      $fuel_price->city_id = $city_id;
      $fuel_price->price = $value['ms'];
      $fuel_price->save();

        $fuel_price = new FuelPrice();
        $fuel_price->fuel_id = $fuel_id_diesel;
        $fuel_price->fuel_category_id = $fuel_category_id;
        $fuel_price->company_id = $company_id;
        $fuel_price->city_id = $city_id;
        $fuel_price->price = $value['hsd'];
        $fuel_price->save();


}


            }
          }else {
            // Log::info("not set");
            }
        }
      }catch(\Exception $e){
        // Log::info("Error detected: ".$e->getMessage());
      }
    }
    // Log::info("updated successfully for hpcl");
    return true;
  }

  public function getEssarPrice(){
    // Log::info("called by cronjob for essar");
    $statecode = array(
                      // "AN"=>"Andaman and Nicobar Islands",
                      "85"=>"Andhra Pradesh",
                      "1874"=>"Arunachal Pradesh",
                      "416"=>"Assam",
                      "728"=>"Bihar",
                      "363"=>"Chandigarh",
                      "225"=>"Chattisgarh",
                      "1846"=>"Dadra and Nagar Haveli",
                      "676"=>"Daman and Diu",
                      "100"=>"Delhi",
                      "1334"=>"Goa",
                      "284"=>"Gujarat",
                      "56"=>"Haryana",
                      "1008"=>"Himachal Pradesh",
                      "1312"=>"Jammu and Kashmir",
                      "355"=>"Jharkhand",
                      "160"=>"Karnataka",
                      "206"=>"Kerala",
                      // "LD"=>"Lakshadweep Islands",
                      "274"=>"Madhya Pradesh",
                      "187"=>"Maharashtra",
                      "1654"=>"Manipur",
                      "1203"=>"Meghalaya",
                      // "ME"=>"Meghalaya",
                      // "MI"=>"Mizoram",
                      "1791"=>"Mizoram",
                      "2370"=>"Nagaland",
                      "317"=>"Orrisa",
                      "1596"=>"Pondicherry",
                      "377"=>"Punjab",
                      "306"=>"Rajasthan",
                      "2320"=>"Sikkim",
                      "128"=>"Tamil Nadu",
                      // "TS"=>"Telangana",
                      "3000"=>"Telangana",
                      "1918"=>"Tripura",
                      "138"=>"Uttar Pradesh",
                      "1200"=>"Uttarakhand",
                      "258"=>"West Bengal"
                    );
            $petrol = Fuel::where('fuel','=','Petrol')->first();
            if($petrol){
              $fuel_id_petrol = $petrol->id;
            }

            $diesel = Fuel::where('fuel','=','Diesel')->first();
            if($diesel){
                $fuel_id_diesel = $diesel->id;
            }

            $category = FuelCategory::where('category','=','Normal')->first();
            if($category){
                $fuel_category_id = $category->id;
            }

            $company = Company::where('company','=','Essar Oil')->first();
            if($company){
                  $company_id = $company->id;
            }
            foreach ($statecode as $key => $v) {
              try{
              $state = $v;
              $state_code = $key;
              $client = new Client();
              $response = $client->request('POST','https://www.essaroil.co.in/MyWebService.asmx/FindExistingROSByFilter',array(
                'headers' =>array('Content-type'=>'application/json'),
                'json'=>array('stateId' => $key,'district'=> "",'city'=>"",'cmdCode'=>"")
              ));
               $data = json_decode($response->getBody(), true);
              foreach ($data as $key => $value) {
                 $EssarData = json_decode($value, true);
               }
                 foreach ($EssarData as $key => $val) {
                   $city = City::where('city_name','=',$val['cityName'])->first();
                   if($city){
                     $city_id = $city->id;
                     $petrol_price = $val['PetrolPrice'];
                     $diesel_price =  $val['DieselPrice'];
                     $fuel = FuelPrice::where('city_id','=',$city_id)->where('company_id','=',$company_id)->first();
                     if($fuel){

                       $fuel_update = DB::table('fuel_prices')
                                        ->where('fuel_prices.fuel_id','=',$fuel_id_petrol)
                                        ->where('fuel_prices.city_id','=',$city_id)
                                        ->where('fuel_prices.company_id','=',$company_id)
                                        ->update(['fuel_prices.price'=>$petrol_price,'fuel_prices.created_at'=>Carbon::now()]);

                       $fuel_update = DB::table('fuel_prices')
                                        ->where('fuel_prices.fuel_id','=',$fuel_id_diesel)
                                        ->where('fuel_prices.city_id','=',$city_id)
                                        ->where('fuel_prices.company_id','=',$company_id)
                                        ->update(['fuel_prices.price'=>$diesel_price,'fuel_prices.created_at'=>Carbon::now()]);

                     }else {
                         $fuel_price = new FuelPrice();
                         $fuel_price->fuel_id = $fuel_id_petrol;
                         $fuel_price->fuel_category_id = $fuel_category_id;
                         $fuel_price->company_id = $company_id;
                         $fuel_price->city_id = $city_id;
                         $fuel_price->price = $petrol_price;
                         $fuel_price->save();

                         $fuel_price = new FuelPrice();
                         $fuel_price->fuel_id = $fuel_id_diesel;
                         $fuel_price->fuel_category_id = $fuel_category_id;
                         $fuel_price->company_id = $company_id;
                         $fuel_price->city_id = $city_id;
                         $fuel_price->price = $diesel_price;
                         $fuel_price->save();
                     }
                   }else {
                       // Log::info("city not present :".$val['cityName']);
                      if($val['cityName'] != ''){
                        $city = new City();
                        $city->city_name = $val['cityName'];
                        $city->city_state = $state;
                        $city->state_code = $state_code;
                        $city->lng = $val['LONGITUDE'];
                        $city->lat = $val['LATITUDE'];
                        $city->save();


                        $city_id = $city->id;

                        $fuel_price = new FuelPrice();
                        $fuel_price->fuel_id = $fuel_id_petrol;
                        $fuel_price->fuel_category_id = $fuel_category_id;
                        $fuel_price->company_id = $company_id;
                        $fuel_price->city_id = $city_id;
                        $fuel_price->price = $val['PetrolPrice'];
                        $fuel_price->save();

                        $fuel_price = new FuelPrice();
                        $fuel_price->fuel_id = $fuel_id_diesel;
                        $fuel_price->fuel_category_id = $fuel_category_id;
                        $fuel_price->company_id = $company_id;
                        $fuel_price->city_id = $city_id;
                        $fuel_price->price = $val['DieselPrice'];
                        $fuel_price->save();

                    }
                   }

                }
              }catch(\Exception $e){
                //Log::info("Error detected: ".$e->getMessage());
              }
            }//foreach closed for statecode array
            Log::info("updated successfully for essar");
            return true;

  }

  public function getBPCLPrice(){

    $petrol = Fuel::where('fuel','=','Petrol')->first();
    if($petrol){
      $fuel_id_petrol = $petrol->id;
    }

    $diesel = Fuel::where('fuel','=','Diesel')->first();
    if($diesel){
        $fuel_id_diesel = $diesel->id;
    }

    $category = FuelCategory::where('category','=','Normal')->first();
    if($category){
        $fuel_category_id = $category->id;
    }

    $company = Company::where('company','=','Bharat Petroleum')->first();
    if($company){
          $company_id = $company->id;
    }

    $cities = City::all();
    foreach ($cities as $key => $c) {
      if(!is_NULL($c->custID)){
          $cust_id = $c->custID;
          // Log::info("cust id: ".$cust_id);
        try{
        $client = new Client();
        $api = "http://www.smartfleetonline.co.in/smartmap/search_google2new.jsp?custID=".$cust_id;
        $res  = $client->request('GET',$api);
        if($res->getStatusCode() == 200){
          // Log::info("status code: ".$response->getStatusCode());
          // Log::info("content type: ".$response->getHeaderLine('content-type'));
        $xml = $res->getBody();
        // $xml = preg_replace('!^[^>]+>(\r\n|\n|\r|/)!','',$xml);
        $xml = simplexml_load_string($xml);
        if(isset($xml->marker2)){
          foreach ($xml->marker2 as $key => $value) {
            // Log::info("customer id : ".$value['custID']);
            $city_name = $value['city'];
            $petrol_price = $value['petrol'];
            $diesel_price = $value['diesel'];
            $city = City::where('city_name','=',$city_name)->first();
            $city_id = $city->id;
            $fuel = FuelPrice::where('city_id','=',$city_id)->where('company_id','=',$company_id)->first();
            if($fuel){

              $fuel_update = DB::table('fuel_prices')
                               ->where('fuel_prices.fuel_id','=',$fuel_id_petrol)
                               ->where('fuel_prices.city_id','=',$city_id)
                               ->where('fuel_prices.company_id','=',$company_id)
                               ->update(['fuel_prices.price'=>$petrol_price,'fuel_prices.created_at'=>Carbon::now()]);

              $fuel_update = DB::table('fuel_prices')
                               ->where('fuel_prices.fuel_id','=',$fuel_id_diesel)
                               ->where('fuel_prices.city_id','=',$city_id)
                               ->where('fuel_prices.company_id','=',$company_id)
                               ->update(['fuel_prices.price'=>$diesel_price,'fuel_prices.created_at'=>Carbon::now()]);

            }else {
                $fuel_price = new FuelPrice();
                $fuel_price->fuel_id = $fuel_id_petrol;
                $fuel_price->fuel_category_id = $fuel_category_id;
                $fuel_price->company_id = $company_id;
                $fuel_price->city_id = $city_id;
                $fuel_price->price = $petrol_price;
                $fuel_price->save();

                $fuel_price = new FuelPrice();
                $fuel_price->fuel_id = $fuel_id_diesel;
                $fuel_price->fuel_category_id = $fuel_category_id;
                $fuel_price->company_id = $company_id;
                $fuel_price->city_id = $city_id;
                $fuel_price->price = $diesel_price;
                $fuel_price->save();
                }
              }//foreach loop closed for marker2
            }// if closed for marker2
          } //if closed for status code 200
         }catch(\Exception $e){
          // Log::info("error detected : ".$e->getMessage());
          // Log::info("custID :".$c->custID);
            }
          }

         }//foreach loop closed for cities
        // Log::info("updated successfully for bpcl");
        return true;

}


  public function getIOCLPrice(){
    // Log::info("called by cronjob for iocl");
    $statecode = array(
                      "AN"=>"Andaman and Nicobar Islands",
                      "AP"=>"Andhra Pradesh",
                      "ARP"=>"Arunachal Pradesh",
                      "AS"=>"Assam",
                      "BH"=>"Bihar",
                      "CD"=>"Chandigarh",
                      "CSG"=>"Chattisgarh",
                      "DH"=>"Dadra and Nagar Haveli",
                      "DD"=>"Daman and Diu",
                      "DEL"=>"Delhi",
                      "GDD"=>"Goa",
                      "GJ"=>"Gujarat",
                      "HR"=>"Haryana",
                      "HP"=>"Himachal Pradesh",
                      "JK"=>"Jammu and Kashmir",
                      "JRK"=>"Jharkhand",
                      "KAR"=>"Karnataka",
                      "KER"=>"Kerala",
                      "LD"=>"Lakshadweep Islands",
                      "MP"=>"Madhya Pradesh",
                      "MAH"=>"Maharashtra",
                      "MNP"=>"Manipur",
                      "MGL"=>"Meghalaya",
                      // "ME"=>"Meghalaya",
                      // "MI"=>"Mizoram",
                      "MZ"=>"Mizoram",
                      "NG"=>"Nagaland",
                      "OR"=>"Orrisa",
                      "PY"=>"Pondicherry",
                      "PB"=>"Punjab",
                      "RJ"=>"Rajasthan",
                      "SK"=>"Sikkim",
                      "TN"=>"Tamil Nadu",
                      // "TS"=>"Telangana",
                      "TG"=>"Telangana",
                      "TRP"=>"Tripura",
                      "UP"=>"Uttar Pradesh",
                      "UTK"=>"Uttarakhand",
                      "WB"=>"West Bengal"
                    );
                    $petrol = Fuel::where('fuel','=','Petrol')->first();
                    if($petrol){
                      $fuel_id_petrol = $petrol->id;
                    }

                    $diesel = Fuel::where('fuel','=','Diesel')->first();
                    if($diesel){
                        $fuel_id_diesel = $diesel->id;
                    }

                    $category = FuelCategory::where('category','=','Normal')->first();
                    if($category){
                        $fuel_category_id = $category->id;
                    }

                    $company = Company::where('company','=','Indian Oil')->first();
                    if($company){
                          $company_id = $company->id;
                    }
        foreach ($statecode as $key => $value) {
          try{
          $state = $value;
          $state_code = $key;
          $iocl_array = array();
          $curl = curl_init();
          curl_setopt_array($curl, array(
          CURLOPT_URL => "https://associates.indianoil.co.in/PumpLocator/StateWiseLocator?state=".$key,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          // Log::info("failed");
          // return "cURL Error #:" . $err;
        } else {
          //  Log::info($response);
          $string = preg_replace('/\.$/', '', $response); //Remove dot at end if exists
          $array_iocl = explode(',', $string);
          foreach($array_iocl as $key => $iocl){
            $city = City::where('city_name','=',$iocl)->first();
            if($city){
              $city_id = $city->id;
              $diesel_price = $array_iocl[$key-8];
              $petrol_price =  $array_iocl[$key-9];
              $fuel = FuelPrice::where('city_id','=',$city_id)->where('company_id','=',$company_id)->first();
              // Log::info("fuel:".$fuel);
              if($fuel){
                // Log::info("inside if:".$iocl);
                // return 'if';

                 $fuel_update = DB::table('fuel_prices')
                                  ->where('fuel_prices.fuel_id','=',$fuel_id_petrol)
                                  ->where('fuel_prices.city_id','=',$city_id)
                                  ->where('fuel_prices.company_id','=',$company_id)
                                  ->update(['fuel_prices.price'=>$petrol_price,'fuel_prices.created_at'=>Carbon::now()]);

                 $fuel_update = DB::table('fuel_prices')
                                  ->where('fuel_prices.fuel_id','=',$fuel_id_diesel)
                                  ->where('fuel_prices.city_id','=',$city_id)
                                  ->where('fuel_prices.company_id','=',$company_id)
                                  ->update(['fuel_prices.price'=>$diesel_price,'fuel_prices.created_at'=>Carbon::now()]);


                 }else {
                  //  Log::info("else got");
                  //  return 'else';
                       $fuel_price = new FuelPrice();
                       $fuel_price->fuel_id = $fuel_id_petrol;
                       $fuel_price->fuel_category_id = $fuel_category_id;
                       $fuel_price->company_id = $company_id;
                       $fuel_price->city_id = $city_id;
                       $fuel_price->price = $petrol_price;
                       $fuel_price->save();

                       $fuel_price = new FuelPrice();
                       $fuel_price->fuel_id = $fuel_id_diesel;
                       $fuel_price->fuel_category_id = $fuel_category_id;
                       $fuel_price->company_id = $company_id;
                       $fuel_price->city_id = $city_id;
                       $fuel_price->price = $diesel_price;
                       $fuel_price->save();
                }
              }else {
                  // Log::info("else no city present :");

                }
              }
            }
          }catch(\Exception $e){
            // Log::info("error detected: ".$e->getMessage());
          }
        } //foreach closed for statecode array
        // Log::info("updated successfully for iocl");
        return true;

  }//function closed


} //class closed
