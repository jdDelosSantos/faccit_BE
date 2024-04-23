<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OpenAttendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OpenAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        $existingOpenClass = DB::table("open_attendances")
                            ->where("prof_id", $request->prof_id)
                            ->where("laboratory", $request->laboratory)
                            ->where("class_code", $request->class_code)
                            ->where("date", $request->date)
                            ->where("class_day", $request->class_day)
                            ->where("start_time", $request->start_time)
                            ->where("end_time", $request->end_time)
                            ->first();

        if ($existingOpenClass){
            return response()->json(['message' => ''.$request->class_code.' already opened for attendance!'], 409);
        }
        else{
            $openClass = new OpenAttendance;
            $openClass->prof_id = $request->prof_id;
            $openClass->laboratory = $request->laboratory;
            $openClass->class_code = $request->class_code;
            $openClass->date = $request->date;
            $openClass->class_day = $request->class_day;
            $openClass->start_time = $request->start_time;
            $openClass->end_time = $request->end_time;
            $openClass->time_in = $request->start_time;
            $openClass->status = "Open";
            $openClass->save();

            return response()->json(['message' => ''.$request->class_code.' class has been opened for attendance successfully!']);
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
