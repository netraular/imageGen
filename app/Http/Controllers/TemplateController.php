<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Element;
use App\Models\Prompt;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateLlmResponseJob;
use App\Models\LlmResponse;
use App\Models\Category;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = Template::all();
        return view('templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sentence' => 'required|string',
        ]);

        $template = Template::create($request->all());

        // Llamar a la función para generar prompts

        return redirect()->route('templates.index')->with('success', 'Template created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Template $template)
    {
        return view('templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Template $template)
    {
        return view('templates.edit', compact('template'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Template $template)
    {
        $request->validate([
            'sentence' => 'required|string',
        ]);

        $template->update($request->all());

        return redirect()->route('templates.index')->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Template deleted successfully.');
    }
    public function generatePrompts(Request $request)
    {
        $templateId = $request->input('template_id');
        $template = Template::findOrFail($templateId);
        // Obtener la frase de la plantilla
        $sentence = $template->sentence;
    
        // Buscar todas las categorías entre doble claudator
        preg_match_all('/{{(.*?)}}/', $sentence, $matches);
        $categories = array_unique($matches[1]);

        // Obtener los IDs correspondientes y sustituir en el array
        $CategoriesWithIds = $this->replaceCategoriesWithIds($categories);
        $categories = $CategoriesWithIds['ids'];
        $categoriesIdMap = $CategoriesWithIds['map'];
        
        // Obtener todos los elementos de cada categoría
        $elementsByCategory = [];
        foreach ($categories as $category) {
            // Separar la categoría en caso de que tenga un punto
            $categoryParts = explode('.', $category);
            $mainCategoryId = $categoryParts[0];
            $subCategoryId = isset($categoryParts[1]) ? $categoryParts[1] : null;
    
            // Obtener los elementos de la categoría principal
            $mainElements = Element::where('category_id', $mainCategoryId)->pluck('name', 'id')->toArray();
    
            // Si hay una subcategoría, obtener los elementos de la subcategoría para cada elemento principal
            if ($subCategoryId) {
                foreach ($mainElements as $mainElementId => $mainElementName) {
                    $elementsByCategory[$category][$mainElementId] = Element::where('category_id', $subCategoryId)
                        ->whereHas('parent', function ($query) use ($mainElementId) {
                            $query->where('id', $mainElementId);
                        })->pluck('name', 'id')->toArray();
                }
            } else {
                // Si no hay subcategoría, simplemente guardar los elementos de la categoría principal
                $elementsByCategory[$category] = $mainElements;
            }
        }
        $combinations = $this->generateCombinations($elementsByCategory);

        $combinationsWithNames = $this->replaceIdsWithNames($combinations, $elementsByCategory, $categoriesIdMap);

        
        // Crear los prompts con las combinaciones generadas
        $batchSize = 1000; // Define el tamaño del lote
        $totalCombinations = count($combinationsWithNames);
        
        DB::transaction(function () use ($template, $sentence, $combinationsWithNames, $batchSize, $totalCombinations) {
            for ($i = 0; $i < $totalCombinations; $i += $batchSize) {
                $batch = array_slice($combinationsWithNames, $i, $batchSize);
        
                foreach ($batch as $combination) {
                    $promptSentence = $sentence;
                    foreach ($combination as $category => $element) {
                        $promptSentence = str_replace("{{{$category}}}", $element, $promptSentence);
                    }
        
                    Prompt::create([
                        'sentence' => $promptSentence,
                        'template_id' => $template->id,
                    ]);
                }
        
                // Confirmar el lote actual
                DB::commit();
        
                // Comenzar una nueva transacción para el siguiente lote
                DB::beginTransaction();
            }
        });

    return redirect()->route('templates.index')->with('success', 'Prompts generados exitosamente.');
}
protected function replaceIdsWithNames(array $combinations, array $elementsByCategory, array $categoriesIdMap)
{
    $combinationsWithNames = [];
    foreach ($combinations as $combination) {
        $newCombination = [];
        foreach ($combination as $category => $elementId) {
            $categoryParts = explode('.', $category);
            $mainCategory = $categoryParts[0];
            $subCategory = isset($categoryParts[1]) ? $categoryParts[1] : null;

            if ($subCategory) {
                // Si es una subcategoría, buscar en el subarray correspondiente
                $mainElementId = $elementsByCategory[$category][$combination[$mainCategory]][$elementId];
                // dd($elementsByCategory);
                // dd($category);
                // dd($elementsByCategory[$category]);

                // dd($mainCategory);
                // dd($combination);
                // dd($combination[$mainCategory]);

                // dd($elementsByCategory[$category][$combination[$mainCategory]]);
                // dd($elementId);
                // dd($elementsByCategory[$category][$combination[$mainCategory]][$elementId]);

                $newCombination[$category] = $mainElementId;
            } else {
                // Si es una categoría principal, buscar directamente
                $newCombination[$category] = $elementsByCategory[$category][$elementId];
            }
        }
        $combinationsWithNames[] = $newCombination;
    }

    // Reemplazar las claves de las combinaciones con los valores de $categoriesIdMap
    $finalCombinationsWithNames = [];
    foreach ($combinationsWithNames as $combination) {
        $newCombination = [];

        foreach ($combination as $key => $value) {
            if (isset($categoriesIdMap[$key])) {
                $newCombination[$categoriesIdMap[$key]] = $value;
            } else {
                $newCombination[$key] = $value;
            }
        }

        $finalCombinationsWithNames[] = $newCombination;
    }
    return $finalCombinationsWithNames;
}
private function replaceCategoriesWithIds(array $categories): array
{
    $categoryIds = [];
    $userId = auth()->id(); // Obtener el ID del usuario autenticado
    $idToCategoryMap = [];

    foreach ($categories as $category) {
        $parts = explode('.', $category);
        $ids = [];

        foreach ($parts as $part) {
            // Buscar la categoría con el mismo user_id
            $categoryModel = Category::where('name', $part)
                                     ->where('user_id', $userId)
                                     ->first();

            if ($categoryModel) {
                // Almacenar el ID de la categoría
                $ids[] = $categoryModel->id;
                // Almacenar el mapeo id -> category
                $idToCategoryMap[$categoryModel->id] = $part;
            } else {
                // Si no se encuentra la categoría, puedes manejarlo como prefieras
                $ids[] = null;
            }
        }

        // Unir los IDs con puntos
        $categoryIds[$category] = implode('.', $ids);
    }

    // Reemplazar los nombres por los IDs en el array original
    $resultIds = array_map(function($category) use ($categoryIds) {
        return $categoryIds[$category];
    }, $categories);

    // Crear el diccionario de id -> category
    $resultMap = [];
    foreach ($categoryIds as $category => $id) {
        $resultMap[$id] = $category;
    }

    return [
        'ids' => $resultIds,
        'map' => $resultMap,
    ];
}
protected function generateCombinations(array $elementsByCategory)
{
    // Paso 1: Generar combinaciones para las categorías principales (sin puntos)
    $mainCombinations = [[]];
    foreach ($elementsByCategory as $category => $elements) {
        $categoryParts = explode('.', $category);
        // Si la categoría no tiene punto, es una categoría principal
        if (count($categoryParts) == 1) {
            $newCombinations = [];
            foreach ($mainCombinations as $combination) {
                foreach ($elements as $elementId => $elementName) {
                    $newCombination = $combination;
                    $newCombination[$category] = $elementId;
                    $newCombinations[] = $newCombination;
                }
            }
            $mainCombinations = $newCombinations;
        }
    }
    // Paso 2: Generar combinaciones para las subcategorías (con puntos)
    foreach ($elementsByCategory as $category => $elements) {
        $categoryParts = explode('.', $category);
        // Si la categoría tiene punto, es una subcategoría
        if (count($categoryParts) > 1) {
            $newCombinations = [];
            //Para cada combinación original sin elementos padres,
            foreach ($mainCombinations as $combination) {
                //Miro cada categoría con elemento padre
                foreach ($elements as $mainElementId => $subElements) {
                    //Miro cada elemento con elemento padre
                    foreach ($subElements as $subElementId => $subElementName) {
                        //Si la categoría padre está en la frase
                        if($combination[$categoryParts[0]] == $mainElementId){
                            //Guardo el elemento en una combinación nueva
                            $newCombination = $combination;
                            $newCombination[$category] = $subElementId;
                            $newCombinations[] = $newCombination;
                        }
                    }
                }
            }
            $mainCombinations = $newCombinations;
        }
    }
    return $mainCombinations;
}
    public function executePrompts(Request $request)
    {
        $templateId = $request->input('template_id');
        $prompts = Prompt::where('template_id', $templateId)->get();

        foreach ($prompts as $prompt) {
            // Crear un registro en la tabla llm_responses con el estado "pending"
            $llmResponse = LlmResponse::create([
                'prompt_id' => $prompt->id,
                'status' => 'pending',
            ]);

            // Encolar el job
            GenerateLlmResponseJob::dispatch($prompt);
        }

        return redirect()->route('templates.index')->with('success', 'Prompts encolados para ejecución.');
    }
}