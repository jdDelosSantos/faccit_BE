<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\ClassStudents;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function addManualAttendance(Request $request)
    {
        $classCode = $request->class_code;
        $faithId = $request->faith_id;
        $date = Carbon::parse($request->date);
        $timeIn = $request->time_in;
        $status = $request->status;

        // Get the day of the week from the date
        $day = $date->format('l');

        // Get all the facilities for the given class code and day
        $facilities = Facility::where('class_code', $classCode)
            ->where('class_day', $day)
            ->get();

        if ($facilities->isNotEmpty()) {
            $validSchedule = false;

            foreach ($facilities as $facility) {
                $startTime = Carbon::parse($facility->start_time, config('app.timezone'));
                $endTime = Carbon::parse($facility->end_time, config('app.timezone'));

                $parsedTimeIn = Carbon::parse($timeIn, config('app.timezone'));

                if ($parsedTimeIn->between($startTime, $endTime)) {
                    $existingRecord = Attendance::where('class_code', $classCode)
                        ->where('faith_id', $faithId)
                        ->where('date', $date->format('Y-m-d'))
                        ->where('start_time', $facility->start_time)
                        ->where('end_time', $facility->end_time)
                        ->first();

                    if ($existingRecord) {
                        return response()->json(['message' => $faithId . ' attendance for the class schedule already exists.'], 422);
                    }

                    $validSchedule = true;

                    // Create a new attendance record with the facility's start_time and end_time
                    $manualAttendance = new Attendance;
                    $manualAttendance->class_code = $classCode;
                    $manualAttendance->faith_id = $faithId;
                    $manualAttendance->date = $date->format('Y-m-d');
                    $manualAttendance->start_time = $facility->start_time;
                    $manualAttendance->end_time = $facility->end_time;
                    $manualAttendance->time_in = $timeIn;
                    $manualAttendance->status = $status;
                    $manualAttendance->save();

                    return response()->json(['message' => $faithId . ' set to Present successfully']);
                }
            }

            if (!$validSchedule) {
                return response()->json(['message' => 'Time In: ' . $timeIn . ' has no corresponding class schedule'], 422);
            }
        } else {
            return response()->json(['message' => 'The day has no corresponding class schedule!'], 422);
        }
    }

    //FUNCTION FOR GETTING STUDENTS BASED ON THE CLASS CODE
    public function getStudentAttendances(string $classCode, Request $request)
{
    $date = $request->date;
    $startTime = $request->start_time;
    $endTime = $request->end_time;

    // Retrieve the list of students enrolled in the class
    $students = DB::table('class_students')
        ->where('class_code', $classCode)
        ->pluck('faith_id');

    // Join the class_students table with attendances, classes, and students tables
    $studentAttendances = DB::table('class_students')
        ->join('students', 'class_students.faith_id', '=', 'students.faith_id')
        ->leftJoin('attendances', function ($join) use ($classCode, $date, $startTime, $endTime) {
            $join->on('class_students.faith_id', '=', 'attendances.faith_id')
                ->where('attendances.class_code', $classCode)
                ->whereDate('attendances.date', $date)
                ->whereTime('attendances.time_in', '>=', $startTime)
                ->whereTime('attendances.time_in', '<=', $endTime);
        })
        ->leftJoin('classes', 'class_students.class_code', '=', 'classes.class_code')
        ->where('class_students.class_code', $classCode)
        ->whereIn('class_students.faith_id', $students)
        ->select(
            'students.faith_id',
            'students.std_fname',
            'students.std_lname',
            'students.std_course',
            'students.std_level',
            'students.std_section',
            DB::raw("COALESCE(attendances.date, '{$date}') as date"),
            'attendances.time_in',
            DB::raw('COALESCE(attendances.status, "Absent") as status'),
            'classes.class_name',
            'classes.class_code'
        )
        ->get();

    // Return the retrieved attendance records, class names, and student information as a JSON response
    return response()->json($studentAttendances);
}


