<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Element;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ElementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $elements = Element::whereHas('category', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('id', 'desc')->get();
    
        $categories = Category::where('user_id', $user->id)->get();
    
        return view('elements.index', compact('elements', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->get();
        $elements = Element::whereHas('category', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        return view('elements.create', compact('categories', 'elements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validateStoreRequest($request);
        
        $category = Category::find($request->input('category_id'));
        $user = Auth::user();
        if ($category->user_id !== $user->id) {
            return redirect()->back()->withErrors(['category_id' => 'La categoría seleccionada no pertenece al usuario logueado.'])->withInput();
        }
    
        $names = $this->getNamesFromStoreRequest($request);
        $this->createElements($request, $names);
    
        return redirect()->route('elements.index')->with('success', 'Elements created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Element $element)
    {
        $user = Auth::user();
        if ($element->category->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        return view('elements.show', compact('element'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Element $element)
    {
        $user = Auth::user();
        if ($element->category->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        $categories = Category::where('user_id', $user->id)->get();
        $elements = Element::whereHas('category', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        return view('elements.edit', compact('element', 'categories', 'elements'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Element $element)
    {
        $this->validateUpdateRequest($request);
        $category = Category::find($request->input('category_id'));
        
        $user = Auth::user();
        if ($category->user_id !== $user->id) {
            return redirect()->back()->withErrors(['category_id' => 'La categoría seleccionada no pertenece al usuario logueado.'])->withInput();
        }
        $this->updateElement($request, $element);

        return redirect()->route('elements.index')->with('success', 'Element updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Element $element)
    {
        $user = Auth::user();
        if ($element->category->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        $element->delete();

        return redirect()->route('elements.index')->with('success', 'Element deleted successfully.');
    }

    /**
     * Validate the request.
     */
    private function validateUpdateRequest(Request $request){
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'parent_id' => 'nullable|exists:elements,id',
        ];    
        $request->validate($rules);

    }
    private function validateStoreRequest(Request $request)
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'parent_id' => 'nullable|exists:elements,id',
            'separator' => 'nullable|string',
            'input_mode' => 'required|in:individual,bulk',
        ];
    
        if ($request->input('input_mode') === 'individual') {
            $rules['names'] = 'nullable|array'; // Hacer el campo names opcional
            $rules['names.*'] = 'nullable|string|max:255';
        } else {
            $rules['bulk_names'] = 'nullable|string'; // Hacer el campo bulk_names opcional
        }
    
        $request->validate($rules);
    }


    /**
     * Get the names from the request.
     */
    private function getNamesFromStoreRequest(Request $request)
    {
        $names = [];

        if ($request->input('input_mode') === 'individual') {
            $names = $request->input('names', []);
        } else {
            $separator = $request->input('separator', 'comma');
            $bulkNames = [];

            switch ($separator) {
                case 'comma':
                    $bulkNames = preg_split('/,/', $request->input('bulk_names'));
                    break;
                case 'semicolon':
                    $bulkNames = preg_split('/;/', $request->input('bulk_names'));
                    break;
                case 'space':
                    $bulkNames = preg_split('/\s+/', $request->input('bulk_names'));
                    break;
                case 'tab':
                    $bulkNames = preg_split('/\t/', $request->input('bulk_names'));
                    break;
                case 'newline':
                    $bulkNames = preg_split('/\n/', $request->input('bulk_names'));
                    break;
            }

            $names = array_merge($names, $bulkNames);
        }

        return $names;
    }

    /**
     * Create elements from the names.
     */
    private function createElements(Request $request, $names)
    {
        $parentId = $request->input('parent_id');
    
        foreach ($names as $name) {
            if (empty($name)) {
                continue; // No crear un elemento si el nombre está vacío
            }
    
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
    }

    /**
     * Update the specified element.
     */
    private function updateElement(Request $request, Element $element)
    {
        $parentId = $request->input('parent_id');

        // Verificar si el nombre del elemento es igual al nombre del padre
        if ($parentId) {
            $parentElement = Element::find($parentId);
            if ($parentElement && $parentElement->name === $request->input('name')) {
                return redirect()->back()->withErrors(['name' => 'El nombre del elemento no puede ser igual al nombre del padre.'])->withInput();
            }
        }

        $element->update($request->all());
    }
    public function bulkDelete(Request $request)
    {
        $elementIds = $request->input('element_ids');

        // Verificar que los elementos pertenecen al usuario actual
        $user = Auth::user();
        $elements = Element::whereIn('id', $elementIds)->get();

        foreach ($elements as $element) {
            if ($element->category->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.']);
            }
        }

        // Eliminar los elementos
        Element::whereIn('id', $elementIds)->delete();

        return response()->json(['success' => true]);
    }
}