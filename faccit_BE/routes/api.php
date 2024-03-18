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

    Route::get('students', [App\Http\Controllers\StudentController::class, 'index']);
    Route::post('students', [App\Http\Controllers\StudentController::class, 'store']);
    Route::put('update_students/{faith_id}', [App\Http\Controllers\StudentController::class, 'update']);

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

    Route::get('courses', [App\Http\Controllers\CourseController::class, 'index']);
    Route::post('courses', [App\Http\Controllers\CourseController::class, 'store']);

    Route::get('subjects', [App\Http\Controllers\SubjectController::class, 'index']);
    Route::post('subjects', [App\Http\Controllers\SubjectController::class, 'store']);
    Route::put('update_subjects/{subject_code}', [App\Http\Controllers\SubjectController::class, 'update']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('student_images', [App\Http\Controllers\StudentImageController::class, 'getImagesForNode']);
    Route::post('superadmin', [App\Http\Controllers\UserController::class, 'storeNewSuperAdmin']);
});





