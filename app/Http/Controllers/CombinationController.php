<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Combination;
use App\Models\Generation;
use App\Jobs\GenerateLlmResponseJob;

class CombinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $combinations = Combination::all();
        return view('combinations.index', compact('combinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('combinations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sentence' => 'required|string',
        ]);

        $combination = Combination::create($request->all());

        // Despacha el job para generar respuestas
        GenerateLlmResponseJob::dispatch($combination);

        return redirect()->route('combinations.index')->with('success', 'Combination created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Combination $combination)
    {
        return view('combinations.show', compact('combination'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Combination $combination)
    {
        return view('combinations.edit', compact('combination'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Combination $combination)
    {
        $request->validate([
            'sentence' => 'required|string',
        ]);

        $combination->update($request->all());

        return redirect()->route('combinations.index')->with('success', 'Combination updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Combination $combination)
    {
        $combination->delete();

        return redirect()->route('combinations.index')->with('success', 'Combination deleted successfully.');
    }

    /**
     * Generate responses for the specified combination.
     */
    public function generateResponses(Request $request, Combination $combination)
    {
        // Despacha el job para generar respuestas
        GenerateLlmResponseJob::dispatch($combination);

        return redirect()->route('combinations.show', $combination)->with('success', 'Responses generation job dispatched.');
    }
}