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

//     public function getClassesForRequestMakeupClass(Request $request, string $id)
// {
//     $laboratory = $request->input('laboratory');

//     $classesWithSchedules = DB::table('classes')
//     ->join('facilities')
//         ->withCount('classStudents')
//         ->has('classStudents', '>', 0)
//         ->get();

//     return response()->json($classes);
// }

public function getClassSchedForAbsent(Request $request, string $id)
{
    $laboratory = $request->input('laboratory');

    $classes = Classes::with(['facilities' => function ($query) use ($laboratory) {
        $query->where('laboratory', $laboratory);
    }])
    ->where('prof_id', $id)
    ->has('facilities')
    ->get();


    $classesWithFacilities = $classes->map(function ($class) {
        $facilities = $class->facilities->map(function ($facility) {
            return [
                'id' => $facility->id,
                'laboratory' => $facility->laboratory,
                'class_code' => $facility->class_code,
                'class_day' => $facility->class_day,
                'start_time' => $facility->start_time,
                'end_time' => $facility->end_time,
            ];
        });

        return [
            'id' => $class->id,
            'class_code' => $class->class_code,
            'class_name' => $class->class_name,
            'class_description' => $class->class_description,
            'college_name' => $class->college_name,
            'prof_id' => $class->prof_id,
            'class_status' => $class->class_status,
            'facilities' => $facilities,
        ];
    });

    return response()->json($classesWithFacilities);
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
    public function update(Request $request, int $id)
    {
        $newClassCode = $request->class_code;

        $existingClass = Classes::where('class_code', $newClassCode)
            ->where('id', '!=', $id)
            ->first();

            if ($existingClass) {
                // Course name already exists
                $message = (object) [
                    "status" => "0",
                    "message" => "Class Code: ".$newClassCode." already exists!"
                ];
                return response()->json($message, 422); // Unprocessable Entity
            }


        $updateClass = Classes::where('id', $id)->first();

        if (!$updateClass) {
            // Course with ID not found
            $message = (object) [
                "status" => "0",
                "message" => "Class code not found!"
            ];
            return response()->json($message, 404); // Not Found
        }
        $updateClass->class_code = $request->class_code;
        $updateClass->class_name = $request->class_name;
        $updateClass->class_description = $request->class_description;
        $updateClass->college_name = $request->college_name;
        $updateClass->prof_id = $request->prof_id;
        $updateClass->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Updated Class code to ".$newClassCode
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
    $classes = Classes::where('prof_id', $profId)
        ->withCount('classStudents')
        ->get();

    return response()->json($classes);
}
}
