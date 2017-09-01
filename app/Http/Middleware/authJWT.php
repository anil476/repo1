<?php
namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Exception;
use Log;
class authJWT
{
    public function handle($request, Closure $next)
    {
        try {
              // Log::info("token inside Middleware: ".\JWTAuth::getToken());
             $user = JWTAuth::toUser(\JWTAuth::getToken());
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['error'=>'Access token is Invalid'],401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['error'=>'Access token is Expired'],401);
            }else{
                return response()->json(['error'=>'Access token is missing'],401);
            }
        }
        return $next($request);
    }
}