public function getMonthStudentAttendances(string $class_code, Request $request)
{
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    $studentAttendances = DB::table('class_students')
        ->join('students', 'class_students.faith_id', '=', 'students.faith_id')
        ->leftJoin('attendances', function ($join) use ($class_code, $startDate, $endDate) {
            $join->on('class_students.faith_id', '=', 'attendances.faith_id')
                ->where('attendances.class_code', $class_code)
                ->whereBetween('attendances.date', [$startDate, $endDate]);
        })
        ->leftJoin('classes', 'class_students.class_code', '=', 'classes.class_code')
        ->leftJoin('professor_attendances', function ($join) use ($class_code, $startDate, $endDate) {
            $join->on('classes.class_code', '=', 'professor_attendances.class_code')
                ->whereBetween('professor_attendances.date', [$startDate, $endDate]);
        })
        ->where('class_students.class_code', $class_code)
        ->select(
            'classes.class_name',
            'classes.class_code',
            'students.faith_id',
            'students.std_fname',
            'students.std_lname',
            'students.std_course',
            'students.std_level',
            'students.std_section',
            DB::raw("COALESCE(attendances.date, '{$startDate}') as date"),
            'attendances.time_in',
            DB::raw('COALESCE(attendances.status, "Absent") as status'),
            DB::raw('COUNT(CASE WHEN attendances.status = "Present" THEN 1 END) AS present_count'),
            DB::raw('COUNT(CASE WHEN attendances.status = "Late" THEN 1 END) AS late_count'),
            DB::raw('COUNT(CASE WHEN professor_attendances.status = "Open" THEN 1 END) AS open_count')
        )
        ->groupBy(
            'classes.class_name',
            'classes.class_code',
            'students.faith_id',
            'students.std_fname',
            'students.std_lname',
            'students.std_course',
            'students.std_level',
            'students.std_section',
            'attendances.date',
            'attendances.time_in',
            'attendances.status'
        )
        ->get();

    return response()->json($studentAttendances);
}



    public function getAllStudentAttendances()
    {
        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        // Retrieve the list of students enrolled in the class
        $students = DB::table('class_students')
            ->where('class_code', $classCode)
            ->pluck('faith_id');

        // Join the class_students table with attendances, classes, and students tables
        $studentAttendances = DB::table('class_students')
            ->join('students', 'class_students.faith_id', '=', 'students.faith_id')
            ->leftJoin('attendances', function ($join) use ($classCode, $date, $startTime, $endTime) {
                $join->on('class_students.faith_id', '=', 'attendances.faith_id')
                    ->where('attendances.class_code', $classCode)
                    ->whereDate('attendances.date', $date)
                    ->whereTime('attendances.time_in', '>=', $startTime)
                    ->whereTime('attendances.time_in', '<=', $endTime);
            })
            ->leftJoin('classes', 'class_students.class_code', '=', 'classes.class_code')
            ->where('class_students.class_code', $classCode)
            ->whereIn('class_students.faith_id', $students)
            ->select(
                'students.faith_id',
                'students.std_fname',
                'students.std_lname',
                'students.std_course',
                'students.std_level',
                'students.std_section',
                DB::raw("COALESCE(attendances.date, '{$date}') as date"),
                'attendances.time_in',
                DB::raw('COALESCE(attendances.status, "Absent") as status'),
                'classes.class_name',
                'classes.class_code'
            )
            ->get();

        // Return the retrieved attendance records, class names, and student information as a JSON response
        return response()->json($studentAttendances);
    }

    //PRIVATE FUNCTION FOR DETERMINING THE ID
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
            return $response;
        } elseif ($this->isStudent($id)) {
            $response = $this->storeStudentAttendance($request, $day, $time, $laboratory);
            return $response;
        }
        else{
            // Return an error response if the ID is neither a professor nor a student
        return response()->json(['error' => 'Invalid ID provided.'], 422);
        }

    }

    //PUBLIC FUNCTION FOR PROFESSOR ATTENDANCE
    public function storeProfessorAttendance(Request $request, $day, $time, $laboratory) {
        $professorId = $request->input('id');

        // Check if the professor has any class scheduled for the given day, time, and laboratory
        $classSchedules = DB::table('facilities')
            ->join('classes', 'facilities.class_code', '=', 'classes.class_code')
            ->where('classes.prof_id', $professorId)
            ->where('facilities.class_day', $day)
            ->where('facilities.laboratory', $laboratory)
            ->get();

        if (!$classSchedules || $classSchedules->count() === 0) {
            return response()->json(['message' => 'No class schedule found for the given day and/or laboratory!']);
        }

        foreach ($classSchedules as $classSchedule) {

            $professorData = DB::table('users')
            ->where('prof_id', $professorId)
            ->first();

            $classCode = DB::table('classes')
            ->where('class_code', $classSchedule->class_code)
            ->first();

            if ($time >= $classSchedule->start_time && $time <= $classSchedule->end_time) {
                $existingAttendance = DB::table('professor_attendances')
                    ->where('prof_id', $professorId)
                    ->where('class_code', $classSchedule->class_code)
                    ->where('date', date('Y-m-d'))
                    ->whereBetween('time_in', [$classSchedule->start_time, $classSchedule->end_time])
                    ->exists();

            if ($existingAttendance)
            {
                    $data = [
                    "message" => [
                      "id" => $professorId,
                      "name" => $professorData->user_lastname.', '.$professorData->user_firstname,
                      "class_name" => $classCode->class_name,
                      "time_in" => $time,
                      "status" => "Class already opened!"
                    ]
                  ];

                return response()->json($data);
            }

                if (!$existingAttendance) {
                    $attendanceData = [
                        'class_code' => $classSchedule->class_code,
                        'prof_id' => $professorId,
                        'date' => date('Y-m-d'),
                        'start_time' => $classSchedule->start_time,
                        'end_time' => $classSchedule->end_time,
                        'time_in' => $time,
                        'status' => 'Open',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    DB::table('professor_attendances')->insert($attendanceData);

                    $data = [
                        "message" => [
                            'id' => $professorId,
                            "name" => $professorData->user_lastname.', '.$professorData->user_firstname,
                            'time_in' => $time,
                            'class_name' => $classCode->class_name,
                            'status' => 'Successfully opened class!'
                        ]
                      ];

                    return response()->json($data);
                }
            }
        }
    }


    //PUBLIC FUNCTION FOR STUDENT ATTENDANCE
    public function storeStudentAttendance(Request $request, $day, $time, $laboratory)
    {
    $studentId = $request->input('id');

    // Check if there are any class schedules for the given day, time, and laboratory
    $classSchedules = DB::table('facilities')
        ->where('class_day', $day)
        ->where('laboratory', $laboratory)
        ->get();

    if (!$classSchedules || $classSchedules->count() === 0) {
        return response()->json(['message' => 'No class schedule found for the given day and/or laboratory!']);
    }

    foreach ($classSchedules as $classSchedule) {
        $studentData = DB::table('students')
        ->where('faith_id', $studentId)
        ->first();

        $classCode = DB::table('classes')
        ->where('class_code', $classSchedule->class_code)
        ->first();

        // Check if the student is enrolled in the class
        $isStudentEnrolled = DB::table('class_students')
            ->where('faith_id', $studentId)
            ->where('class_code', $classSchedule->class_code)
            ->exists();

        if (!$isStudentEnrolled) {
            // continue;
            $data = [
                "message" => [
                  "id" => $studentId,
                  "name" => $studentData->std_lname.', '.$studentData->std_fname,
                  "courseYrSection" => $studentData->std_course.'-'.$studentData->std_level.''.$studentData->std_section,
                  "class_name" => $classCode->class_name,
                  "time_in" => $time,
                  "status" => "Student not enrolled at ".$classCode->class_name."!"
                ]
              ];

            return response()->json($data);
        }

        $professorAttendance = DB::table('professor_attendances')
        ->where('class_code', $classSchedule->class_code)
        ->where('date', date('Y-m-d'))
        ->whereBetween('time_in', [$classSchedule->start_time, $classSchedule->end_time])
        ->first();

    if (!$professorAttendance) {
        // continue;
        $data = [
            "message" => [
                "id" => $studentId,
                "name" => $studentData->std_lname . ', ' . $studentData->std_fname,
                "courseYrSection" => $studentData->std_course . '-' . $studentData->std_level . '' . $studentData->std_section,
                "class_name" => $classCode->class_name,
                "time_in" => $time,
                "status" => "Class is not yet open for attendance!"
            ]
        ];

        return response()->json($data);
    }

    // Check if the student is late or not
    $timeDifference = strtotime($time) - strtotime($professorAttendance->time_in);
    $lateThreshold = 15 * 60; // 15 minutes in seconds
    $closedThreshold = 30 * 60; // 30 minutes in seconds

    if ($timeDifference > $closedThreshold) {
        $data = [
            "message" => [
                "id" => $studentId,
                "name" => $studentData->std_lname . ', ' . $studentData->std_fname,
                "courseYrSection" => $studentData->std_course . '-' . $studentData->std_level . '' . $studentData->std_section,
                "class_name" => $classCode->class_name,
                "time_in" => $time,
                "status" => "Class Closed!"
            ]
        ];

        return response()->json($data);
    }

    // Check if the student has already taken attendance for this class schedule within the time range
    $existingAttendance = DB::table('attendances')
        ->where('faith_id', $studentId)
        ->where('class_code', $classSchedule->class_code)
        ->where('date', date('Y-m-d'))
        ->whereBetween('time_in', [$classSchedule->start_time, $classSchedule->end_time])
        ->exists();

    if ($existingAttendance) {
        $data = [
            "message" => [
                "id" => $studentId,
                "name" => $studentData->std_lname . ', ' . $studentData->std_fname,
                "courseYrSection" => $studentData->std_course . '-' . $studentData->std_level . '' . $studentData->std_section,
                "class_name" => $classCode->class_name,
                "time_in" => $time,
                "status" => "Already Present"
            ]
        ];

        return response()->json($data);
    }

    if (!$existingAttendance) {
        if ($time >= $classSchedule->start_time && $time <= $classSchedule->end_time) {
            $status = 'Present';
            if ($timeDifference > $lateThreshold) {
                $status = 'Late';
            }

            DB::table('attendances')->insert([
                'class_code' => $classSchedule->class_code,
                'faith_id' => $studentId,
                'date' => date('Y-m-d'),
                'start_time' => $classSchedule->start_time,
                'end_time' => $classSchedule->end_time,
                'time_in' => $time,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $data = [
                "message" => [
                    "id" => $studentId,
                    "name" => $studentData->std_lname . ', ' . $studentData->std_fname,
                    "courseYrSection" => $studentData->std_course . '-' . $studentData->std_level . '' . $studentData->std_section,
                    "class_name" => $classCode->class_name,
                    "time_in" => $time,
                    "status" => $status
                ]
            ];

            return response()->json($data);
        }
    }
    }
}

    //FUNCTION FOR STORING PROFESSOR ATTENDANCE MANUALLY
    public function storeManualProfessorAttendance(Request $request) {
        $professorId = $request->input('id');
    $day = $request->input('day');
    $time = $request->input('time');
    $date = $request->input('date');
    $laboratory = $request->input('laboratory');

    // Convert the date to a Carbon instance
    $requestedDate = Carbon::parse($date);

    // Get the day of the week from the requested date
    $requestedDayOfWeek = $requestedDate->format('l'); // 'Monday', 'Tuesday', etc.

    // Check if the requested day and date match
    if (strtolower($day) !== strtolower($requestedDayOfWeek)) {
        return response()->json([
            'message' => 'The provided date is not a '.$day.'!',
        ], 400);
    }

        // Check if the professor has any class scheduled for the given day, time, and laboratory
        $classSchedules = DB::table('facilities')
            ->join('classes', 'facilities.class_code', '=', 'classes.class_code')
            ->where('classes.prof_id', $professorId)
            ->where('facilities.class_day', $day)
            ->where('facilities.laboratory', $laboratory)
            ->get();

        if ($classSchedules->isEmpty()) {
            return response()->json(['message' => 'No class schedule found for the provided details.']);
        }

        $attendanceRecorded = false;

        foreach ($classSchedules as $classSchedule) {
            if ($time >= $classSchedule->start_time && $time <= $classSchedule->end_time) {
                $existingAttendance = DB::table('professor_attendances')
                    ->where('prof_id', $professorId)
                    ->where('class_code', $classSchedule->class_code)
                    ->where('date', $date)
                    ->whereBetween('time_in', [$classSchedule->start_time, $classSchedule->end_time])
                    ->exists();

                if (!$existingAttendance) {
                    $attendanceData = [
                        'class_code' => $classSchedule->class_code,
                        'prof_id' => $professorId,
                        'date' => $date,
                        'start_time' => $classSchedule->start_time,
                        'end_time' => $classSchedule->end_time,
                        'time_in' => $time,
                        'status' => 'Open',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    DB::table('professor_attendances')->insert($attendanceData);
                    $attendanceRecorded = true;

                    return response()->json([
                        'message'=> 'Successfully opened '.$classSchedule->class_name.' from '.$classSchedule->start_time.' - '.$classSchedule->end_time.' at '.$date
                    ]);
                }
            }
        }

        if ($attendanceRecorded) {
            return response()->json(['message' => 'Attendance recorded successfully']);
        } else {
            return response()->json(['message' => 'Professor attendance already recorded for this class schedule.'], 409);
        }
    }

    //FUNCTION FOR GETTING PROFESSOR ATTENDANCES
    public function getOpenAttendances(string $prof_id)
    {
        $openClasses=DB::table("professor_attendances")
            ->join('classes', 'professor_attendances.class_code', '=', 'classes.class_code')
            ->where('professor_attendances.prof_id', $prof_id)
            ->select('professor_attendances.*', 'classes.class_name')
            ->get();

            return response()->json($openClasses);
    }
}
