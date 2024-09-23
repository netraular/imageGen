<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Concept;

class ConceptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $concepts = Concept::all();
        return view('concepts.index', compact('concepts'));
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
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:concepts,id',
        ]);

        Concept::create($request->all());

        return redirect()->route('concepts.index')->with('success', 'Concept created successfully.');
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
        $concept = Concept::findOrFail($id);
        $concept->delete();

        return redirect()->route('concepts.index')->with('success', 'Concept deleted successfully.');
    }

    public function generateCombinations(Request $request)
    {
        // Lógica para generar combinaciones a partir de conceptos seleccionados
        // Esto podría incluir la combinación de conceptos y la creación de nuevas combinaciones en la base de datos

        return redirect()->route('combinations.index')->with('success', 'Combinations generated successfully.');
    }
}
