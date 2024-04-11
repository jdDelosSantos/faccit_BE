<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestMakeupClass;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MakeupClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $makeupClasses = RequestMakeupClass::with(['class' => function ($query) {
        $query->select('class_name', 'class_code');
    }])
    ->with(['professor' => function ($query) {
        $query->select('prof_id', 'user_firstname', 'user_lastname');
    }])
    ->select('*', DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as created_date'))
    ->get();


        return response()->json($makeupClasses);
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
    public function store(Request $request, string $id)
    {
        $makeupClass = new RequestMakeupClass;
        $makeupClass->prof_id = $id;
        $makeupClass->laboratory = $request->laboratory;
        $makeupClass->class_code = $request->class_code;
        $makeupClass->class_day = $request->class_day;
        $makeupClass->start_time = $request->start_time;
        $makeupClass->end_time = $request->end_time;

        $makeupClass->absent_laboratory = $request->absent_laboratory;
        $makeupClass->absent_class_code = $request->absent_class_code;
        $makeupClass->absent_class_day = $request->absent_class_day;
        $makeupClass->absent_start_time = $request->absent_start_time;
        $makeupClass->absent_end_time = $request->absent_end_time;
        $makeupClass->save();

        return response()->json(['message' => 'Makeup Class requested successfully']);
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

    public function approveMakeupClass(Request $request, int $id)
    {
    // Validate the request data
    $validatedData = $request->validate([
        'class_code' => 'required',
        'class_day' => 'required',
        'start_time' => 'required|date_format:H:i:s',
        'end_time' => 'required|date_format:H:i:s',
        'laboratory' => 'required',
        'absent_class_code' => 'required',
        'absent_class_day' => 'required',
        'absent_start_time' => 'required|date_format:H:i:s',
        'absent_end_time' => 'required|date_format:H:i:s',
        'absent_laboratory' => 'required',
    ]);

    // Check if the new class schedule conflicts with an existing one
    $startTime = Carbon::parse($validatedData['start_time']);
    $endTime = Carbon::parse($validatedData['end_time']);

    $conflictingClass = Facility::where('laboratory', $validatedData['laboratory'])
        ->where('class_day', $validatedData['class_day'])
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

    if ($conflictingClass){
        return response()->json([
            'message' => "Error! There is a Conflicting Class Schedule!",
          ], 409);
    }

    else if (!$conflictingClass) {
        // Create a new record in the facilities table
        $facility = new Facility();
        $facility->class_code = $validatedData['class_code'];
        $facility->class_day = $validatedData['class_day'];
        $facility->start_time = $validatedData['start_time'];
        $facility->end_time = $validatedData['end_time'];
        $facility->laboratory = $validatedData['laboratory'];
        $facility->save();

        // Delete the record with the absent_* values
        Facility::where('class_code', $validatedData['absent_class_code'])
            ->where('class_day', $validatedData['absent_class_day'])
            ->where('start_time', $validatedData['absent_start_time'])
            ->where('end_time', $validatedData['absent_end_time'])
            ->where('laboratory', $validatedData['absent_laboratory'])
            ->delete();

        // Update the makeup_class_status in the request_makeup_classes table
        $requestMakeupClass = RequestMakeupClass::find($id);
        $requestMakeupClass->makeup_class_status = 'Approved';
        $requestMakeupClass->save();


        return response()->json([
            'message' => 'Makeup class created and absent class deleted successfully.',
        ], 200);
    } else {
        return response()->json([
            'message' => 'The new class schedule conflicts with an existing class.',
        ], 422);
    }
}

    public function rejectMakeupClass(Request $request)
    {
        $reject = RequestMakeupClass::find($request->id);
        $reject->makeup_class_status = 'Rejected';
        $reject->save();

        return response()->json([
            'message' => 'Makeup class rejected successfully.',
        ]);
    }

}
