<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;

class CollegeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colleges = College::select('college_name', 'college_description','college_status')->get();


        return response()->json($colleges);
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
        $existingCollege = College::where('college_name', $request->college_name)->first();

        if ($existingCollege) {
            $collegeName = $existingCollege->college_name; // Assuming 'name' is the property for college name
            return response()->json([
              'message' => "Error! {$collegeName} Already Exists!",
            ], 409);
          }
        else{
            $colleges = new College;
            $colleges->college_name = $request->college_name;
            $colleges->college_description = $request->college_description;

            $colleges->save();

            $message=(object)[
                "status"=>"1",
                "message"=> "Successfully Added ". $request->college_name
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
    public function update(Request $request, string $college_name)
    {
        $updateCollege = College::where('college_name', $college_name)->first();

        $updateCollege->college_description = $request->college_description;

        $updateCollege->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Updated ".$college_name
        ];
        return response()->json($message);
    }

    public function deactivateCollege(Request $request, string $college_name)
    {
        $deactivateCollege = College::where('college_name', $college_name)->first();
        $deactivateCollege->college_status = $request->college_status;
        $deactivateCollege->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Disabled ".$college_name
        ];
        return response()->json($message);
    }

    public function activateCollege(Request $request, string $college_name)
    {
        $activateCollege = College::where('college_name', $college_name)->first();
        $activateCollege->college_status = $request->college_status;
        $activateCollege->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Reactivated ".$college_name
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
}
