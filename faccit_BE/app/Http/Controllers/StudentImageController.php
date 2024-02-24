<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentImage;

class StudentImageController extends Controller
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
        $studentImage = new StudentImage;
        $studentImage->faith_id = $request-> faith_id;
        $studentImage->std_folder_url = $request->std_folder_url;
        $studentImage->std_folder_img_url = $request->std_folder_img_url;
        $studentImage->save();

        $message=(object)[
            "status"=>"1",
            "message"=> "Successfully Added Image"
        ];
        return response()->json($message);
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
