<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AssignedCourse;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseAssignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function assignedCourses($lecturer_id)
    {
        if (auth()->user()->role !== 2 && auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }
        $user = User::where('id',$lecturer_id)->first();
        if ($user->role !== 2) {
            return response()->json(['message' => 'User is not a Lecturer'],401);
        }

        $courses = AssignedCourse::where('lecturer_id',$lecturer_id)->get();

        if ($courses->count() > 0) {
            return response()->json(['courses'=>$courses],200);
        }else{
            return response()->json(['message' => 'No Course(s) found'], 404);
        }
    }

    public function assignCourses(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'lecturer_id' => 'required',
            'course_id' => 'required|array',
        ]);

        $user = User::where('id',$request->input('lecturer_id'))->first();
        if ($user->role !== 2) {
            return response()->json(['message' => 'User is not a Lecturer'],401);
        }
        $lecturerName = $user->last_name.' '.$user->first_name;
        $count = count($request->input('course_id'));

        $courseIds = $request->input('course_id');

        foreach ($courseIds as $courseId) {
            if (AssignedCourse::where(['course_id' =>$courseId,'lecturer_id'=>$request->input('lecturer_id')])->exists()) {
                return response()->json(['message' => 'Course(s) Already Assigned to the lecturer'], 422);
            }
            $course = Course::where('id',$courseId)->first();
            $courseName = $course->course_name;
            $courseCode = $course->course_code;

            $assignCourse = new AssignedCourse;
            $assignCourse->lecturer_id = $request->input('lecturer_id');
            $assignCourse->course_id = $courseId;
            $assignCourse->course_name = $courseName;
            $assignCourse->course_code = $courseCode;
            $assignCourse->save();
        }
        return response()->json(['message'=>$count.' course(s) has been assigned to '.$lecturerName]);
    }

    public function unassignCourses(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'lecturer_id' => 'required',
            'course_id' => 'required|array',
        ]);

        $user = User::where('id',$request->input('lecturer_id'))->first();
        if ($user->role !== 2) {
            return response()->json(['message' => 'User is not a Lecturer'],401);
        }
        $lecturerName = $user->last_name.' '.$user->first_name;
        $count = count($request->input('course_id'));

        $courseIds = $request->input('course_id');
        
        foreach ($courseIds as $courseId) {
            // if (!AssignedCourse::where(['course_id' =>$courseId,'lecturer_id'=>$request->input('lecturer_id')])->exists()) {
            //     return response()->json(['message' => 'Course(s) not Assigned to the lecturer'], 422);
            // }
            // $course = Course::where('id',$courseId)->first();
            // $courseName = $course->course_name;
            // $courseCode = $course->course_code;

            $assignCourse = AssignedCourse::where(['course_id' => $courseId,'lecturer_id' => $request->input('lecturer_id')])
                                            ->update(['isAssign' => false]);
            // $assignCourse->isAssign = false;
            // $assignCourse->update();
        }
        return response()->json(['message'=>$count.' course(s) has been unassigned from '.$lecturerName]);
    }

}
