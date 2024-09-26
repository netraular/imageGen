<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Value;
use App\Models\Category;

class ValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $values = Value::all();
        return view('values.index', compact('values'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $values = Value::all();
        return view('values.create', compact('categories', 'values'));
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
            'parent_id' => 'nullable|exists:values,id',
        ]);

        foreach ($request->input('names') as $name) {
            Value::create([
                'category_id' => $request->input('category_id'),
                'name' => $name,
                'parent_id' => $request->input('parent_id'),
            ]);
        }

        return redirect()->route('values.index')->with('success', 'Values created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Value $value)
    {
        return view('values.show', compact('value'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Value $value)
    {
        $categories = Category::all();
        $values = Value::all();
        return view('values.edit', compact('value', 'categories', 'values'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Value $value)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:values,id',
        ]);

        $value->update($request->all());

        return redirect()->route('values.index')->with('success', 'Value updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Value $value)
    {
        $value->delete();

        return redirect()->route('values.index')->with('success', 'Value deleted successfully.');
    }
}