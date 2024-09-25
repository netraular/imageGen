<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Concept;
use App\Models\Category;

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
        $categories = \App\Models\Category::all();
        $concepts = Concept::all();
        return view('concepts.create', compact('categories', 'concepts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'names' => 'required|array',
            'names.*' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'parent_id' => 'nullable|exists:concepts,id',
        ]);

        foreach ($request->input('names') as $name) {
            Concept::create([
                'category_id' => $request->input('category_id'),
                'name' => $name,
                'parent_id' => $request->input('parent_id'),
            ]);
        }

        return redirect()->route('concepts.index')->with('success', 'Conceptos creados exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $concept = Concept::findOrFail($id);
        return view('concepts.show', compact('concept'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $concept = Concept::findOrFail($id);
        $categories = \App\Models\Category::all();
        $concepts = Concept::all();
        return view('concepts.edit', compact('concept', 'categories', 'concepts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Concept $concept)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:concepts,id',
        ]);

        $concept->update($request->all());

        return redirect()->route('concepts.index')->with('success', 'Concepto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Concept $concept)
    {
        $concept->delete();

        return redirect()->route('concepts.index')->with('success', 'Concepto eliminado exitosamente.');
    }

    public function generateCombinations(Request $request)
    {
        // Lógica para generar combinaciones a partir de conceptos seleccionados
        // Esto podría incluir la combinación de conceptos y la creación de nuevas combinaciones en la base de datos

        return redirect()->route('combinations.index')->with('success', 'Combinations generated successfully.');
    }
}
