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
    public function update(Request $request, string $faith_id)
    {
        $updateStudentImage = StudentImage::where('faith_id', $faith_id)->where('std_folder_img_url', $request->std_folder_img_url)->first();
        if(!$updateStudentImage){
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
        } else{
            $updateStudentImage->std_folder_url = $request->std_folder_url;
            $updateStudentImage->std_folder_img_url = $request->std_folder_img_url;
            $updateStudentImage->save();

            $message=(object)[
                "status"=>"1",
                "message"=> "Successfully Updated Image"
            ];
            return response()->json($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getStudentImages(Request $request)
    {
        try {
        // Retrieve only 'student_id' and 'data_url' columns from the database
        $studentImages = StudentImage::where('faith_id',  $request->faith_id)->select('std_folder_url', 'std_folder_img_url')->get();
        // select('student_id', 'data_url')->get();
        // Return the specific data to the frontend
        return response()->json($studentImages);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching data URLs'], 500);
        }
    }

    public function getImagesForNode()
    {
        try {
            $studentImages = StudentImage::select('faith_id', 'std_folder_url', 'std_folder_img_url')->get();
        return response()->json($studentImages);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching data URLs'], 500);
        }
    }
}
