<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfessorImage;

class ProfessorImageController extends Controller
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
        $professorImage = new ProfessorImage;
        $professorImage->faith_id = $request-> faith_id;
        $professorImage->std_folder_url = $request->std_folder_url;
        $professorImage->std_folder_img_url = $request->std_folder_img_url;
        $professorImage->save();

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
    public function update(Request $request, string $prof_id)
    {
        $updateProfessorImage = ProfessorImage::where('faith_id', $prof_id)->where('std_folder_img_url', $request->std_folder_img_url)->first();
        if(!$updateProfessorImage){
        $professorImage = new ProfessorImage;
        $professorImage->faith_id = $request-> faith_id;
        $professorImage->std_folder_url = $request->std_folder_url;
        $professorImage->std_folder_img_url = $request->std_folder_img_url;
        $professorImage->save();

        $message=(object)[
            "status"=>"1",
            "message"=> "Successfully Added Image"
        ];
        return response()->json($message);
        } else{
            $updateProfessorImage->std_folder_url = $request->std_folder_url;
            $updateProfessorImage->std_folder_img_url = $request->std_folder_img_url;
            $updateProfessorImage->save();

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

    public function getProfessorImages(Request $request)
    {
        try {
        // Retrieve only 'prof_id' and 'data_url' columns from the database
        $professorImages = ProfessorImage::where('faith_id',  $request->faith_id)->select('std_folder_url', 'std_folder_img_url')->get();

        // Return the specific data to the frontend
        return response()->json($professorImages);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching data URLs'], 500);
        }
    }
}
