<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Prompt;
use App\Models\Template;
use App\Models\LlmResponse;
use App\Models\Element;
use App\Models\Category;
use App\Jobs\GeneratePromptsJob;
use App\Jobs\ExecutePromptsJob;
use Illuminate\Support\Facades\Log;

use App\Notifications\JobStartedNotification;
use Illuminate\Support\Facades\Auth;

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

        // Eliminar los prompts existentes relacionados con el template
        Prompt::deletePromptsByTemplateId($templateId);

        GeneratePromptsJob::dispatch($templateId, $elementsByCategory, $categoriesIdMap, $sentence);

        // Enviar notificación de job iniciado
        $user = Auth::user();
        $user->notify(new JobStartedNotification('GeneratePromptsJob'));

        return redirect()->route('templates.index')->with('success', 'Prompts generados exitosamente.');
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

    public function executePrompts(Request $request)
    {
        $templateId = $request->input('template_id');
        $batchSize = 10000; // Tamaño del lote
    
        // Generar un nuevo execution_id
        $lastExecutionId = LlmResponse::whereHas('prompt', function ($query) use ($templateId) {
            $query->where('template_id', $templateId);
        })->max('execution_id') ?? 0;
    
        $executionId = $lastExecutionId + 1;
    
        // Log cuando se inicia el proceso de encolado
        Log::channel('llmApi')->info('Iniciando encolado de prompts para Template ID: ' . $templateId . ' con Execution ID: ' . $executionId);
    
        // Despachar el job para ejecutar los prompts en segundo plano
        $user = Auth::user();
        ExecutePromptsJob::dispatch($templateId, $batchSize, $user->id, $executionId);
        Log::channel('llmApi')->info('Job encolado para Template ID: ' . $templateId . ' con Execution ID: ' . $executionId);
    
        // Enviar notificación de job iniciado
        $user->notify(new JobStartedNotification('ExecutePromptsJob'));
    
        // Log cuando se encola el job
        Log::channel('llmApi')->info('Job encolado para Template ID: ' . $templateId . ' con Execution ID: ' . $executionId);
    
        return redirect()->route('templates.index')->with('success', 'Prompts encolados para ejecución.');
    }
}