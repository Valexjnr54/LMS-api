<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except' => ['login','register']]);
    }

    public function register(Request $request)
    {
        $this->validate($request,[
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'gender' => 'required',
            'username' => 'required',
            'role' => 'required',
            'level' => 'required',
            'dept' => 'required',
            'faculty' => 'required',
            'reg_number' => 'required',
            'password' => 'required',
        ]);
        if ($request->input('role') == 3) {
            $user_type = 'Student';
        }else {
            return response()->json(['message' => 'Invalid Role Entry'], 403);
        }

        if($request->hasFile('image')){
            $file = $request->file('image');
            $folder = 'Qverselearning_LMS/images/user_avatars';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $fileNameToStore = $uploadedFile->getSecurePath();
        }else{
            $fileNameToStore = 'noImage.jpg';
        }

        $user = new User;
        $user->first_name = $request->input('firstName');
        $user->last_name = $request->input('lastName');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->gender = $request->input('gender');
        $user->level = $request->input('level');
        $user->dept = $request->input('dept');
        $user->faculty = $request->input('faculty');
        $user->reg_number = $request->input('reg_number');
        $user->bio = $request->input('bio');
        $user->avatar = $fileNameToStore;
        $user->role = $request->input('role');
        $user->user_type= $user_type;
        $user->password = bcrypt($request->input('password'));
        $user->save();

        // Generate JWT token for the user
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'message' => 'Registration successful',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ], 201);
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

        if (auth()->user()->role !== 3) {
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
