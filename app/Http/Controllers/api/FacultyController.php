<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createFaculty(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'faculty' => 'required',
        ]);

        if (Faculty::where('faculty',$request->input('faculty'))->exists()) {
            return response()->json(['message' => 'Faculty Already Exist'], 422);
        }

        $faculty = new Faculty;
        $faculty->faculty = $request->input('faculty');
        $faculty->save();

        return response()->json(['message' => 'Faculty created successfully', 'faculty' => $faculty],200);
    }

    public function updateFaculty(Request $request,$id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'faculty' => 'required',
        ]);

        $facultyChecked = Faculty::where('id',$id)
                        ->orWhere('faculty',$id)
                        ->exists();
        if (!$facultyChecked) {
            return response()->json(['message' => 'Faculty not found'], 404);
        }

        $faculty = Faculty::find($id);
        $faculty->faculty = $request->input('faculty');
        $faculty->save();

        return response()->json(['message' => 'Faculty updated successfully', 'faculty' => $faculty],200);
    }

    public function viewFaculties()
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $faculty = Faculty::all();
        if ($faculty->count() > 0) {
            return response()->json(['faculties'=>$faculty],200);
        }else{
            return response()->json(['message' => 'No Faculty(s) found'], 404);
        }
    }

    public function viewSingleFaculty($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $facultyChecked = Faculty::where('id',$id)
                        ->orWhere('faculty',$id)
                        ->exists();
        if (!$facultyChecked) {
            return response()->json(['message' => 'Faculty not found'], 404);
        }else{
            $fetched_faculty = Faculty::where('id',  "$id")
                            ->orWhere('faculty', "$id")
                            ->first();
            return response()->json(['faculty'=>$fetched_faculty],200);
        }
    }

    public function deleteFaculty($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $faculty = Faculty::find($id);
        if (!$faculty) {
            return response()->json(['message' => 'Faculty not found'], 404);
        }

        $faculty->delete();
        return response()->json(['message'=>'Faculty Has been deleted'],200);
    }
}
