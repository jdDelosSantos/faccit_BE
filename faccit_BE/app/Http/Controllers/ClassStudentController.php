<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassStudents;

class ClassStudentController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request){

    }

    public function createClassStudents(Request $request, string $class_code)
    {
        $selectedStudents = $request->all();
        $errors = [];
        foreach ($selectedStudents as $studentData) {
            $existingStudent = ClassStudents::where('faith_id', $studentData['faith_id'])
                                     ->where('class_code', $class_code)
                                     ->first();
            if (!$existingStudent){
                $student = new ClassStudents();
            $student->class_code = $class_code; // Use $subject_code directly
            $student->faith_id = $studentData['faith_id']; // Use array access []

            $student->save();
        } else if ($existingStudent){
            $errorMessage = "Error! Student with ID {$studentData['faith_id']} already enrolled in subject {$class_code}";
            $errors[] = $errorMessage;
        }
    }
    if (count($errors) > 0) {
        return response()->json([
            'message' => 'Some students were not added due to Existing Records.',
            'errors' => $errors
        ], 209);
    }
    return response()->json(['message' => 'Selected students saved successfully'], 200);
    }



    public function getClassStudents(string $class_code)
{
    $classStudents = ClassStudents::where('class_code', $class_code)
        ->with('student:faith_id,std_lname,std_fname,std_course,std_level,std_section')
        ->get();

    return response()->json($classStudents);
}


    public function removeClassStudents(Request $request, $class_code)
{
    // Assuming the request body contains an array of objects with IDs of students to be removed
    $studentsToRemove = $request->all();

    // Extract student IDs from the array
    $studentIds = array_column($studentsToRemove, 'id');

    // Delete the students with the given IDs
    $deletedCount = ClassStudents::whereIn('id', $studentIds)->delete();

    return response()->json([
        'message' => 'Student records deleted successfully',
        'deleted_count' => $deletedCount,
    ]);
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
