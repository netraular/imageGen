<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Element;
use App\Models\Category;

class ElementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $elements = Element::all();
        return view('elements.index', compact('elements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $elements = Element::all();
        return view('elements.create', compact('categories', 'elements'));
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
            'parent_id' => 'nullable|exists:elements,id',
        ]);

        $parentId = $request->input('parent_id');

        foreach ($request->input('names') as $name) {
            // Verificar si el nombre del elemento es igual al nombre del padre
            if ($parentId) {
                $parentElement = Element::find($parentId);
                if ($parentElement && $parentElement->name === $name) {
                    return redirect()->back()->withErrors(['names' => 'El nombre del elemento no puede ser igual al nombre del padre.'])->withInput();
                }
            }

            Element::create([
                'category_id' => $request->input('category_id'),
                'name' => $name,
                'parent_id' => $parentId,
            ]);
        }

        return redirect()->route('elements.index')->with('success', 'Elements created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Element $element)
    {
        return view('elements.show', compact('element'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Element $element)
    {
        $categories = Category::all();
        $elements = Element::all();
        return view('elements.edit', compact('element', 'categories', 'elements'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Element $element)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:elements,id',
        ]);

        $parentId = $request->input('parent_id');

        // Verificar si el nombre del elemento es igual al nombre del padre
        if ($parentId) {
            $parentElement = Element::find($parentId);
            if ($parentElement && $parentElement->name === $request->input('name')) {
                return redirect()->back()->withErrors(['name' => 'El nombre del elemento no puede ser igual al nombre del padre.'])->withInput();
            }
        }

        $element->update($request->all());

        return redirect()->route('elements.index')->with('success', 'Element updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Element $element)
    {
        $element->delete();

        return redirect()->route('elements.index')->with('success', 'Element deleted successfully.');
    }
}