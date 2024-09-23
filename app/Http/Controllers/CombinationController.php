<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Combination;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'is_generated' => 'boolean',
        ]);

        Combination::create($request->all());

        return redirect()->route('combinations.index')->with('success', 'Combination created successfully.');
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
        $combination = Combination::findOrFail($id);
        $combination->delete();

        return redirect()->route('combinations.index')->with('success', 'Combination deleted successfully.');
    }

    public function generateResponses(Request $request, Combination $combination)
    {
        // Lógica para generar respuestas para una combinación usando la API Groq
        // Esto podría incluir la llamada a la API y la creación de nuevas respuestas en la base de datos

        return redirect()->route('combinations.show', $combination)->with('success', 'Responses generated successfully.');
    }
}
