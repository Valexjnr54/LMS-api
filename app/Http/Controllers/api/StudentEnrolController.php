<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AssignedGroupCourse;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\CreditLoad;
use App\Models\StudentEnroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentEnrolController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function fetchCoursesToEnroll(Request $request)
    {
        if (auth()->user()->role !== 3) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'semester' => 'required'
        ]);

        $level = auth()->user()->level;
        $department = auth()->user()->dept;
        $faculty = auth()->user()->faculty;

        $fac = substr($faculty, 0, 3);
        $dept = substr($department, 0, 3);

        $fac = strtoupper($fac);
        $dept = strtoupper($dept);
        $sem = strtoupper($request->input('semester'));

        if ($level == 100) {
            $year = 'Y1';
        }elseif ($level == 200) {
            $year = 'Y2';
        }elseif ($level == 300) {
            $year = 'Y3';
        }elseif ($level == 400) {
            $year = 'Y4';
        }elseif ($level == 500) {
            $year = 'Y5';
        }elseif ($level == 600) {
            $year = 'Y6';
        }elseif ($level == 700) {
            $year = 'Y7';
        }

        $shortcode = $fac."|".$dept."|".$year."|".$sem;

        if (CourseGroup::where('shortcode',$shortcode)->exists()) {
            $courses = AssignedGroupCourse::where(['course_group_code' => $shortcode])->get();
            return response()->json(['courses' => $courses], 200);
        }else{
            return response()->json(['message' => 'No Course Group(s) available'], 422);
        }
    }

    public function enrollCourse(Request $request)
    {
        if (auth()->user()->role !== 3) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'semester' => 'required',
            'courses' => 'required|array',
            'courses.*.course_id' => 'required|integer',
            'courses.*.credit_load' => 'required|integer',
        ]);

        $count = count($request->input('courses'));
        $courses = $request->input('courses');
        $sem = $request->input('semester');

        $department = auth()->user()->dept;
        $level = auth()->user()->level;

        $dept = substr($department, 0, 3);
        $dept = strtoupper($dept);
        $sem = strtoupper($request->input('semester'));

        if ($level == 100) {
            $year = 'Y1';
        }elseif ($level == 200) {
            $year = 'Y2';
        }elseif ($level == 300) {
            $year = 'Y3';
        }elseif ($level == 400) {
            $year = 'Y4';
        }elseif ($level == 500) {
            $year = 'Y5';
        }elseif ($level == 600) {
            $year = 'Y6';
        }elseif ($level == 700) {
            $year = 'Y7';
        }

        if ($sem == 'SM1') {
            $semester = "First Semester";
        }elseif ($sem == 'SM2') {
            $semester = "Second Semester";
        }

        $shortcode = $dept."|".$year."|".$sem;

        $credit_load_sum = array_sum(array_column($courses, 'credit_load'));

        $studEnroll = StudentEnroll::where(['student_id' => auth()->user()->id,'semester' => $semester,'exam_taken' => 0, 'exam_passed' => 0, 'status' => 'active'])->sum('credit_load');

        $credLoad = CreditLoad::where('shortcode',$shortcode)->first();
        $minLoad = $credLoad->min_load;
        $maxLoad = $credLoad->max_load;

        if ($studEnroll >= $maxLoad) {
            return response()->json(['message' => 'You Have Reached The Maximum Credit Load for this Semester and Level'], 500);
        }

        if (CreditLoad::where('shortcode',$shortcode)->exists()) {
            if ($credit_load_sum < $minLoad) {
                return response()->json(['message' => 'Minimum Credit Load Required is ' .$minLoad], 500);
            }elseif ($credit_load_sum > $maxLoad) {
                return response()->json(['message' => 'Maximum Credit Load Required is ' .$maxLoad], 500);
            }

            foreach ($courses as $course) {
                try {
                    $course_id = $course['course_id'];
                    $credit_load = $course['credit_load'];
                    $course = Course::where('id',$course['course_id'])->first();
                    $courseName = $course->course_name;
                    $courseCode = $course->course_code;
                    if (StudentEnroll::where(['course_id' => $course['course_id'],'student_id'=> auth()->user()->id])->exists()) {
                        return response()->json(['message' => auth()->user()->last_name.' '.auth()->user()->first_name.' has already enrolled to '.$courseName], 422);
                    }

                    $enroll = StudentEnroll::create([
                        'student_id' => auth()->user()->id,
                        'semester' => $semester,
                        'course_id' =>  $course_id,
                        'course_code' => $courseCode,
                        'course_title' => $courseName,
                        'credit_load' => $credit_load
                    ]);
                } catch (\Throwable $e) {
                    StudentEnroll::rollBack();
                    throw $e;
                }
            }
            return response()->json(['message'=> auth()->user()->last_name.' '.auth()->user()->first_name.' has successfully enrolled to '.$count.' Course(s)']);

        }else {
            return response()->json(['message' => 'Invalid Server Error'], 500);
        }
    }
}
