<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AssignedGroupCourse;
use App\Models\Course;
use App\Models\CourseGroup;
use Illuminate\Http\Request;

class CourseGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createCourseGroup(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'faculty' => 'required',
            'department' => 'required',
            'level' => 'required',
            'semester' => 'required'
        ]);

        $fac = substr($request->input('faculty'), 0, 3);
        $dept = substr($request->input('department'), 0, 3);

        $fac = strtoupper($fac);
        $dept = strtoupper($dept);
        $year = strtoupper($request->input('level'));
        $sem = strtoupper($request->input('semester'));

        if ($year == 'Y1') {
            $lvl = 100;
        }elseif ($year == 'Y2') {
            $lvl = 200;
        }elseif ($year == 'Y3') {
            $lvl = 300;
        }elseif ($year == 'Y4') {
            $lvl = 400;
        }elseif ($year == 'Y5') {
            $lvl = 500;
        }elseif ($year == 'Y6') {
            $lvl = 600;
        }elseif ($year == 'Y7') {
            $lvl = 700;
        }

        if ($sem == 'SM1') {
            $semester = "First Semester";
        }elseif ($sem == 'SM2') {
            $semester = "Second Semester";
        }

        $shortcode = $fac."|".$dept."|".$year."|".$sem;

        if (CourseGroup::where('shortcode',$shortcode)->exists()) {
            return response()->json(['message' => 'Course Group Already Exist'], 422);
        }

        $group = new CourseGroup;
        $group->faculty = $request->input('faculty');
        $group->department = $request->input('department');
        $group->level = $lvl;
        $group->semester = $semester;
        $group->shortcode = $shortcode;
        $group->save();
        return response()->json(['message' => 'Course Group created successfully', 'course_group' => $shortcode],200);
    }

    public function updateCourseGroup(Request $request,$id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'faculty' => 'required',
            'department' => 'required',
            'level' => 'required',
            'semester' => 'required'
        ]);

        $fac = substr($request->input('faculty'), 0, 3);
        $dept = substr($request->input('department'), 0, 3);

        $fac = strtoupper($fac);
        $dept = strtoupper($dept);
        $year = strtoupper($request->input('level'));
        $sem = strtoupper($request->input('semester'));

        if ($year == 'Y1') {
            $lvl = 100;
        }elseif ($year == 'Y2') {
            $lvl = 200;
        }elseif ($year == 'Y3') {
            $lvl = 300;
        }elseif ($year == 'Y4') {
            $lvl = 400;
        }elseif ($year == 'Y5') {
            $lvl = 500;
        }elseif ($year == 'Y6') {
            $lvl = 600;
        }elseif ($year == 'Y7') {
            $lvl = 700;
        }

        if ($sem == 'SM1') {
            $semester = "First Semester";
        }elseif ($sem == 'SM2') {
            $semester = "Second Semester";
        }

        $shortcode = $fac."|".$dept."|".$year."|".$sem;

        if (CourseGroup::where('shortcode',$shortcode)->exists()) {
            return response()->json(['message' => 'Course Group Already Exist'], 422);
        }

        $group = CourseGroup::find($id);
        $group->faculty = $request->input('faculty');
        $group->department = $request->input('department');
        $group->level = $lvl;
        $group->semester = $semester;
        $group->shortcode = $shortcode;
        $group->save();
        return response()->json(['message' => 'Course Group Updated successfully', 'course_group' => $shortcode],200);
    }

    public function viewCourseGroups()
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $groups = CourseGroup::all();
        if ($groups->count() > 0) {
            return response()->json(['course_group'=>$groups],200);
        }else{
            return response()->json(['message' => 'No Course Group(s) found'], 404);
        }
    }

    public function deleteCourseGroup($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $group = CourseGroup::find($id);
        if (!$group) {
            return response()->json(['message' => 'Course Group not found'], 404);
        }

        $group->delete();
        return response()->json(['message'=>'Course Group Has been deleted'],200);
    }
    
    public function assignCourseToCourseGroup(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'course_group_code' => 'required',
            'courses' => 'required|array',
            'courses.*.course_id' => 'required|integer',
            'courses.*.credit_load' => 'required|integer',
        ]);

        $count = count($request->input('courses'));
        $courses = $request->input('courses');

        foreach($courses as $course){
            try {
                $course_id = $course['course_id'];
                $credit_load = $course['credit_load'];
                if (AssignedGroupCourse::where(['course_id' => $course['course_id'],'course_group_code'=>$request->input('course_group_code')])->exists()) {
                    return response()->json(['message' => 'Course(s) Already Assigned to the Course Group'], 422);
                }
                $course = Course::where('id',$course['course_id'])->first();
                $courseName = $course->course_name;
                $courseCode = $course->course_code;

                $assignCourse = AssignedGroupCourse::create([
                    'course_group_code' => $request->input('course_group_code'),
                    'course_id' => $course_id,
                    'course_title' => $courseName,
                    'course_code' => $courseCode,
                    'credit_load' => $credit_load,
                ]);
            } catch (\Exception $e) {
                AssignedGroupCourse::rollBack();
                throw $e;
            }
        }
        return response()->json(['message'=>$count.' course(s) has been assigned to '.$request->input('course_group_code').' Course Group']);
    }

    public function unassignCourseToCourseGroup(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'course_group_code' => 'required',
            'courses' => 'required|array',
            'courses.*.course_id' => 'required|integer',
            'courses.*.credit_load' => 'required|integer',
        ]);

        $count = count($request->input('courses'));
        $courses = $request->input('courses');

        foreach($courses as $course){
            $course_id = $course['course_id'];

            $course = AssignedGroupCourse::where(['course_id'=>$course_id,'course_group_code' => $request->input('course_group_code')])->first();
            if (!$course) {
                return response()->json(['message' => 'Course not found'], 404);
            }
            $course->delete();

        }
        return response()->json(['message'=>$count.' course(s) has been unassigned from '.$request->input('course_group_code').' Course Group']);
    }

    public function fetchAssignedCourseToCourseGroup($code)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $course = AssignedGroupCourse::where('course_group_code',$code)
                        ->exists();
        if (!$course) {
            return response()->json(['message' => 'Course(s) not found in '.$code.' Course Group'], 404);
        }else{
            $fetched_course = AssignedGroupCourse::where('course_group_code',$code)
                    ->get();
            return response()->json(['course'=>$fetched_course],200);
        }
    }
}
