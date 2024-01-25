<?php

use App\Http\Controllers\api\UsersController;
use App\Http\Controllers\api\CourseController;
use App\Http\Controllers\api\AdminAuthController;
use App\Http\Controllers\api\LecturerAuthController;
use App\Http\Controllers\api\StudentAuthController;
use App\Http\Controllers\api\CourseAssignController;
use App\Http\Controllers\api\CourseGroupController;
use App\Http\Controllers\api\CreditLoadController;
use App\Http\Controllers\api\DepartmentController;
use App\Http\Controllers\api\FacultyController;
use App\Http\Controllers\api\StudentEnrolController;
use App\Http\Controllers\api\UploadCourseContentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    //User Route Starts
    Route::prefix('users')->group(function(){
        Route::get('/all-users', [UsersController::class, 'viewUsers']);
        Route::get('/single-user/{id}', [UsersController::class, 'viewSingleUser']);
        Route::delete('/delete-user/{id}', [UsersController::class, 'deleteUser']);
        Route::match(['put', 'patch','post'], '/update-user/{id}', [UsersController::class, 'updateUser']);
        Route::post('/create-user', [UsersController::class, 'createUser']);
        Route::get('/all-lecturers', [UsersController::class, 'lecturer']);
    });
    //User Route Ends

    //Student Route Starts
    Route::prefix('students')->group(function(){
        Route::get('/all-students', [UsersController::class, 'viewStudents']);
        Route::get('/single-student/{id}', [UsersController::class, 'viewSingleStudent']);
        Route::delete('/delete-student/{id}', [UsersController::class, 'deleteStudent']);
        Route::match(['put', 'patch','post'], '/update-student/{id}', [UsersController::class, 'updateStudent']);
        Route::post('/create-student', [UsersController::class, 'createStudent']);
    });
    //Student Route Ends

    //Course Route Starts
    Route::prefix('courses')->group(function(){
        Route::get('/all-courses',[CourseController::class, 'viewCourses']);
        Route::get('/single-course/{id}',[CourseController::class, 'viewSingleCourse']);
        Route::delete('/delete-course/{id}',[CourseController::class, 'deleteCourse']);
        Route::match(['put','patch','post'], '/update-course/{id}', [CourseController::class, 'updateCourse']);
        Route::post('/create-course',[CourseController::class, 'createCourse']);
    });
    //Course Route Ends

    //Admin Authentication Route Starts
    Route::prefix('admin')->group(function()
    {
        Route::group(['middleware' => ['api']], function () {
            Route::post('/register', [AdminAuthController::class, 'register']);
            Route::post('/login', [AdminAuthController::class, 'login']);
            Route::get('/profile', [AdminAuthController::class, 'profile']);
            Route::post('/logout', [AdminAuthController::class, 'logout']);
        });
    });
    //Admin Authentication Route Ends

    //Lecturer Authentication Route Starts
    Route::prefix('lecturer')->group(function()
    {
        Route::group(['middleware' => ['api']], function () {
            Route::post('/login', [LecturerAuthController::class, 'login']);
            Route::get('/profile', [LecturerAuthController::class, 'profile']);
            Route::post('/logout', [LecturerAuthController::class, 'logout']);
        });
    });
    //Lecturer Authentication Route Ends

    //Student Authentication Route Starts
    Route::prefix('student')->group(function()
    {
        Route::group(['middleware' => ['api']], function () {
            Route::post('/register', [StudentAuthController::class, 'register']);
            Route::post('/login', [StudentAuthController::class, 'login']);
            Route::get('/profile', [StudentAuthController::class, 'profile']);
            Route::post('/logout', [StudentAuthController::class, 'logout']);
        });
    });
    //Student Authentication Route Ends

    //Assign & Unassign Courses Route Starts
    Route::prefix('assign-unassign')->group(function(){
        Route::group(['middleware' => ['api']],function()
        {
            Route::post('/assign-course', [CourseAssignController::class, 'assignCourses']);
            Route::post('/unassign-course', [CourseAssignController::class, 'unassignCourses']);
            Route::get('/assigned-course/{lecturer_id}', [CourseAssignController::class, 'assignedCourses']);
        });
    });
    //Assign & Unassign Courses Route Ends

    //Upload Course Route Starts
    Route::prefix('upload-course')->group(function(){
        Route::group(['middleware' => ['api']],function()
        {
            Route::get('/extract-content', [UploadCourseContentController::class, 'extractWebContent']);
            Route::post('/upload-content', [UploadCourseContentController::class, 'content']);
            Route::post('/upload-webcontent', [UploadCourseContentController::class, 'webContent']);
            Route::post('/upload-filecontent', [UploadCourseContentController::class, 'fileContent']);
            Route::post('/upload-videocontent', [UploadCourseContentController::class, 'videoContent']);
            Route::post('/upload-audiocontent', [UploadCourseContentController::class, 'audioContent']);
        });
    });
    //Upload Course Route Ends

    //Update Course Route Starts
    Route::prefix('update-course')->group(function(){
        Route::group(['middleware' => ['api']],function()
        {
            Route::match(['put','patch','post'],'/update-content/{id}', [UpdateCourseContentController::class, 'content']);
            Route::match(['put','patch','post'],'/update-webcontent/{id}', [UpdateCourseContentController::class, 'webContent']);
            Route::match(['put','patch','post'],'/update-filecontent/{id}', [UpdateCourseContentController::class, 'fileContent']);
            Route::match(['put','patch','post'],'/update-videocontent/{id}', [UpdateCourseContentController::class, 'videoContent']);
            Route::match(['put','patch','post'],'/update-audiocontent/{id}', [UpdateCourseContentController::class, 'audioContent']);
        });
    });
    //Update Course Route Ends
    
    //Faculty Route Starts
    Route::prefix('faculty')->group(function(){
        Route::group(['middleware' => ['api']],function(){
            Route::match(['put','patch','post'],'/create-faculty',[FacultyController::class, 'createFaculty']);
            Route::match(['put','patch','post'],'/update-faculty/{id}',[FacultyController::class, 'updateFaculty']);
            Route::match(['get'],'/view-faculties',[FacultyController::class, 'viewFaculties']);
            Route::match(['get'],'/view-single-faculty',[FacultyController::class, 'viewSingleFaculty']);
            Route::match(['delete'],'/delete-faculty/{id}',[FacultyController::class, 'deleteFaculty']);
        });
    });
    //Faculty Route Ends

    //Department Route Starts
    Route::prefix('department')->group(function(){
        Route::group(['middleware' => ['api']],function(){
            Route::match(['put','patch','post'],'/create-department',[DepartmentController::class, 'createDepartment']);
            Route::match(['put','patch','post'],'/update-department/{id}',[DepartmentController::class, 'updateDepartment']);
            Route::match(['get'],'/view-departments',[DepartmentController::class, 'viewDepartments']);
            Route::match(['get'],'/view-single-department',[DepartmentController::class, 'viewSingleDepartment']);
            Route::match(['get'],'/view-department-by-faculty/{id}',[DepartmentController::class, 'departmentsByFaculty']);
            Route::match(['delete'],'/delete-department/{id}',[DepartmentController::class, 'deleteDepartment']);
        });
    });
    //Department Route Ends
    
    //Course Group Route Starts
    Route::prefix('course-group')->group(function(){
        Route::group(['middleware' => ['api']],function(){
            Route::match(['put','patch','post'],'/create-course-group',[CourseGroupController::class, 'createCourseGroup']);
            Route::match(['put','patch','post'],'/update-course-group/{id}',[CourseGroupController::class, 'updateCourseGroup']);
            Route::match(['get'],'/view-course-groups',[CourseGroupController::class, 'viewCourseGroups']);
            Route::match(['get'],'/view-single-course-group',[CourseGroupController::class, 'viewSingleCourseGroup']);
            Route::match(['delete'],'/delete-course-group/{id}',[CourseGroupController::class, 'deleteCourseGroup']);
        });
    });
    //Course Group Route Ends
    
    //Assign & Unassign Courses to Course Group Starts
    Route::prefix('course-group')->group(function(){
        Route::group(['middleware' => ['api']],function(){
            Route::match(['put','patch','post'],'/assign-courses-to-course-group',[CourseGroupController::class, 'assignCourseToCourseGroup']);
            Route::match(['put','patch','post'],'/unassign-courses-to-course-group',[CourseGroupController::class, 'unassignCourseToCourseGroup']);
            Route::match(['get'],'/fetch-assigned-courses-to-course-group/{group_code}',[CourseGroupController::class, 'fetchAssignedCourseToCourseGroup']);
        });
    });
    //Assign & Unassign Courses to Course Group Ends
    
    //Credit Load Starts
    Route::prefix('credit-load')->group(function(){
        Route::group(['middleware' => ['api']],function(){
            Route::match(['put','patch','post'],'/create-credit-load',[CreditLoadController::class, 'createCreditLoad']);
            Route::match(['put','patch','post'],'/update-credit-load/{id}',[CreditLoadController::class, 'updateCreditLoad']);
            Route::match(['get'],'/view-credit-load',[CreditLoadController::class, 'viewCreditLoad']);
            Route::match(['delete'],'/delete-credit-load/{id}',[CreditLoadController::class, 'deleteCreditLoad']);
        });
    });
    //Credit Load Ends
    
    //Student Enrollment Starts
    Route::prefix('student-enrollment')->group(function(){
        Route::group(['middleware' => ['api']],function(){
            Route::match(['get','put','patch','post'],'/fetch-course-to-enroll',[StudentEnrolController::class, 'fetchCoursesToEnroll']);
            Route::match(['post'],'/student-enroll',[StudentEnrolController::class, 'enrollCourse']);
        });
    });
    //Student Enrollment Ends
});
