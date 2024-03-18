<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $professors = User::where('role', 'admin')
                      ->withCount('professorImages')
                      ->get();
    return response()->json($professors);
    }

    public function getProfessors()
    {
    $professors = User::where('role', 'admin')
    ->where('user_status', 'Active')
    ->select('user_lastname', 'user_firstname','prof_id')
    ->get();
    return response()->json($professors);
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
        $existingProfessor = User::where('prof_id', $request->prof_id)->orWhere('email',$request->email)->first();
        if ($existingProfessor) {
            $profID = $existingProfessor->prof_id;
            $profEmail = $existingProfessor->email;
            return response()->json([
              'message' => "Error! Prof ID or Email Already Exists!",
            ], 409);
          }

        $users = new User;
        $users->user_lastname = $request->user_lastname;
        $users->user_firstname = $request->user_firstname;
        $users->prof_id = $request->prof_id;
        $users->email= $request->email;
        $password = "TempPassword123";
        $users->password = Hash::make($password);
        $users->role = "admin";
        $users->save();

        $message=(object)[
            "status"=>"1",
            "message"=> "Successfully Added ". $request->prof_id
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
        $updateProfessor = User::where('prof_id', $prof_id)->first();
        if (!$updateProfessor) {
            return response()->json([
                "status" => "0",
                "message" => "User not found with prof_id: $prof_id"
            ], 404);
        }
        else{
            $updateProfessor->user_lastname = $request->user_lastname;
            $updateProfessor->user_firstname = $request->user_firstname;
            $updateProfessor->save();

            $message = (object) [
                "status" => "1",
                "message" => "Successfully Updated ".$prof_id
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

    public function storeNewSuperAdmin(Request $request)
    {
        $users = new User;
        $users->user_lastname = $request->user_lastname;
        $users->user_firstname = $request->user_firstname;
        $users->email= $request->email;
        $password = "123";
        $users->password = Hash::make($password);
        $users->role = "super_admin";
        $users->save();

        $message=(object)[
            "status"=>"1",
            "message"=> "Successfully Added SuperAdmin"
        ];
        return response()->json($message);
    }
}
