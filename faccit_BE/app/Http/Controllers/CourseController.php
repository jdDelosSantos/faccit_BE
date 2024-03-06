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
        $existingCourse = Course::where('course_code', $request->course_code)->first();

        if ($existingCourse){
            return response()->json(['message' => 'Course Code Already Exists!'], 409);
        }
        else{
            $courses = new Course;
            $courses->course_code = $request->course_code;
            $courses->course_name = $request->course_name;
            $courses->course_description = $request->course_description;
            $courses->course_college = $request->course_college;
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
