<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $laboratory)
{
    $schedules = Facility::where('laboratory', $laboratory)
        ->with('class:class_code,class_name,prof_id')
        ->get();

    return response()->json($schedules);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request, string $laboratory)
{
    // Validate the request data as needed
    $validatedData = $request->validate([
        '*.class_code' => 'required',
        '*.class_day' => 'required',
        '*.start_time' => 'required|date_format:H:i:s',
        '*.end_time' => 'required|date_format:H:i:s',
        // Add other validation rules as needed
    ]);

    $facilities = collect();
    $conflicts = [];

    foreach ($validatedData as $data) {
        $facility = new Facility();
        $facility->class_code = $data['class_code'];
        $facility->class_day = $data['class_day'];
        $facility->start_time = $data['start_time'];
        $facility->end_time = $data['end_time'];
        $facility->laboratory = $laboratory;

        // Check for conflicting class schedules
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);

        $conflictingClass = Facility::where('laboratory', $laboratory)
            ->where('class_day', $data['class_day'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($query) use ($startTime, $endTime) {
                    $query->whereRaw('(start_time BETWEEN ? AND ?)', [
                        $startTime->format('H:i:s'),
                        $endTime->subSecond()->format('H:i:s'),
                    ])
                    ->orWhereRaw('(end_time BETWEEN ? AND ?)', [
                        $startTime->addSecond()->format('H:i:s'),
                        $endTime->format('H:i:s'),
                    ]);
                })
                ->orWhere(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<=', $startTime->format('H:i:s'))
                        ->where('end_time', '>=', $endTime->format('H:i:s'));
                });
            })
            ->exists();

        if (!$conflictingClass) {
            $facility->save();
            $facilities->push($facility);
        } else {
            $conflicts[] = [
                'class_code' => $data['class_code'],
                'class_day' => $data['class_day'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'error' => 'The class schedule conflicts with an existing class.',
            ];
        }
    }

    if (count($conflicts) > 0) {
        return Response::json([
            'message' => 'Some classes could not be saved due to conflicts.',
            'conflicts' => $conflicts,
        ], 422);
    }

    return response()->json([
        'message' => 'Selected class schedules saved successfully',
        'facilities' => $facilities,
    ], 200);
}



    public function deleteFacilitySchedule(int $id)
    {
        $existingFacilitySchedule = Facility::findOrFail($id); // Find the record by ID
        $existingFacilitySchedule->delete(); // Delete the record

        // Optional: return a success message or redirect to another page
        return response()->json([
          "message" => "Class schedule removed from lab successfully!"
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

    public function getClassSchedForAbsent(Request $request, string $id)
{
    $laboratory = $request->input('laboratory');

    $classSchedules = DB::table('facilities')
        ->join('classes', 'facilities.class_code', '=', 'classes.class_code')
        ->where('facilities.laboratory', $laboratory)
        ->where('classes.prof_id', $id)
        ->select('facilities.id','facilities.class_day', 'facilities.start_time', 'facilities.end_time', 'classes.class_name', 'classes.class_code')
        ->get();

    return response()->json($classSchedules);
}

    public function getSuperAdminAllPLClasses()
    {

            $pl_classes = Facility::where('laboratory', "lab_programming")
            ->count();

            return response()->json($pl_classes);

    }

    public function getSuperAdminAllMLClasses()
    {
        $ml_classes = Facility::where('laboratory', "lab_multimedia")
            ->count();

            return response()->json($ml_classes);
    }
}
