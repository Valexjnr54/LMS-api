<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createCourse(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'courseTitle' => 'required',
            'courseCode' => 'required',
        ]);

        if (Course::where('course_code',$request->input('courseCode'))->exists()) {
            return response()->json(['message' => 'Course Already Exist'], 422);
        }

        if($request->hasFile('display_image')){
            $file = $request->file('display_image');
            $folder = 'Qverselearning_LMS/images/course_display_avatars';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $displayImage = $uploadedFile->getSecurePath();
        }else{
            $displayImage = 'noImage.jpg';
        }

        if($request->hasFile('course_image')){
            $file = $request->file('course_image');
            $folder = 'Qverselearning_LMS/images/course_avatars';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $courseImage = $uploadedFile->getSecurePath();
        }else{
            $courseImage = 'noImage.jpg';
        }

        $course = new Course();
        $course->course_name = $request->input('courseTitle');
        $course->course_code = $request->input('courseCode');
        $course->description = $request->input('description');
        $course->avatar = $courseImage;
        $course->big_avatar = $displayImage;
        $course->save();
        return response()->json(['message' => 'Course created successfully', 'course' => $course],200);
    }

    public function updateCourse(Request $request,$id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $courseChecked = Course::where('id',$id)
                        ->orWhere('course_code',$id)
                        ->exists();
        if (!$courseChecked) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $this->validate($request,[
            'courseTitle' => 'required',
            'courseCode' => 'required',
        ]);

        $course = Course::find($id);

        if($request->hasFile('display_image')){
            $file = $request->file('display_image');
            $folder = 'Qverselearning_LMS/images/course_display_avatars';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $displayImage = $uploadedFile->getSecurePath();
        }

        if($request->hasFile('course_image')){
            $file = $request->file('course_image');
            $folder = 'Qverselearning_LMS/images/course_avatars';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $courseImage = $uploadedFile->getSecurePath();
        }

        $course->course_name = $request->input('courseTitle');
        $course->course_code = $request->input('courseCode');
        if ($request->input('description') != '') {
            $course->description = $request->input('description');
        }
        if ($request->hasFile('course_image')) {
            $course->avatar = $courseImage;
        }
        if ($request->hasFile('display_image')) {
            $course->big_avatar = $displayImage;
        }
        $course->save();
        return response()->json(['message' => 'Course updated successfully', 'course' => $course],200);
    }

    public function viewCourses()
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $courses = Course::all();
        if ($courses->count() > 0) {
            return response()->json(['courses'=>$courses],200);
        }else{
            return response()->json(['message' => 'No Course(s) found'], 404);
        }
    }

    public function viewSingleCourse($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $course = Course::where('id',$id)
                        ->orWhere('course_code',$id)
                        ->exists();
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }else{
            $fetched_course = Course::where('id',  "$id")
                    ->orWhere('course_code', "$id")
                    ->first();
            return response()->json(['course'=>$fetched_course],200);
        }
    }

    public function deleteCourse($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $course = Course::find($id);
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $course->delete();
        return response()->json(['message'=>'Course Has been deleted'],200);

    }
}
