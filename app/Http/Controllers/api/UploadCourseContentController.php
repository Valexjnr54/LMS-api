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
use Goutte\Client;

class UploadCourseContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function extractWebContent(Request $request)
    {
        if (auth()->user()->role !== 2) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        if (!$request->input('link')) {
            return response()->json(['message' => 'Url is Required']);
        }

        $link = $request->input('link');

        $client = new Client();
        $crawler = $client->request('GET', $link);

        $title = $crawler->filter('title')->text();
        $headers = $crawler->filter('h1, h2, h3, h4, h5, h6')->each(function ($node) {
            return $node->text();
        });
        $content = $crawler->filter('p')->each(function ($node) {
            return $node->text();
        });

        return response()->json([
            'title' => $title,
            'headers' => $headers,
            'content' => $content,
        ]);
    }

    public function content(Request $request)
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
        $options = $request->input('options');
        // dd($options);

        $uploadCourse = new CourseContent;
        $uploadCourse->lecturer_id = $request->input('lecturer_id');
        $uploadCourse->course_id = $request->input('course_id');
        $uploadCourse->topic = $request->input('topic');
        $uploadCourse->completion_method = $request->input('completion_method');
        $uploadCourse->type = $request->input('type');
        $uploadCourse->content = $request->input('content');
        $uploadCourse->save();

        if ($request->input('completion_method') == 'With Question') {
            $course_id = CourseContent::all()->last()->id;
            $contentQuestion = new CourseContentQuestion();
            $contentQuestion->content_id = $course_id;
            $contentQuestion->question = $request->input('question');
            $contentQuestion->answer = $request->input('answer');
            $contentQuestion->save();
            $question_id = CourseContentQuestion::all()->last()->id;
            foreach ($options as $option) {
                $contentQuestionOption = new CourseContentOption();
                $contentQuestionOption->content_id = $course_id;
                $contentQuestionOption->question_id = $question_id;
                $contentQuestionOption->option = $option;
                $contentQuestionOption->save();
            }
        }

        return response()->json(['message' => 'Content for '.$courseName.' has been uploaded successfully'],200);
    }

    public function webContent(Request $request)
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

        $uploadCourse = new CourseContent;
        $uploadCourse->lecturer_id = $request->input('lecturer_id');
        $uploadCourse->course_id = $request->input('course_id');
        $uploadCourse->topic = $request->input('topic');
        $uploadCourse->completion_method = $request->input('completion_method');
        $uploadCourse->type = $request->input('type');
        $uploadCourse->content = $request->input('content');
        $uploadCourse->content_url = $request->input('url');
        $uploadCourse->save();

        if ($request->input('completion_method') == 'With Question') {
            $course_id = CourseContent::all()->last()->id;
            $contentQuestion = new CourseContentQuestion();
            $contentQuestion->content_id = $course_id;
            $contentQuestion->question = $request->input('question');
            $contentQuestion->answer = $request->input('answer');
            $contentQuestion->save();
            $question_id = CourseContentQuestion::all()->last()->id;
            foreach ($options as $option) {
                $contentQuestionOption = new CourseContentOption();
                $contentQuestionOption->content_id = $course_id;
                $contentQuestionOption->question_id = $question_id;
                $contentQuestionOption->option = $option;
                $contentQuestionOption->save();
            }
        }

        return response()->json(['message' => 'Content for '.$courseName.' has been uploaded successfully'],200);
    }

    public function fileContent(Request $request)
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

        $uploadCourse = new CourseContent;
        $uploadCourse->lecturer_id = $request->input('lecturer_id');
        $uploadCourse->course_id = $request->input('course_id');
        $uploadCourse->topic = $request->input('topic');
        $uploadCourse->completion_method = $request->input('completion_method');
        $uploadCourse->type = $request->input('type');
        $uploadCourse->file_url = $filePath;
        $uploadCourse->save();

        if ($request->input('completion_method') == 'With Question') {
            $course_id = CourseContent::all()->last()->id;
            $contentQuestion = new CourseContentQuestion();
            $contentQuestion->content_id = $course_id;
            $contentQuestion->question = $request->input('question');
            $contentQuestion->answer = $request->input('answer');
            $contentQuestion->save();
            $question_id = CourseContentQuestion::all()->last()->id;
            foreach ($options as $option) {
                $contentQuestionOption = new CourseContentOption();
                $contentQuestionOption->content_id = $course_id;
                $contentQuestionOption->question_id = $question_id;
                $contentQuestionOption->option = $option;
                $contentQuestionOption->save();
            }
        }

        return response()->json(['message' => 'Content for '.$courseName.' has been uploaded successfully'],200);

    }

    public function videoContent(Request $request)
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

        $uploadCourse = new CourseContent;
        $uploadCourse->lecturer_id = $request->input('lecturer_id');
        $uploadCourse->course_id = $request->input('course_id');
        $uploadCourse->topic = $request->input('topic');
        $uploadCourse->completion_method = $request->input('completion_method');
        $uploadCourse->type = $request->input('type');
        $uploadCourse->video_url = $filePath;
        $uploadCourse->save();

        if ($request->input('completion_method') == 'With Question') {
            $course_id = CourseContent::all()->last()->id;
            $contentQuestion = new CourseContentQuestion();
            $contentQuestion->content_id = $course_id;
            $contentQuestion->question = $request->input('question');
            $contentQuestion->answer = $request->input('answer');
            $contentQuestion->save();
            $question_id = CourseContentQuestion::all()->last()->id;
            foreach ($options as $option) {
                $contentQuestionOption = new CourseContentOption();
                $contentQuestionOption->content_id = $course_id;
                $contentQuestionOption->question_id = $question_id;
                $contentQuestionOption->option = $option;
                $contentQuestionOption->save();
            }
        }

        return response()->json(['message' => 'Content for '.$courseName.' has been uploaded successfully'],200);
    }

    public function audioContent(Request $request)
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

        $uploadCourse = new CourseContent;
        $uploadCourse->lecturer_id = $request->input('lecturer_id');
        $uploadCourse->course_id = $request->input('course_id');
        $uploadCourse->topic = $request->input('topic');
        $uploadCourse->completion_method = $request->input('completion_method');
        $uploadCourse->type = $request->input('type');
        $uploadCourse->audio_url = $filePath;
        $uploadCourse->save();

        if ($request->input('completion_method') == 'With Question') {
            $course_id = CourseContent::all()->last()->id;
            $contentQuestion = new CourseContentQuestion();
            $contentQuestion->content_id = $course_id;
            $contentQuestion->question = $request->input('question');
            $contentQuestion->answer = $request->input('answer');
            $contentQuestion->save();
            $question_id = CourseContentQuestion::all()->last()->id;
            foreach ($options as $option) {
                $contentQuestionOption = new CourseContentOption();
                $contentQuestionOption->content_id = $course_id;
                $contentQuestionOption->question_id = $question_id;
                $contentQuestionOption->option = $option;
                $contentQuestionOption->save();
            }
        }

        return response()->json(['message' => 'Content for '.$courseName.' has been uploaded successfully'],200);

    }
}
