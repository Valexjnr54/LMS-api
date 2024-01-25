<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // Admin Create User Section
    public function createUser(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'gender' => 'required',
            'username' => 'required',
            'role' => 'required',
            'password' => 'required'
        ]);
        if ($request->input('role') == 1) {
            $user_type = 'Admin';
        }elseif ($request->input('role') == 2) {
            $user_type = 'Lecturer';
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
        $user->bio = $request->input('bio');
        $user->avatar = $fileNameToStore;
        $user->role = $request->input('role');
        $user->user_type= $user_type;
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return response()->json(['message' => 'User created successfully', 'user' => $user],200);
    }

    public function updateUser(Request $request,$id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $this->validate($request,[
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'gender' => 'required',
            'username' => 'required',
            'role' => 'required',
        ]);



        if($request->hasFile('image')){
            $file = $request->file('image');
            $folder = 'Qverselearning_LMS/images/user_avatars';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $fileNameToStore = $uploadedFile->getSecurePath();
        }

        if ($request->input('role') == 1) {
            $user_type = 'Admin';
        }elseif ($request->input('role') == 2) {
            $user_type = 'Lecturer';
        }else {
            return response()->json(['message' => 'Invalid Role Entry'], 403);
        }

        $user->first_name = $request->input('firstName');
        $user->last_name = $request->input('lastName');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->gender = $request->input('gender');
        $user->bio = $request->input('bio');
        if($request->hasFile('image')){
            $user->avatar = $fileNameToStore;
        }
        $user->role = $request->input('role');
        $user->user_type= $user_type;
        $user->save();
        return response()->json(['message' => 'User updated successfully', 'user' => $user],200);
    }

    public function viewUsers()
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $users = User::where('role', 1)
                        ->orWhere('role', 2)->get();
        if ($users->count() > 0) {
            return response()->json(['users'=>$users],200);
        } else {
            return response()->json(['message' => 'No user(s) found'], 404);
        }
    }

    public function lecturer()
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $users = User::where('role', 2)->get();
        if ($users->count() > 0) {
            return response()->json(['lecturers'=>$users],200);
        } else {
            return response()->json(['message' => 'No Lecturer(s) found'], 404);
        }
    }

    public function admin()
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $users = User::where('role', 1)->get();
        if ($users->count() > 0) {
            return response()->json(['admins'=>$users],200);
        } else {
            return response()->json(['message' => 'No Admin(s) found'], 404);
        }
    }

    public function viewSingleUser($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $user = User::where('id',  "$id")
                    ->orWhere('username', "$id")
                    ->orWhere('email', "$id")
                    ->exists();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }else{
            $fetched_user = User::where('id',  "$id")
                    ->orWhere('username', "$id")
                    ->orWhere('email', "$id")
                    ->first();
            if ($fetched_user->role === 1 || $fetched_user->role === 2) {
                return response()->json($fetched_user,200);
            }else{
                return response()->json(['message' => 'Invalid Route to fetch that user'], 404);
            }
        }
    }

    public function deleteUser($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->role === 1 || $user->role === 2) {
            $user->delete();
            return response()->json(['message'=>'User Has been deleted'],200);
        }else{
            return response()->json(['message' => 'Invalid Route to fetch that user'], 404);
        }
    }
    // End of Admin Create User Section


    //Admin Create Student Section
    public function createStudent(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

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
        return response()->json(['message' => 'User created successfully', 'user' => $user],200);
    }

    public function updateStudent(Request $request,$id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

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
        ]);

        if($request->hasFile('image')){
            $file = $request->file('image');
            $folder = 'Qverselearning_LMS/images/user_avatars';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $fileNameToStore = $uploadedFile->getSecurePath();
        }

        if ($request->input('role') == 3) {
            $user_type = 'Student';
        }else {
            return response()->json(['message' => 'Invalid Role Entry'], 403);
        }

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
        if($request->hasFile('image')){
            $user->avatar = $fileNameToStore;
        }
        $user->role = $request->input('role');
        $user->user_type= $user_type;
        $user->save();
        return response()->json(['message' => 'Student updated successfully', 'user' => $user],200);
    }

    public function viewStudents()
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $users = User::where('role', 3)->get();
        if ($users->count() > 0) {
            return response()->json(['users'=>$users],200);
        } else {
            return response()->json(['message' => 'No users found'], 404);
        }
    }

    public function viewSingleStudent($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $user = User::where('id',  "$id")
                    ->orWhere('username', "$id")
                    ->orWhere('email', "$id")
                    ->exists();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }else{
            $fetched_user = User::where('id',  "$id")
                    ->orWhere('username', "$id")
                    ->orWhere('email', "$id")
                    ->first();
            if ($fetched_user->role === 3) {
                return response()->json($fetched_user,200);
            }else{
                return response()->json(['message' => 'Invalid Route to fetch that user'], 404);
            }
        }
    }

    public function deleteStudent($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }
        
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->role === 3) {
            $user->delete();
            return response()->json(['message'=>'User Has been deleted'],200);
        }else{
            return response()->json(['message' => 'Invalid Route to fetch that user'], 404);
        }
        // $user->delete();
        // return response()->json(['message'=>'User Has been deleted'],200);
    }
    //Admin Student Section Ends
}
