<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\ClassStudents;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $attendance = $request->all();

        return response()->json($attendance);
    }

    // public function storeAttendance(Request $request)
    // {
    // // Validate the request data
    // $validatedData = $request->validate([
    //     'faith_id' => 'required|string',
    //     'day' => 'required|string',
    //     'time' => 'required|date_format:H:i:s',
    //     'laboratory' => 'required|string',
    // ]);

    // // Check if the faith_id exists in the class_students table
    // $classStudent = ClassStudents::where('faith_id', $validatedData['faith_id'])->first();
    // if (!$classStudent) {
    //     return response()->json(['message' => 'Invalid faith_id']);
    // }

    // // Check if the day and time match a facility record for the specified laboratory
    // $facility = Facility::where('laboratory', $validatedData['laboratory'])
    //     ->where('class_day', $validatedData['day'])
    //     ->whereTime('start_time', '<=', $validatedData['time'])
    //     ->whereTime('end_time', '>=', $validatedData['time'])
    //     ->first();

    // if (!$facility) {
    //     return response()->json(['message' => 'Invalid day, time, or laboratory']);
    // }

    // // Check if the faith_id is assigned to the class_code in the facility record
    // $classStudentForFacility = ClassStudents::where('class_code', $facility->class_code)
    //     ->where('faith_id', $validatedData['faith_id'])
    //     ->first();

    // if (!$classStudentForFacility) {
    //     return response()->json(['message' => 'Student not enrolled in this laboratory session']);
    // }

    // // Check if an attendance record already exists for the student, class, and date
    // $existingAttendance = Attendance::where('class_code', $facility->class_code)
    //     ->where('faith_id', $validatedData['faith_id'])
    //     ->whereDate('date', now()->toDateString())
    //     ->first();

    // if ($existingAttendance) {
    //     return response()->json(['message' => 'Attendance already recorded for this session']);
    // }

    // // Store the attendance record
    // $attendance = new Attendance();
    // $attendance->class_code = $facility->class_code;
    // $attendance->faith_id = $validatedData['faith_id'];
    // $attendance->date = now()->toDateString();
    // $attendance->time_in = $validatedData['time'];
    // $attendance->status = 'present';
    // $attendance->save();

    // return response()->json(['message' => 'Attendance recorded successfully']);
    // }



//     public function storeAttendance(Request $request)
// {
//     // Validate the request data
//     $validatedData = $request->validate([
//         'faith_id' => 'required|string',
//         'day' => 'required|string',
//         'time' => 'required|date_format:H:i:s',
//         'laboratory' => 'required|string',
//     ]);

//     // Check if the faith_id exists in the class_students table
//     $classStudent = ClassStudents::where('faith_id', $validatedData['faith_id'])->first();
//     if (!$classStudent) {
//         return response()->json(['error' => 'Invalid faith_id'], 400);
//     }

//     // Check if the day and time match a facility record for the specified laboratory
//     $facility = Facility::where('laboratory', $validatedData['laboratory'])
//         ->where('class_day', $validatedData['day'])
//         ->whereTime('start_time', '<=', $validatedData['time'])
//         ->whereTime('end_time', '>=', $validatedData['time'])
//         ->first();

//     if (!$facility) {
//         return response()->json(['error' => 'Invalid day, time, or laboratory'], 400);
//     }

//     // Check if the faith_id is assigned to the class_code in the facility record
//     $classStudentForFacility = ClassStudents::where('class_code', $facility->class_code)
//         ->where('faith_id', $validatedData['faith_id'])
//         ->first();

//     if (!$classStudentForFacility) {
//         return response()->json(['error' => 'Student not enrolled in this laboratory session'], 400);
//     }

//     // Get the professor's faith_id from the classes table
//     $professorFaithId = DB::table('classes')
//         ->where('class_code', $facility->class_code)
//         ->value('prof_id');

//     // Check if the professor has already recorded attendance for this session
//     $professorAttendance = Attendance::where('class_code', $facility->class_code)
//         ->where('faith_id', $professorFaithId)
//         ->whereDate('date', now()->toDateString())
//         ->first();

//     if (!$professorAttendance && $validatedData['faith_id'] !== $professorFaithId) {
//         return response()->json(['error' => 'Professor attendance not recorded yet'], 400);
//     }

//     // Check if an attendance record already exists for the student, class, and date
//     $existingAttendance = Attendance::where('class_code', $facility->class_code)
//         ->where('faith_id', $validatedData['faith_id'])
//         ->whereDate('date', now()->toDateString())
//         ->first();

//     if ($existingAttendance) {
//         return response()->json(['error' => 'Attendance already recorded for this session'], 400);
//     }

