<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Generation;
use App\Models\Combination;

class GenerationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $generations = Generation::all();
        return view('generations.index', compact('generations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $combinations = Combination::all();
        return view('generations.create', compact('combinations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sentence' => 'required|string',
            'combination_id' => 'required|exists:combinations,id',
        ]);

        Generation::create($request->all());

        return redirect()->route('generations.index')->with('success', 'Generation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Generation $generation)
    {
        return view('generations.show', compact('generation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Generation $generation)
    {
        $combinations = Combination::all();
        return view('generations.edit', compact('generation', 'combinations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Generation $generation)
    {
        $request->validate([
            'sentence' => 'required|string',
            'combination_id' => 'required|exists:combinations,id',
        ]);

        $generation->update($request->all());

        return redirect()->route('generations.index')->with('success', 'Generation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Generation $generation)
    {
        $generation->delete();

        return redirect()->route('generations.index')->with('success', 'Generation deleted successfully.');
    }
}