<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createDepartment(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'faculty_id' => 'required',
            'department' => 'required'
        ]);

        if (Department::where(['faculty_id' => $request->input('faculty_id'),'department' => $request->input('department')])->exists()) {
            return response()->json(['message' => 'Department Already Exist'], 422);
        }

        $department = new Department;
        $department->faculty_id = $request->input('faculty_id');
        $department->department = $request->input('department');
        $department->save();

        return response()->json(['message' => 'Department created successfully', 'department' => $department],200);
    }

    public function updateDepartment(Request $request,$id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'faculty_id' => 'required',
            'department' => 'required'
        ]);

        $departmentChecked = Department::where('id',$id)
                        ->orWhere('department',$id)
                        ->exists();
        if (!$departmentChecked) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        $department = Department::find($id);
        $department->faculty_id = $request->input('faculty_id');
        $department->department = $request->input('department');
        $department->save();

        return response()->json(['message' => 'Department updated successfully', 'faculty' => $department],200);
    }

    public function viewDepartments()
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $department = Department::all();
        if ($department->count() > 0) {
            return response()->json(['department'=>$department],200);
        }else{
            return response()->json(['message' => 'No Department(s) found'], 404);
        }
    }

    public function viewSingleDepartment($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $departmentChecked = Department::where('id',$id)
                        ->orWhere('department',$id)
                        ->exists();
        if (!$departmentChecked) {
            return response()->json(['message' => 'No Department(s) Found'], 404);
        }else{
            $fetched_department = Department::where('id',  "$id")
                            ->orWhere('department', "$id")
                            ->first();
            return response()->json(['department'=>$fetched_department],200);
        }
    }

    public function departmentsByFaculty($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $departmentChecked = Department::where('faculty_id',$id)->exists();
        if (!$departmentChecked) {
            return response()->json(['message' => 'No Department(s) Found'], 404);
        }else{
            $fetched_department = Department::where('faculty_id',"$id")->get();
            return response()->json(['departments'=>$fetched_department],200);
        }
    }

    public function deleteDepartment($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $department = Department::find($id);
        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        $department->delete();
        return response()->json(['message'=>'Department Has been deleted'],200);
    }
}
