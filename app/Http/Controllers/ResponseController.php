<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LlmResponse;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $responses = LlmResponse::all();
        return view('responses.index', compact('responses'));
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
         $request->validate([
             'combination_id' => 'required|exists:combinations,id',
             'response' => 'required|string',
             'source' => 'required|string',
         ]);
 
         LlmResponse::create($request->all());
 
         return redirect()->route('responses.index')->with('success', 'Response created successfully.');
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
    public function destroy($id)
    {
        $response = LlmResponse::findOrFail($id);
        $response->delete();

        return redirect()->route('responses.index')->with('success', 'Response deleted successfully.');
    }
}
