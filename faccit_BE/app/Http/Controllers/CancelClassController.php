<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestCancelClass;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CancelClassController extends Controller
{

    public function getCancelClassRequestsforProfessor(string $prof_id)
    {
        $cancelClasses = RequestCancelClass::where('prof_id', $prof_id)->with(['class' => function ($query) {
        $query->select('class_name', 'class_code');
        }])
        ->with(['professor' => function ($query) {
        $query->select('prof_id', 'user_firstname', 'user_lastname');
        }])
        ->select('*', DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as created_date'))
        ->get();

        return response()->json($cancelClasses);
    }


    public function index()
    {
        $cancelClasses = RequestCancelClass::with(['class' => function ($query) {
        $query->select('class_name', 'class_code');
        }])
        ->with(['professor' => function ($query) {
        $query->select('prof_id', 'user_firstname', 'user_lastname');
        }])
        ->select('*', DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as created_date'))
        ->get();

        return response()->json($cancelClasses);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $cancelClass = new RequestCancelClass;
        $cancelClass->prof_id = $id;
        $cancelClass->laboratory = $request->laboratory;
        $cancelClass->class_code = $request->class_code;
        $cancelClass->class_day = $request->class_day;
        $cancelClass->start_time = $request->start_time;
        $cancelClass->end_time = $request->end_time;
        $cancelClass->remarks = $request->remarks;
        $cancelClass->save();

        return response()->json(['message' => 'Cancel Class requested successfully']);
    }

    public function approveCancelClass(Request $request, int $id)
{
    // Validate the request data
    $validatedData = $request->validate([
        'class_code' => 'required',
        'class_day' => 'required',
        'start_time' => 'required|date_format:H:i:s',
        'end_time' => 'required|date_format:H:i:s',
        'laboratory' => 'required',
    ]);

    // Get the class name by joining the facilities and classes tables
    $className = Facility::join('classes', 'facilities.class_code', '=', 'classes.class_code')
        ->where('facilities.class_code', $validatedData['class_code'])
        ->where('facilities.class_day', $validatedData['class_day'])
        ->where('facilities.start_time', $validatedData['start_time'])
        ->where('facilities.end_time', $validatedData['end_time'])
        ->where('facilities.laboratory', $validatedData['laboratory'])
        ->select('classes.class_name')
        ->first();

    // Delete the record with the absent_* values
    $findRecord = Facility::where('class_code', $validatedData['class_code'])
        ->where('class_day', $validatedData['class_day'])
        ->where('start_time', $validatedData['start_time'])
        ->where('end_time', $validatedData['end_time'])
        ->where('laboratory', $validatedData['laboratory'])
        ->delete();

        if (!$findRecord){
            return response()->json([
                'message' => 'Cannot find Class Schedule to Cancel!',
            ], 404);
        }

    // Update the makeup_class_status in the request_makeup_classes table
    $requestMakeupClass = RequestCancelClass::find($id);
    $requestMakeupClass->cancel_class_status = 'Approved';
    $requestMakeupClass->save();

        return response()->json([
            'message' => 'Approved the Cancellation of ' . $className->class_name . ' class successfully.',
        ], 200);
}


    public function rejectCancelClass(Request $request)
    {
    $reject = RequestCancelClass::find($request->id);
    $reject->cancel_class_status = 'Rejected';
    $reject->save();

    return response()->json([
    'message' => 'Cancel Class Request rejected successfully.',
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

    public function getSuperAdminAllPendingCancel()
    {
        $pending = RequestCancelClass::where('cancel_class_status', "Pending")
        ->count();

        return response()->json(['pending_cancel_count' => $pending]);
    }
}
