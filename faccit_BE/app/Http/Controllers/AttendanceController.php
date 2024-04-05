<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\ClassStudents;
use App\Models\Facility;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $attendance = $request->all();

        return response()->json($attendance);
    }

    public function storeAttendance(Request $request)
    {
    // Validate the request data
    $validatedData = $request->validate([
        'faith_id' => 'required|string',
        'day' => 'required|string',
        'time' => 'required|date_format:H:i:s',
        'laboratory' => 'required|string',
    ]);

    // Check if the faith_id exists in the class_students table
    $classStudent = ClassStudents::where('faith_id', $validatedData['faith_id'])->first();
    if (!$classStudent) {
        return response()->json(['message' => 'Invalid faith_id']);
    }

    // Check if the day and time match a facility record for the specified laboratory
    $facility = Facility::where('laboratory', $validatedData['laboratory'])
        ->where('class_day', $validatedData['day'])
        ->whereTime('start_time', '<=', $validatedData['time'])
        ->whereTime('end_time', '>=', $validatedData['time'])
        ->first();

    if (!$facility) {
        return response()->json(['message' => 'Invalid day, time, or laboratory']);
    }

    // Check if the faith_id is assigned to the class_code in the facility record
    $classStudentForFacility = ClassStudents::where('class_code', $facility->class_code)
        ->where('faith_id', $validatedData['faith_id'])
        ->first();

    if (!$classStudentForFacility) {
        return response()->json(['message' => 'Student not enrolled in this laboratory session']);
    }

    // Check if an attendance record already exists for the student, class, and date
    $existingAttendance = Attendance::where('class_code', $facility->class_code)
        ->where('faith_id', $validatedData['faith_id'])
        ->whereDate('date', now()->toDateString())
        ->first();

    if ($existingAttendance) {
        return response()->json(['message' => 'Attendance already recorded for this session']);
    }

    // Store the attendance record
    $attendance = new Attendance();
    $attendance->class_code = $facility->class_code;
    $attendance->faith_id = $validatedData['faith_id'];
    $attendance->date = now()->toDateString();
    $attendance->time_in = $validatedData['time'];
    $attendance->status = 'present';
    $attendance->save();

    return response()->json(['message' => 'Attendance recorded successfully']);
    }


}
