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
            $subjects->subject_day = $request->subject_day;
            $subjects->start_time = $request->start_time;
            $subjects->end_time = $request->end_time;
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
