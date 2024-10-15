<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('order')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar que se reciba al menos un nombre de categoría
        $request->validate([
            'names' => 'required|array|min:1',
            'names.*' => 'required|string|max:255',
            'descriptions.*' => 'nullable|string',
            'orders.*' => 'nullable|integer',
        ]);
        
        foreach ($request->names as $key => $name) {
            Category::create([
                'name' => $name,
                'description' => $request->descriptions[$key] ?? null,
                'order' => $request->orders[$key] ?? 0,
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Categorías creadas exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);
        
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'order' => $request->order,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}