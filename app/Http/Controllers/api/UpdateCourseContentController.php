<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AssignedCourse;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\CourseContentOption;
use App\Models\CourseContentQuestion;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateCourseContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function content(Request $request,$id)
    {
        if (auth()->user()->role !== 2) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'lecturer_id' => 'required',
            'course_id' => 'required',
            'completion_method' => 'required',
            'type' => 'required',
            'topic' => 'required',
        ]);

        if ($request->input('completion_method') == 'With Question') {
            $this->validate($request,[
                'question' => 'required',
                'option' => 'array',
                'answer' => 'required',
            ]);
        }

        if ($request->input('type') == 'content') {
            $this->validate($request,[
                'content' => 'required',
            ]);
        }

        $user = User::where('id',$request->input('lecturer_id'))->first();
        if ($user->role !== 2) {
            return response()->json(['message' => 'User is not a Lecturer'],401);
        }

        if (!AssignedCourse::where(['course_id' =>$request->input('course_id'),'lecturer_id'=>$request->input('lecturer_id')])->exists()) {
            return response()->json(['message' => 'Course not Assigned to the lecturer'], 422);
        }

        $course = Course::where('id',$request->input('course_id'))->first();
        $courseName = $course->course_name;
        $options = $request->input('option');
        // dd($options);

        $uploadCourse = CourseContent::find($id);
        $uploadCourse->lecturer_id = $request->input('lecturer_id');
        $uploadCourse->course_id = $request->input('course_id');
        $uploadCourse->topic = $request->input('topic');
        $uploadCourse->completion_method = $request->input('completion_method');
        $uploadCourse->type = $request->input('type');
        $uploadCourse->content = $request->input('content');
        $uploadCourse->save();

        if ($request->input('completion_method') == 'With Question') {
            $course_id = $id;
            $contentQuestion = CourseContentQuestion::where('content_id',$id)->first();
            $contentQuestion->content_id = $course_id;
            $contentQuestion->question = $request->input('question');
            $contentQuestion->answer = $request->input('answer');
            $contentQuestion->save();
            $question_id = $contentQuestion->id;
            foreach ($options as $option) {
                $contentQuestionOption = CourseContentOption::where(['content_id' => $id,'question_id' => $question_id])->first();
                $contentQuestionOption->content_id = $course_id;
                $contentQuestionOption->question_id = $question_id;
                $contentQuestionOption->option = $option;
                $contentQuestionOption->save();
            }
        }

        return response()->json(['message' => 'Content for '.$courseName.' has been updated successfully'],200);
    }

    public function webContent(Request $request, $id)
    {
        if (auth()->user()->role !== 2) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'lecturer_id' => 'required',
            'course_id' => 'required',
            'completion_method' => 'required',
            'type' => 'required',
            'topic' => 'required',
        ]);

        if ($request->input('completion_method') == 'With Question') {
            $this->validate($request,[
                'question' => 'required',
                'options' => 'array',
                'answer' => 'required',
            ]);
        }

        if ($request->input('type') == 'web content') {
            $this->validate($request,[
                'content' => 'required',
                'url' => 'required|active_url'
            ]);
        }

        $user = User::where('id',$request->input('lecturer_id'))->first();
        if ($user->role !== 2) {
            return response()->json(['message' => 'User is not a Lecturer'],401);
        }

        if (!AssignedCourse::where(['course_id' =>$request->input('course_id'),'lecturer_id'=>$request->input('lecturer_id')])->exists()) {
            return response()->json(['message' => 'Course not Assigned to the lecturer'], 422);
        }

        $course = Course::where('id',$request->input('course_id'))->first();
        $courseName = $course->course_name;
        $options = $request->input('options');
        // dd($options);

        $uploadCourse = CourseContent::find($id);
        $uploadCourse->lecturer_id = $request->input('lecturer_id');
        $uploadCourse->course_id = $request->input('course_id');
        $uploadCourse->topic = $request->input('topic');
        $uploadCourse->completion_method = $request->input('completion_method');
        $uploadCourse->type = $request->input('type');
        $uploadCourse->content = $request->input('content');
        $uploadCourse->content_url = $request->input('url');
        $uploadCourse->save();

        if ($request->input('completion_method') == 'With Question') {
            $course_id = $id;
            $contentQuestion = CourseContentQuestion::where('content_id',$id)->first();
            $contentQuestion->content_id = $course_id;
            $contentQuestion->question = $request->input('question');
            $contentQuestion->answer = $request->input('answer');
            $contentQuestion->save();
            $question_id = $contentQuestion->id;
            foreach ($options as $option) {
                $contentQuestionOption = CourseContentOption::where(['content_id' => $id,'question_id' => $question_id])->first();
                $contentQuestionOption->content_id = $course_id;
                $contentQuestionOption->question_id = $question_id;
                $contentQuestionOption->option = $option;
                $contentQuestionOption->save();
            }
        }

        return response()->json(['message' => 'Content for '.$courseName.' has been updated successfully'],200);
    }

    public function fileContent(Request $request, $id)
    {
        if (auth()->user()->role !== 2) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }
        $this->validate($request,[
            'lecturer_id' => 'required',
            'course_id' => 'required',
            'completion_method' => 'required',
            'type' => 'required',
            'topic' => 'required',
        ]);

        if ($request->input('completion_method') == 'With Question') {
            $this->validate($request,[
                'question' => 'required',
                'options' => 'array',
                'answer' => 'required',
            ]);
        }

        if ($request->input('type') == 'file upload') {
            $this->validate($request,[
                'file' => 'required'
            ]);
        }

        $user = User::where('id',$request->input('lecturer_id'))->first();
        if ($user->role !== 2) {
            return response()->json(['message' => 'User is not a Lecturer'],401);
        }

        if (!AssignedCourse::where(['course_id' =>$request->input('course_id'),'lecturer_id'=>$request->input('lecturer_id')])->exists()) {
            return response()->json(['message' => 'Course not Assigned to the lecturer'], 422);
        }

        $course = Course::where('id',$request->input('course_id'))->first();
        $courseName = $course->course_name;
        $options = $request->input('options');
        // dd($options);

        if($request->hasFile('file')){
            $file = $request->file('file');
            $folder = 'Qverselearning_LMS/files/course_files';
            $uploadedFile = cloudinary()->uploadFile($file->getRealPath(), [
                'folder' => $folder
            ]);

            $filePath = $uploadedFile->getSecurePath();
        }

        $uploadCourse = CourseContent::find($id);
        $uploadCourse->lecturer_id = $request->input('lecturer_id');
        $uploadCourse->course_id = $request->input('course_id');
        $uploadCourse->topic = $request->input('topic');
        $uploadCourse->completion_method = $request->input('completion_method');
        $uploadCourse->type = $request->input('type');
        $uploadCourse->file_url = $filePath;
        $uploadCourse->save();

        if ($request->input('completion_method') == 'With Question') {
            $course_id = CourseContent::all()->last()->id;
            $contentQuestion = CourseContentQuestion::where('content_id',$id)->first();
            $contentQuestion->content_id = $course_id;
            $contentQuestion->question = $request->input('question');
            $contentQuestion->answer = $request->input('answer');
            $contentQuestion->save();
            $question_id = $contentQuestion->id;
            foreach ($options as $option) {
                $contentQuestionOption = CourseContentOption::where(['content_id' => $id,'question_id' => $question_id])->first();
                $contentQuestionOption->content_id = $course_id;
                $contentQuestionOption->question_id = $question_id;
                $contentQuestionOption->option = $option;
                $contentQuestionOption->save();
            }
        }

        return response()->json(['message' => 'Content for '.$courseName.' has been updated successfully'],200);

    }

    public function videoContent(Request $request, $id)
    {
        if (auth()->user()->role !== 2) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }
        $this->validate($request,[
            'lecturer_id' => 'required',
            'course_id' => 'required',
            'completion_method' => 'required',
            'type' => 'required',
            'topic' => 'required',
        ]);

        if ($request->input('completion_method') == 'With Question') {
            $this->validate($request,[
                'question' => 'required',
                'options' => 'array',
                'answer' => 'required',
            ]);
        }

        if ($request->input('type') == 'video upload') {
            $this->validate($request,[
                'file' => 'required'
            ]);
        }

        $user = User::where('id',$request->input('lecturer_id'))->first();
        if ($user->role !== 2) {
            return response()->json(['message' => 'User is not a Lecturer'],401);
        }

        if (!AssignedCourse::where(['course_id' =>$request->input('course_id'),'lecturer_id'=>$request->input('lecturer_id')])->exists()) {
            return response()->json(['message' => 'Course not Assigned to the lecturer'], 422);
        }

        $course = Course::where('id',$request->input('course_id'))->first();
        $courseName = $course->course_name;
        $options = $request->input('options');
        // dd($options);

        if($request->hasFile('file')){
            $file = $request->file('file');
            $folder = 'Qverselearning_LMS/videos/course_videos';
            $uploadedFile = cloudinary()->uploadVideo($file->getRealPath(), [
                'folder' => $folder
            ]);

            $filePath = $uploadedFile->getSecurePath();
        }

        $uploadCourse = CourseContent::find($id);
        $uploadCourse->lecturer_id = $request->input('lecturer_id');
        $uploadCourse->course_id = $request->input('course_id');
        $uploadCourse->topic = $request->input('topic');
        $uploadCourse->completion_method = $request->input('completion_method');
        $uploadCourse->type = $request->input('type');
        $uploadCourse->video_url = $filePath;
        $uploadCourse->save();

        if ($request->input('completion_method') == 'With Question') {
            $course_id = CourseContent::all()->last()->id;
            $contentQuestion = CourseContentQuestion::where('content_id',$id)->first();
            $contentQuestion->content_id = $course_id;
            $contentQuestion->question = $request->input('question');
            $contentQuestion->answer = $request->input('answer');
            $contentQuestion->save();
            $question_id = $contentQuestion->id;
            foreach ($options as $option) {
                $contentQuestionOption = CourseContentOption::where(['content_id' => $id,'question_id' => $question_id])->first();
                $contentQuestionOption->content_id = $course_id;
                $contentQuestionOption->question_id = $question_id;
                $contentQuestionOption->option = $option;
                $contentQuestionOption->save();
            }
        }

        return response()->json(['message' => 'Content for '.$courseName.' has been updated successfully'],200);
    }

    public function audioContent(Request $request, $id)
    {
        if (auth()->user()->role !== 2) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }
        $this->validate($request,[
            'lecturer_id' => 'required',
            'course_id' => 'required',
            'completion_method' => 'required',
            'type' => 'required',
            'topic' => 'required',
        ]);

        if ($request->input('completion_method') == 'With Question') {
            $this->validate($request,[
                'question' => 'required',
                'options' => 'array',
                'answer' => 'required',
            ]);
        }

        if ($request->input('type') == 'audio upload') {
            $this->validate($request,[
                'file' => 'required'
            ]);
        }

        $user = User::where('id',$request->input('lecturer_id'))->first();
        if ($user->role !== 2) {
            return response()->json(['message' => 'User is not a Lecturer'],401);
        }

        if (!AssignedCourse::where(['course_id' =>$request->input('course_id'),'lecturer_id'=>$request->input('lecturer_id')])->exists()) {
            return response()->json(['message' => 'Course not Assigned to the lecturer'], 422);
        }

        $course = Course::where('id',$request->input('course_id'))->first();
        $courseName = $course->course_name;
        $options = $request->input('options');
        // dd($options);

        if($request->hasFile('file')){
            $file = $request->file('file');
            $folder = 'Qverselearning_LMS/audios/course_audios';
            $uploadedFile = cloudinary()->uploadFile($file->getRealPath(), [
                'folder' => $folder
            ]);

            $filePath = $uploadedFile->getSecurePath();
        }

        $uploadCourse = CourseContent::find($id);
        $uploadCourse->lecturer_id = $request->input('lecturer_id');
        $uploadCourse->course_id = $request->input('course_id');
        $uploadCourse->topic = $request->input('topic');
        $uploadCourse->completion_method = $request->input('completion_method');
        $uploadCourse->type = $request->input('type');
        $uploadCourse->audio_url = $filePath;
        $uploadCourse->save();

        if ($request->input('completion_method') == 'With Question') {
            $course_id = CourseContent::all()->last()->id;
            $contentQuestion = CourseContentQuestion::where('content_id',$id)->first();
            $contentQuestion->content_id = $course_id;
            $contentQuestion->question = $request->input('question');
            $contentQuestion->answer = $request->input('answer');
            $contentQuestion->save();
            $question_id = $contentQuestion->id;
            foreach ($options as $option) {
                $contentQuestionOption = CourseContentOption::where(['content_id' => $id,'question_id' => $question_id])->first();
                $contentQuestionOption->content_id = $course_id;
                $contentQuestionOption->question_id = $question_id;
                $contentQuestionOption->option = $option;
                $contentQuestionOption->save();
            }
        }

        return response()->json(['message' => 'Content for '.$courseName.' has been updated successfully'],200);

    }
}
