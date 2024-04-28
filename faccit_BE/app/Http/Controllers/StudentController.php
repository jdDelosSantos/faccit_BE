<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use League\Csv\Reader;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::withCount('studentImages')
                      ->get();

    return response()->json($students);
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
        $existingStudent = Student::where('faith_id', $request->faith_id)->first();

        if ($existingStudent) {
            $studentID = $existingStudent->faith_id;
            return response()->json([
              'message' => "Error! {$studentID} Already Exists!",
            ], 409);
          }
        else{
            $students = new Student;
            $students->faith_id = $request->faith_id;
            $students->std_lname = $request->std_lname;
            $students->std_fname = $request->std_fname;
            $students->std_course = $request->std_course;
            $students->std_level = $request->std_level;
            $students->std_section = $request->std_section;
            $students->save();

            $message=(object)[
                "status"=>"1",
                "message"=> "Successfully Added ". $request->faith_id
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
    public function update(Request $request, string $faith_id)
    {
        $updateStudent = Student::where('faith_id', $faith_id)->first();
        $updateStudent->std_fname = $request->std_fname;
        $updateStudent->std_lname = $request->std_lname;
        $updateStudent->std_course = $request->std_course;
        $updateStudent->std_level = $request->std_level;
        $updateStudent->std_section = $request->std_section;
        $updateStudent->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Updated ".$faith_id
        ];
        return response()->json($message);
    }

    public function deactivateStudent(Request $request, string $faith_id)
    {
        $deactivateStudent = Student::where('faith_id', $faith_id)->first();
        $deactivateStudent->std_status = $request->std_status;
        $deactivateStudent->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Disabled ".$faith_id
        ];
        return response()->json($message);
    }

    public function activateStudent(Request $request, string $faith_id)
    {
        $activateStudent = Student::where('faith_id', $faith_id)->first();
        $activateStudent->std_status = $request->std_status;
        $activateStudent->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Reactivated ".$faith_id
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

    public function getSuperAdminAllStudents()
    {
        $studentCount = Student::all()
                    ->count();

    return response()->json([
        'student_count' => $studentCount
    ]);
    }

    public function bulkInsertFromCSV(Request $request)
{
    $file = $request->file('csv_file');

    if ($file) {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Set the header offset if your CSV file has a header row

         // Get the header row
         $headers = $csv->getHeader();

         // Check if the required columns exist
         $requiredColumns = ['faith_id', 'std_lname', 'std_fname', 'std_course', 'std_level', 'std_section'];
         $missingColumns = array_diff($requiredColumns, $headers);

         if (!empty($missingColumns)) {
             return response()->json([
                 'message' => 'CSV file is missing the following required columns: ' . implode(', ', $missingColumns),
             ], 400);
         }

        $records = $csv->getRecords();
        $errors = [];
        $insertedCount = 0;

        foreach ($records as $record) {
            $validator = Validator::make($record, [
                'faith_id' => 'required|unique:students,faith_id',
                'std_lname' => 'required',
                'std_fname' => 'required',
                'std_course' => 'required',
                'std_level' => 'required',
                'std_section' => 'required',
            ]);

            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
                continue;
            }

            $existingStudent = Student::where('faith_id', $record['faith_id'])->first();

            if ($existingStudent) {
                $errors[] = "A record with faith_id '{$record['faith_id']}' already exists.";
                continue;
            }

            Student::create([
                'faith_id' => $record['faith_id'],
                'std_lname' => $record['std_lname'],
                'std_fname' => $record['std_fname'],
                'std_course' => $record['std_course'],
                'std_level' => $record['std_level'],
                'std_section' => $record['std_section'],
            ]);

            $insertedCount++;
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Bulk insert completed with conflicts due to already existing students',
                'inserted_count' => $insertedCount,
                'errors' => $errors,
            ], 206);
        }

        return response()->json(['message' => 'Bulk insert successful', 'inserted_count' => $insertedCount]);
    }

    return response()->json(['message' => 'CSV file not provided'], 400);
}

}
