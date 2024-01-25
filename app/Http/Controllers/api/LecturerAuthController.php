<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LecturerAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['errors' =>'Invalid Email / Password'], 401);
        }

        if (auth()->user()->role !== 2) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }
        return $this->createToken($token);
    }

    public function createToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'User Logged out successful'
        ]);
    }
}
