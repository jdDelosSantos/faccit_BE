<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group([

    'middleware' => 'auth',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

    Route::get('professors', [App\Http\Controllers\UserController::class, 'index']);
    Route::get('getProfessors', [App\Http\Controllers\UserController::class, 'getProfessors']);
    Route::post('professors', [App\Http\Controllers\UserController::class, 'store']);
    Route::put('update_professors/{prof_id}', [App\Http\Controllers\UserController::class, 'update']);
    Route::put('prof_deactivate/{prof_id}', [App\Http\Controllers\UserController::class, 'deactivateUser']);
    Route::put('prof_activate/{prof_id}', [App\Http\Controllers\UserController::class, 'activateUser']);


    Route::get('students', [App\Http\Controllers\StudentController::class, 'index']);
    Route::post('students', [App\Http\Controllers\StudentController::class, 'store']);
    Route::put('update_students/{faith_id}', [App\Http\Controllers\StudentController::class, 'update']);
    Route::put('student_deactivate/{faith_id}', [App\Http\Controllers\StudentController::class, 'deactivateStudent']);
    Route::put('student_activate/{faith_id}', [App\Http\Controllers\StudentController::class, 'activateStudent']);


    Route::get('student_images', [App\Http\Controllers\StudentImageController::class, 'index']);
    Route::post('student_images', [App\Http\Controllers\StudentImageController::class, 'store']);
    Route::put('student_images/{faith_id}', [App\Http\Controllers\StudentImageController::class, 'update']);
    Route::post('student_img_url', [App\Http\Controllers\StudentImageController::class, 'getStudentImages']);

    Route::get('prof_images', [App\Http\Controllers\ProfessorImageController::class, 'index']);
    Route::post('prof_images', [App\Http\Controllers\ProfessorImageController::class, 'store']);

    Route::put('professor_images/{prof_id}', [App\Http\Controllers\ProfessorImageController::class, 'update']);
    Route::post('prof_img_url', [App\Http\Controllers\ProfessorImageController::class, 'getProfessorImages']);

    Route::get('colleges', [App\Http\Controllers\CollegeController::class, 'index']);
    Route::post('colleges', [App\Http\Controllers\CollegeController::class, 'store']);
    Route::put('update_college/{id}', [App\Http\Controllers\CollegeController::class, 'update']);
    Route::put('college_deactivate/{college_name}', [App\Http\Controllers\CollegeController::class, 'deactivateCollege']);
    Route::put('college_activate/{college_name}', [App\Http\Controllers\CollegeController::class, 'activateCollege']);

    Route::get('courses', [App\Http\Controllers\CourseController::class, 'index']);
    Route::post('courses', [App\Http\Controllers\CourseController::class, 'store']);
    Route::put('update_course/{id}', [App\Http\Controllers\CourseController::class, 'update']);
    Route::put('course_deactivate/{course_name}', [App\Http\Controllers\CourseController::class, 'deactivateCourse']);
    Route::put('course_activate/{course_name}', [App\Http\Controllers\CourseController::class, 'activateCourse']);

    Route::get('classes', [App\Http\Controllers\ClassController::class, 'index']);
    Route::post('classes', [App\Http\Controllers\ClassController::class, 'store']);
    Route::get('profClasses/{prof_id}', [App\Http\Controllers\ClassController::class, 'getClassesForProfessor']);
    Route::put('update_classes/{id}', [App\Http\Controllers\ClassController::class, 'update']);
    Route::put('class_disable/{class_code}', [App\Http\Controllers\ClassController::class, 'disableClass']);
    Route::put('class_enable/{class_code}', [App\Http\Controllers\ClassController::class, 'enableClass']);

    Route::get('class_schedule', [App\Http\Controllers\ClassScheduleController::class, 'index']);
    Route::get('class_schedule_prof', [App\Http\Controllers\ClassScheduleController::class, 'getClassSchedules']);
    Route::post('class_schedule', [App\Http\Controllers\ClassScheduleController::class, 'store']);
    Route::put('update_class_schedule/{id}', [App\Http\Controllers\ClassScheduleController::class, 'update']);
    Route::delete('delete_class_schedule/{id}', [App\Http\Controllers\ClassScheduleController::class, 'destroy']);
    Route::get('get_schedules_students', [App\Http\Controllers\ClassScheduleController::class, 'getJoinedClassSchedulesWithStudents']);

    Route::post('create_class_students/{class_code}', [App\Http\Controllers\ClassStudentController::class, 'createClassStudents']);
    Route::delete('remove_class_students/{class_code}', [App\Http\Controllers\ClassStudentController::class, 'removeClassStudents']);
    Route::get('get_class_students/{class_code}', [App\Http\Controllers\ClassStudentController::class, 'getClassStudents']);

    Route::post('create_subject_students/{subject_code}', [App\Http\Controllers\SubjectStudentController::class, 'createSubjectStudents']);
    Route::get('get_subject_students/{subject_code}', [App\Http\Controllers\SubjectStudentController::class, 'getSubjectStudents']);
    Route::delete('remove_subject_students/{subject_code}', [App\Http\Controllers\SubjectStudentController::class, 'removeSubjectStudents']);

    Route::get('laboratory_class_schedules/{laboratory}', [App\Http\Controllers\FacilityController::class, 'index']);
    Route::post('create_laboratory_classes/{laboratory}', [App\Http\Controllers\FacilityController::class, 'store']);
    Route::delete('delete_laboratory_classes/{id}', [App\Http\Controllers\FacilityController::class, 'deleteFacilitySchedule']);


});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('student_images', [App\Http\Controllers\StudentImageController::class, 'getImagesForNode']);
    Route::post('superadmin', [App\Http\Controllers\UserController::class, 'storeNewSuperAdmin']);

    Route::post('attendance', [App\Http\Controllers\AttendanceController::class, 'storeAttendance']);
});





