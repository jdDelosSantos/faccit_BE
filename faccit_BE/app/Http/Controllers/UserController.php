<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use League\Csv\Reader;
use Illuminate\Support\Facades\Validator;

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
    ->select('user_lastname', 'user_firstname','prof_id', 'user_status')
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

    public function deactivateUser(Request $request, string $prof_id)
    {
        $deactivateUser = User::where('prof_id', $prof_id)->first();
        $deactivateUser->user_status = $request->user_status;
        $deactivateUser->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Disabled ".$prof_id
        ];
        return response()->json($message);
    }

    public function activateUser(Request $request, string $prof_id)
    {
        $deactivateUser = User::where('prof_id', $prof_id)->first();
        $deactivateUser->user_status = $request->user_status;
        $deactivateUser->save();

        $message = (object) [
            "status" => "1",
            "message" => "Successfully Enabled ".$prof_id
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

    public function getProfessorInfo(string $prof_id)
    {
        $profInfo = User::where('prof_id', $prof_id)
            ->select('prof_id', 'user_lastname', 'user_firstname', 'email')
            ->first();


        return response()->json($profInfo);
    }

    public function updateProfessorInfo(Request $request, string $prof_id)
    {
        $prof= User::where('prof_id', $prof_id)
            ->first();

            if (!$prof){
                return response()->json(['message' => 'Professor not found!'], 404);
            }
            else{
                $prof->user_lastname = $request->user_lastname;
                $prof->user_firstname = $request->user_firstname;
                $prof->save();

                return response()->json(['message' => 'Professor successfully updated!']);
            }
    }

    public function updateProfPass(Request $request, string $prof_id)
    {
        $password = $request->password;
        $newPassword = $request->new_password;
        $retypedPassword = $request->retyped_password;

        $prof = User::where('prof_id', $prof_id)->first();

            if (!$prof){
                return response()->json(['message' => 'Professor not found!'], 404);
            }
            if (!Hash::check($password, $prof->password)) {
                return response()->json(['message' => 'Incorrect password!'], 401);
            }
            if ($newPassword !== $retypedPassword) {
                return response()->json(['message' => 'New passwords do not match!'], 400);
            }

            if ($newPassword === $password) {
                return response()->json(['message' => 'New password cannot be the same as the old password!'], 400);
            }
            $prof->password = Hash::make($newPassword);
            $prof->save();

            return response()->json(['message' => 'Password updated successfully!'], 200);
    }


    public function getUserInfo(string $email)
    {
        $userInfo = User::where('email', $email)
            ->select('user_lastname', 'user_firstname', 'email')
            ->first();


        return response()->json($userInfo);
    }

    public function updateUserInfo(Request $request, string $email)
    {
        $user= User::where('email', $email)
            ->first();

            if (!$user){
                return response()->json(['message' => 'Super Admin not found!'], 404);
            }
            else{
                $user->user_lastname = $request->user_lastname;
                $user->user_firstname = $request->user_firstname;
                $user->save();

                return response()->json(['message' => 'Super Admin successfully updated!']);
            }
    }

    public function updateUserPass(Request $request, string $email)
    {
        $password = $request->password;
        $newPassword = $request->new_password;
        $retypedPassword = $request->retyped_password;

        $user = User::where('email', $email)->first();

            if (!$user){
                return response()->json(['message' => 'Super Admin not found!'], 404);
            }
            if (!Hash::check($password, $user->password)) {
                return response()->json(['message' => 'Incorrect password!'], 401);
            }
            if ($newPassword !== $retypedPassword) {
                return response()->json(['message' => 'New passwords do not match!'], 400);
            }

            if ($newPassword === $password) {
                return response()->json(['message' => 'New password cannot be the same as the old password!'], 400);
            }
            $user->password = Hash::make($newPassword);
            $user->save();

            return response()->json(['message' => 'Password updated successfully!'], 200);
    }

    public function resetProfPass(string $prof_id, Request $request)
    {

        $resetPass = User::where('prof_id', $prof_id)
                    ->first();

            if (!$resetPass){
                return response()->json(['message' => 'Professor cannot be found!'], 404);
            }

            $newPassword = "TempPassword123";

            // Check if the new password is the same as the current hashed password
            if (Hash::check($newPassword, $resetPass->password)) {
                return response()->json(['message' => 'The password is already at default.'], 400);
            }
                $resetPass->password = Hash::make($newPassword);
                $resetPass->save();

                return response()->json(['message' => 'Successfully reset password for '.$prof_id.'!']);
    }


    public function bulkInsertFromCSVProf(Request $request)
{
    $file = $request->file('csv_file');
    if ($file) {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Set the header offset if your CSV file has a header row

         // Get the header row
         $headers = $csv->getHeader();

         // Check if the required columns exist
         $requiredColumns = ['user_lastname', 'user_firstname', 'prof_id', 'email'];
         $missingColumns = array_diff($requiredColumns, $headers);

         if (!empty($missingColumns)) {
             return response()->json([
                 'message' => 'CSV file is missing the following required columns: ' . implode(', ', $missingColumns),
             ], 400);
         }

        $records = $csv->getRecords();
        $errors = [];
        $insertedCount = 0;
        $password = "TempPassword123";
        $hashPassword = Hash::make($password);
        $role = "admin";

        foreach ($records as $record) {
            $validator = Validator::make($record, [

                'user_lastname' => 'required',
                'user_firstname' => 'required',
                'prof_id' => 'required|string|unique:users,prof_id',
                'email' => 'required|unique:users,email',
            ]);



            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
                continue;
            }

            $existingUser = User::where('prof_id', $record['prof_id'])->orWhere('email', $record['email'])->first();
            if ($existingUser) {
                $errors[] = "A record with prof_id '{$record['prof_id']}' or email '{$record['email']}' already exists.";
                continue;
            }
            $prof_id = $record['prof_id'];

            $users = new User;
            $users->user_lastname = $record['user_lastname'];
            $users->user_firstname = $record['user_firstname'];
            $users->prof_id = $prof_id;
            $users->email= $record['email'];
            $users->password = $hashPassword;
            $users->role= $role;
            $users->save();

            $insertedCount++;
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Bulk insert completed with conflicts due to already existing professors',
                'inserted_count' => $insertedCount,
                'errors' => $errors,
            ], 206);
        }

        return response()->json(['message' => 'Bulk insert successful', 'inserted_count' => $insertedCount]);
    }

    return response()->json(['message' => 'CSV file not provided'], 400);
}

}
