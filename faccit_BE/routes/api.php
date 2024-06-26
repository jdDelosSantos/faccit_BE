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

    Route::get('admins', [App\Http\Controllers\UserController::class, 'getAdmins']);
    Route::post('admins', [App\Http\Controllers\UserController::class, 'storeAdmin']);
    Route::put('update_admin/{id}', [App\Http\Controllers\UserController::class, 'updateAdminInfo']);
    Route::put('admin_deactivate/{id}', [App\Http\Controllers\UserController::class, 'deactivateAdmin']);
    Route::put('admin_activate/{id}', [App\Http\Controllers\UserController::class, 'activateAdmin']);
    Route::put('reset_admin_pass/{id}', [App\Http\Controllers\UserController::class, 'resetAdminPass']);


    Route::get('professors', [App\Http\Controllers\UserController::class, 'index']);
    Route::get('getProfessors', [App\Http\Controllers\UserController::class, 'getProfessors']);
    Route::get('prof_info/{prof_id}', [App\Http\Controllers\UserController::class, 'getProfessorInfo']);
    Route::put('prof_info_update/{prof_id}', [App\Http\Controllers\UserController::class, 'update']);
    Route::post('professors', [App\Http\Controllers\UserController::class, 'store']);
    Route::post('update_pass_prof/{prof_id}', [App\Http\Controllers\UserController::class, 'updateProfPass']);
    Route::put('update_professors/{prof_id}', [App\Http\Controllers\UserController::class, 'updateProfessorInfo']);
    Route::put('prof_deactivate/{prof_id}', [App\Http\Controllers\UserController::class, 'deactivateUser']);
    Route::put('prof_activate/{prof_id}', [App\Http\Controllers\UserController::class, 'activateUser']);


    Route::get('all_professors', [App\Http\Controllers\UserController::class, 'getAllProfessors']);
    Route::get('super_admin_info/{email}', [App\Http\Controllers\UserController::class, 'getUserInfo']);
    Route::put('super_admin_info_update/{email}', [App\Http\Controllers\UserController::class, 'updateUserInfo']);
    Route::post('update_pass_super_admin/{email}', [App\Http\Controllers\UserController::class, 'updateUserPass']);
    Route::put('reset_prof_pass/{prof_id}', [App\Http\Controllers\UserController::class, 'resetProfPass']);


    Route::get('students', [App\Http\Controllers\StudentController::class, 'index']);
    Route::post('students', [App\Http\Controllers\StudentController::class, 'store']);
    Route::put('update_students/{faith_id}', [App\Http\Controllers\StudentController::class, 'update']);
    Route::put('student_deactivate/{faith_id}', [App\Http\Controllers\StudentController::class, 'deactivateStudent']);
    Route::put('student_activate/{faith_id}', [App\Http\Controllers\StudentController::class, 'activateStudent']);
    Route::post('bulk_insert', [App\Http\Controllers\StudentController::class, 'bulkInsertFromCSV']);
    Route::post('bulk_insert_prof', [App\Http\Controllers\UserController::class, 'bulkInsertFromCSVProf']);
    Route::post('bulk_insert_admin', [App\Http\Controllers\UserController::class, 'bulkInsertFromCSVAdmin']);


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
    Route::get('super_admin_all_classes', [App\Http\Controllers\ClassController::class, 'getSuperAdminAllClasses']);
    Route::get('super_admin_all_students', [App\Http\Controllers\StudentController::class, 'getSuperAdminAllStudents']);
    Route::get('super_admin_all_pending_makeup', [App\Http\Controllers\MakeupClassController::class, 'getSuperAdminAllPendingMakeup']);
    Route::get('super_admin_all_pending_cancel', [App\Http\Controllers\CancelClassController::class, 'getSuperAdminAllPendingCancel']);
    Route::get('super_admin_all_classes_pl', [App\Http\Controllers\FacilityController::class, 'getSuperAdminAllPLClasses']);
    Route::get('super_admin_all_classes_ml', [App\Http\Controllers\FacilityController::class, 'getSuperAdminAllMLClasses']);

    Route::post('classes', [App\Http\Controllers\ClassController::class, 'store']);
    Route::get('profClasses/{prof_id}', [App\Http\Controllers\ClassController::class, 'getClassesForProfessor']);
    Route::get('prof_classes/{prof_id}', [App\Http\Controllers\ClassController::class, 'getClassesForProf']);
    Route::get('prof_all_classes/{prof_id}', [App\Http\Controllers\ClassController::class, 'getCountClassesForProfessor']);
    Route::get('prof_all_classes_pl/{prof_id}', [App\Http\Controllers\ClassController::class, 'getCountClassesForProfessorInPL']);
    Route::get('prof_all_classes_ml/{prof_id}', [App\Http\Controllers\ClassController::class, 'getCountClassesForProfessorInML']);
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

    Route::post('get_laboratory_scheds/{id}', [App\Http\Controllers\ClassController::class, 'getClassSchedForAbsent']);

    Route::get('makeup_classes', [App\Http\Controllers\MakeupClassController::class, 'index']);
    Route::get('makeup_classes_prof/{prof_id}', [App\Http\Controllers\MakeupClassController::class, 'getMakeupClassRequestsforProfessor']);
    Route::post('request_makeup_class/{id}', [App\Http\Controllers\MakeupClassController::class, 'store']);
    Route::post('approve_makeup_class/{id}', [App\Http\Controllers\MakeupClassController::class, 'approveMakeupClass']);
    Route::post('reject_makeup_class' , [App\Http\Controllers\MakeupClassController::class, 'rejectMakeupClass']);

    Route::get('cancel_classes', [App\Http\Controllers\CancelClassController::class, 'index']);
    Route::post('request_cancel_class/{id}' , [App\Http\Controllers\CancelClassController::class, 'store']);
    Route::get('cancel_classes_prof/{prof_id}', [App\Http\Controllers\CancelClassController::class, 'getCancelClassRequestsforProfessor']);
    Route::post('approve_cancel_class/{id}', [App\Http\Controllers\CancelClassController::class, 'approveCancelClass']);
    Route::post('reject_cancel_class' , [App\Http\Controllers\CancelClassController::class, 'rejectCancelClass']);

    Route::post('student_attendances/{classCode}' , [App\Http\Controllers\AttendanceController::class, 'getStudentAttendances']);
    Route::post('month_student_attendances/{classCode}' , [App\Http\Controllers\AttendanceController::class, 'getMonthStudentAttendances']);
    Route::post('add_manual_attendance' , [App\Http\Controllers\AttendanceController::class, 'addManualAttendance']);
    Route::post('open_attendance', [App\Http\Controllers\AttendanceController::class, 'storeManualProfessorAttendance']);
    Route::get('get_open_class/{prof_id}', [App\Http\Controllers\AttendanceController::class, 'getOpenAttendances']);

    Route::get('all_prof_classes', [App\Http\Controllers\ClassController::class, 'getAllClassesWithProf']);
    Route::post('student_attendances/{classCode}' , [App\Http\Controllers\AttendanceController::class, 'getStudentAttendances']);

});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('student_images', [App\Http\Controllers\StudentImageController::class, 'getImagesForNode']);
    Route::post('superadmin', [App\Http\Controllers\UserController::class, 'storeNewSuperAdmin']);

    Route::get('all_images', [App\Http\Controllers\ImageController::class, 'getAllImages']);

    Route::post('attendance', [App\Http\Controllers\AttendanceController::class, 'storeAttendance']);
});





