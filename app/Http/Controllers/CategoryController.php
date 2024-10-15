<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('user_id', Auth::id())->orderBy('order')->get();
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
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Categorías creadas exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $this->authorizeCategory($category);
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $this->authorizeCategory($category);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorizeCategory($category);

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

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoría eliminada exitosamente.');
    }

    /**
     * Authorize that the authenticated user owns the category.
     *
     * @param  \App\Models\Category  $category
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeCategory(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
    }
}