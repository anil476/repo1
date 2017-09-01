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
use App\Client;

class ClientController extends Controller
{
public function getClients(Request $request,$company = NULL){
  $clients = Client::select('company')->where('clients.company','like','%' . $company . '%')->distinct()->get();
  if($clients){
    return Response::json($clients);
  }else {
    return Response::json('Clients does not exist',400);
    }
  }

}
