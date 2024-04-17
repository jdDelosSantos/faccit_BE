<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestCancelClass;

class CancelClassController extends Controller
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
    public function store(Request $request, string $id)
    {
        $cancelClass = new RequestCancelClass;
        $cancelClass->prof_id = $id;
        $cancelClass->laboratory = $request->laboratory;
        $cancelClass->class_code = $request->class_code;
        $cancelClass->class_day = $request->class_day;
        $cancelClass->start_time = $request->start_time;
        $cancelClass->end_time = $request->end_time;

        $cancelClass->save();

        return response()->json(['message' => 'Cancel Class requested successfully']);
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
