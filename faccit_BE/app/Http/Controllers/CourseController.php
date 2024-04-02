<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::all();
        return response()->json($courses);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $existingCourse = Course::where('course_name', $request->course_name)->first();

        if ($existingCourse) {
            $courseName = $existingCourse->course_name;
            return response()->json([
              'message' => "Error! {$courseName} Already Exists!",
            ], 409);
          }
        else{
            $courses = new Course;
            $courses->course_name = $request->course_name;
            $courses->course_description = $request->course_description;
            $courses->college_name = $request->college_name;
            $courses->save();

            $message=(object)[
                "status"=>"1",
                "message"=> "Successfully Added ". $request->course_name
            ];
            return response()->json($message);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {

        $newCourseName = $request->course_name;
        $existingCourse = Course::where('course_name', $newCourseName)
            ->where('id', '!=', $id)
            ->first();

            if ($existingCourse) {
                // Course name already exists
                $message = (object) [
                    "status" => "0",
                    "message" => "Course Name ".$newCourseName." already exists!"
                ];
                return response()->json($message, 422); // Unprocessable Entity
            }


        $updateCourse = Course::where('id', $id)->first();
        if (!$updateCourse) {
            // Course with ID not found
            $message = (object) [
                "status" => "0",
                "message" => "Course not found!"
            ];
            return response()->json($message, 404); // Not Found
        }
        $updateCourse->course_name = $request->course_name;
        $updateCourse->course_description = $request->course_description;
        $updateCourse->college_name = $request->college_name;
        $updateCourse->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Updated ".$newCourseName
        ];
        return response()->json($message);
    }

    public function deactivateCourse(Request $request, string $course_name)
    {
        $deactivateCourse = Course::where('course_name', $course_name)->first();
        $deactivateCourse->course_status = $request->course_status;
        $deactivateCourse->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Disabled ".$course_name
        ];
        return response()->json($message);
    }

    public function activateCourse(Request $request, string $course_name)
    {
        $activateCourse = Course::where('course_name', $course_name)->first();
        $activateCourse->course_status = $request->course_status;
        $activateCourse->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Reactivated ".$course_name
        ];
        return response()->json($message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
