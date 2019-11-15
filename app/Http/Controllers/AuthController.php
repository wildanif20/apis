<?php

namespace App\Http\Controllers;

use App\Http\Middleware\GetUserFromToken;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Hash;

class AuthController extends Controller
{
    public function __construct()
    { }

    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $token = auth()->login($user);

        return $this->responWithToken($token);
    }


    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                $code = 404;
                $response = ['code' => $code, 'message' => 'email yang anda masukan salah'];                
                return response()->json($response, $code);
            }
        } catch (JWTException $e) {
            $response = ['status' => $e];
            return response()->json($response, 404);
        }
        return $this->responWithToken($token);
    }


    public function logout(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $logout = JWTAuth::invalidate();
        $code = 200;
        $response = [
            'code' => $code,
            'message' => 'berhasil logout',
            'content' => $logout
        ];
        return response()->json($response, $code);
    }


    public function refresh()
    {
        $code = 200;
        $token = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($token);
        $response = ['code' => $code, 'message' => 'New Token', 'content' => $newToken];
        return response()->json($response, $code);
    }

    public function responWithToken($token)
    {
        $code = 200;
        return response()->json(
            [
                'code' => $code,
                'message' => 'success',
                'content' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            ],
            $code
        );
    }

    public function me()
    {
        $code = 200;
        $response = ['code' => $code, 'message' => 'Data user', 'content' => auth()->user()];
        return response()->json($response, $code);
    }

    public function updateName(UpdateProfileRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;
        $name = $request->name;
        $email = $request->email;

        $user = User::find($user_id);
        $user->name = $name;
        $user->email = $email;
        $user->save();

        $code = 200;
        $response = ['code' => $code, 'meesage' => 'berhasil ubah profile'];
        return response()->json($response, $code);
    }


    public function changepassword(ChangePasswordRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $old = $request->old_password;
        $usr = $user->password;
        
        try {
            if (!Hash::check($old, $usr)) {
                $code = 404;
                $response = ['code' => $code, 'message' => 'Password no Match'];
                return response()->json($response, $code);
            }    
        } catch (Exception $e) {
            $response = ['status' => $e];
            return response()->json($response, 404);
        }
        
        $user_id = $user->id;
        $password = $request->password;

        $user = User::find($user_id);
        $user->password = $password;
        $user->save();
        $code = 200;
        $response = ['code' => $code, 'message' => 'berhasil ubah Password'];
        return response()->json($response, $code);
}
}
