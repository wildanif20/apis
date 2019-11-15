<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use JWTAuth;
class GetUserFromToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $code = 401;
        $status = array('success' => false, 'code' => $code, 'message' => 'token not found');
        if (! $token = $this->auth->setRequest($request)->getToken()){
            $response = ['code' => $code, 'message' => 'Authorization not provider'];
            return response()->json($response, $code);
        } 
        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            $message = 'Authorization expired';
            $code;
            $response =['code' => $code, 'message' => $message];
            return response()->json($response, $code);
        } catch (JWTException $e) {
            $message = 'Authorization is invalid';
            $response = ['code' => $code, 'message' => $message];
                return response()->json($response, $code);
        } 

        if (! $user){
            $message = 'user not found';
            $response = ['code' => $code, 'message' => $response];
            return response()->json($response, $code);
        }   

        return $next($request);
    }
}
