<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classes::all();
        return response()->json($classes);
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
        $existingClass = Classes::where('class_code', $request->class_code)->first();

        if ($existingClass) {
            $classCode = $existingClass->class_code;
            return response()->json([
              'message' => "Error! {$classCode} Already Exists!",
            ], 409);
          }
        else{
            $classes = new Classes;
            $classes->class_code = $request->class_code;
            $classes->class_name = $request->class_name;
            $classes->class_description = $request->class_description;
            $classes->college_name = $request->college_name;
            $classes->prof_id = $request->prof_id;
            $classes->save();

            $message=(object)[
                "status"=>"1",
                "message"=> "Successfully Added ". $request->class_name
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
    public function update(Request $request, string $class_code)
    {
        $updateClass = Classes::where('class_code', $class_code)->first();

        if(!$updateClass){
            return response()->json(['error' => 'Missing Subject Code!'], 404);
        }
        $updateClass->class_code = $request->class_code;
        $updateClass->class_name = $request->class_name;
        $updateClass->class_description = $request->class_description;
        $updateClass->college_name = $request->college_name;
        $updateClass->prof_id = $request->prof_id;
        $updateClass->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Updated ".$class_code
        ];
        return response()->json($message);
    }

    public function disableClass(Request $request, string $class_code)
    {
        $disableClass = Classes::where('class_code', $class_code)->first();
        $disableClass->class_status = $request->class_status;
        $disableClass->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Disabled ".$class_code
        ];
        return response()->json($message);
    }

    public function enableClass(Request $request, string $class_code)
    {
        $disableClass = Classes::where('class_code', $class_code)->first();
        $disableClass->class_status = $request->class_status;
        $disableClass->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Enabled ".$class_code
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

    public function getClassesForProfessor($profId)
    {
        $classes = Classes::where('prof_id', $profId)->get();

        return response()->json($classes);
    }
}