//     // Store the attendance record
//     $attendance = new Attendance();
//     $attendance->class_code = $facility->class_code;
//     $attendance->faith_id = $validatedData['faith_id'];
//     $attendance->date = now()->toDateString();
//     $attendance->time_in = $validatedData['time'];
//     $attendance->status = 'present';
//     $attendance->save();

//     return response()->json(['message' => 'Attendance recorded successfully']);
// }



    private function isStudent($id)
    {
        return DB::table('class_students')->where('faith_id', $id)->exists();
    }

    private function isProfessor($id)
    {
        return DB::table('classes')->where('prof_id', $id)->exists();
    }

    public function storeAttendance(Request $request)
    {
        $id = $request->input('id');
        $day = $request->input('day');
        $time = $request->input('time');
        $laboratory = $request->input('laboratory');

        if ($this->isProfessor($id)) {
            $response = $this->storeProfessorAttendance($request, $day, $time, $laboratory);
            if ($response instanceof JsonResponse) {
                return $response;
            }
        } elseif ($this->isStudent($id)) {
            $response = $this->storeStudentAttendance($request, $day, $time, $laboratory);
            if ($response instanceof JsonResponse) {
                return $response;
            }
        }
        else{
            // Return an error response if the ID is neither a professor nor a student
        return response()->json(['error' => 'Invalid ID provided.'], 422);
        }

    }


    public function storeProfessorAttendance(Request $request, $day, $time, $laboratory)
{
    $professorId = $request->input('id');

    // Check if the professor has a class scheduled for the given day, time, and laboratory
    $classSchedule = DB::table('facilities')
        ->join('classes', 'facilities.class_code', '=', 'classes.class_code')
        ->where('classes.prof_id', $professorId)
        ->where('facilities.class_day', $day)
        ->where('facilities.start_time', '<=', $time)
        ->where('facilities.end_time', '>=', $time)
        ->where('facilities.laboratory', $laboratory)
        ->first();

    if (!$classSchedule) {
        return response()->json(['message' => 'No class schedule found for the provided details.']);
    }

    // Check if the professor has already taken attendance for this class schedule
    $existingAttendance = DB::table('professor_attendances')
        ->where('prof_id', $professorId)
        ->where('class_code', $classSchedule->class_code)
        ->where('date', date('Y-m-d'))
        ->exists();

    if ($existingAttendance) {
        return response()->json(['message' => 'Professor attendance already recorded for this class schedule.']);
    }

    // Store the professor attendance record
    DB::table('professor_attendances')->insert([
        'class_code' => $classSchedule->class_code,
        'prof_id' => $professorId,
        'date' => date('Y-m-d'),
        'time_in' => $time,
        'status' => 'Present',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Return an appropriate response
    return response()->json(['message' => 'Attendance recorded successfully']);
}


public function storeStudentAttendance(Request $request, $day, $time, $laboratory)
{
    $studentId = $request->input('id');

    // Check if there is a class schedule for the given day, time, and laboratory
    $classSchedule = DB::table('facilities')
        ->where('class_day', $day)
        ->where('start_time', '<=', $time)
        ->where('end_time', '>=', $time)
        ->where('laboratory', $laboratory)
        ->first();

    if (!$classSchedule) {
        return response()->json(['message' => 'No class schedule found for the provided details.']);
    }

    // Check if the student is enrolled in the class
    $isStudentEnrolled = DB::table('class_students')
        ->where('faith_id', $studentId)
        ->where('class_code', $classSchedule->class_code)
        ->exists();

    if (!$isStudentEnrolled) {
        return response()->json(['message' => 'Student is not enrolled in the class.']);
    }

    // Check if the professor has already taken attendance for this class schedule
    $professorAttendance = DB::table('professor_attendances')
        ->where('class_code', $classSchedule->class_code)
        ->where('date', date('Y-m-d'))
        ->exists();

    if (!$professorAttendance) {
        return response()->json(['message' => 'Professor attendance not recorded for this class schedule.']);
    }

    // Check if the student has already taken attendance
    $existingAttendance = DB::table('attendances')
        ->where('faith_id', $studentId)
        ->where('class_code', $classSchedule->class_code)
        ->where('date', date('Y-m-d'))
        ->exists();

    if ($existingAttendance) {
        return response()->json(['message' => 'Student attendance already recorded for this class schedule.']);
    }

    // Store the student attendance record
    DB::table('attendances')->insert([
        'class_code' => $classSchedule->class_code,
        'faith_id' => $studentId,
        'date' => date('Y-m-d'),
        'time_in' => $time,
        'status' => 'Present',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Return an appropriate response
    return response()->json(['message' => 'Attendance recorded successfully']);
}

}
