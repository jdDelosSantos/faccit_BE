<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassSchedule;

class ClassScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classSchedules = ClassSchedule::with(['class' => function ($query) {
            $query->select('class_name','class_code');
        }])->get();

    // Return the JSON response with only class_name
    return response()->json($classSchedules);
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
        $existingCount = ClassSchedule::where('class_code', $request->class_code)->count();

            if ($existingCount <3){
                $classSchedule = new ClassSchedule;
            $classSchedule->class_code = $request->class_code;
            $classSchedule->class_day = $request->class_day;
            $classSchedule->start_time = $request->start_time;
            $classSchedule->end_time = $request->end_time;
            $classSchedule->save();

            $message=(object)[
                "status"=>"1",
                "message"=> "Successfully Added Class Schedule."
            ];
            return response()->json($message);
            }
            else{
                return response()->json([
                    "message" => "Maximum of 3 records allowed for class code '{$request->class_code}'."
                  ], 422);
            }


        // }
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
        $updateClassSchedule = ClassSchedule::where('id', $id)->first();

        $updateClassSchedule->class_code = $request->class_code;
        $updateClassSchedule->class_day = $request->class_day;
        $updateClassSchedule->start_time = $request->start_time;
        $updateClassSchedule->end_time = $request->end_time;
        $updateClassSchedule->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Updated the Class Schedule!"
        ];
        return response()->json($message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $classSchedule = ClassSchedule::findOrFail($id); // Find the record by ID
        $classSchedule->delete(); // Delete the record

        // Optional: return a success message or redirect to another page
        return response()->json([
          "message" => "Class schedule deleted successfully!"
        ]);
    }
}
