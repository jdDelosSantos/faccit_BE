<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{


    public function login(Request $request)
    {
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $user = auth()->user();

        // Check user role directly from the database
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return response()->json([
                'firstname' => $user->firstname,
                'surname' => $user->surname,
                'email' => $user->email,
                'role' => $user->role
            ]);
        }  else {
            return response()->json(['error' => 'Invalid role assigned to user'], 400);
        }
        // if ($user->role === 'super_admin' || $user->role === 'admin') {
        //     $token = $user->createToken('Super Admin Token')->plainTextToken;
        // } elseif ($user->role === 'admin') {
        //     return response()->json($user);
        //     $token = $user->createToken('Admin Token')->plainTextToken;
        //     // $token = auth()->login($user)
        // } else {
        //     return response()->json(['error' => 'Invalid role assigned to user'], 400);
        // }

    } else {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
    }
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
        //
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
