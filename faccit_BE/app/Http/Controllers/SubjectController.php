<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::all();
        return response()->json($subjects);
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
        $existingSubject = Subject::where('subject_code', $request->subject_code)->first();

        if ($existingSubject) {
            $subjectCode = $existingSubject->subject_code;
            return response()->json([
              'message' => "Error! {$subjectCode} Already Exists!",
            ], 409);
          }
        else{
            $subjects = new Subject;
            $subjects->subject_code = $request->subject_code;
            $subjects->subject_name = $request->subject_name;
            $subjects->subject_description = $request->subject_description;
             // Check if prof_id is not null before assigning
        if ($request->prof_id !== null) {
            $subjects->prof_id = $request->prof_id;
        }

        // Check if subject_day is not null before assigning
        if ($request->subject_day !== null) {
            $subjects->subject_day = $request->subject_day;
        }

        // Check if start_time is not null before assigning
        if ($request->start_time !== null) {
            $subjects->start_time = $request->start_time;
        }

        // Check if end_time is not null before assigning
        if ($request->end_time !== null) {
            $subjects->end_time = $request->end_time;
        }
            $subjects->save();

            $message=(object)[
                "status"=>"1",
                "message"=> "Successfully Added ". $request->subject_name
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
    public function update(Request $request, string $subject_code)
    {
        $updateSubject = Subject::where('subject_code', $subject_code)->first();

        if(!$updateSubject){
            return response()->json(['error' => 'Missing Subject Code!'], 404);
        }
        $updateSubject->subject_code = $request->subject_code;
        $updateSubject->subject_name = $request->subject_name;
        $updateSubject->subject_description = $request->subject_description;
        if ($request->prof_id !== null) {
            $updateSubject->prof_id = $request->prof_id;
        }

        // Check if subject_day is not null before assigning
        if ($request->subject_day !== null) {
            $updateSubject->subject_day = $request->subject_day;
        }

        // Check if start_time is not null before assigning
        if ($request->start_time !== null) {
            $updateSubject->start_time = $request->start_time;
        }

        // Check if end_time is not null before assigning
        if ($request->end_time !== null) {
            $updateSubject->end_time = $request->end_time;
        }
        $updateSubject->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Updated ".$subject_code
        ];
        return response()->json($message);
    }

    public function deactivateSubject(Request $request, string $subject_code)
    {
        $deactivateSubject = Subject::where('subject_code', $subject_code)->first();
        $deactivateSubject->subject_status = $request->subject_status;
        $deactivateSubject->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Disabled ".$subject_code
        ];
        return response()->json($message);
    }

    public function activateSubject(Request $request, string $subject_code)
    {
        $activateSubject = Subject::where('subject_code', $subject_code)->first();
        $activateSubject->subject_status = $request->subject_status;
        $activateSubject->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Reactivated ".$subject_code
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

    public function getSubjectsForProfessor($profId)
    {
        $subjects = Subject::where('prof_id', $profId)->get();

        return response()->json($subjects);
    }
}
