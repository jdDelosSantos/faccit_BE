<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::withCount('studentImages')
                      ->get();

    return response()->json($students);
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
        $existingStudent = Student::where('faith_id', $request->faith_id)->first();

        if ($existingStudent) {
            $studentID = $existingStudent->faith_id;
            return response()->json([
              'message' => "Error! {$studentID} Already Exists!",
            ], 409);
          }
        else{
            $students = new Student;
            $students->faith_id = $request->faith_id;
            $students->std_lname = $request->std_lname;
            $students->std_fname = $request->std_fname;
            $students->std_course = $request->std_course;
            $students->std_level = $request->std_level;
            $students->std_section = $request->std_section;
            $students->save();

            $message=(object)[
                "status"=>"1",
                "message"=> "Successfully Added ". $request->faith_id
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
    public function update(Request $request, string $faith_id)
    {
        $updateStudent = Student::where('faith_id', $faith_id)->first();
        $updateStudent->std_fname = $request->std_fname;
        $updateStudent->std_lname = $request->std_lname;
        $updateStudent->std_course = $request->std_course;
        $updateStudent->std_level = $request->std_level;
        $updateStudent->std_section = $request->std_section;
        $updateStudent->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Updated ".$faith_id
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
