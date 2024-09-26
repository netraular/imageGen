<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Element;
use App\Models\Prompt;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateLlmResponseJob;

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
        $categories = $matches[1];

        // Obtener todos los elementos de cada categoría
        $elementsByCategory = [];
        foreach ($categories as $category) {
            $elementsByCategory[$category] = Element::whereHas('category', function ($query) use ($category) {
                $query->where('name', $category);
            })->pluck('name')->toArray();
        }

        // Generar todas las combinaciones posibles
        $combinations = $this->generateCombinations($elementsByCategory);

        // Crear los prompts con las combinaciones generadas
        DB::transaction(function () use ($template, $sentence, $combinations) {
            foreach ($combinations as $combination) {
                $promptSentence = $sentence;
                foreach ($combination as $category => $element) {
                    $promptSentence = str_replace("{{{$category}}}", $element, $promptSentence);
                }

                Prompt::create([
                    'sentence' => $promptSentence,
                    'template_id' => $template->id,
                ]);
            }
        });

        return redirect()->route('templates.index')->with('success', 'Prompts generados exitosamente.');
    }
    protected function generateCombinations(array $elementsByCategory)
    {
        $combinations = [[]];

        foreach ($elementsByCategory as $category => $elements) {
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($elements as $element) {
                    $newCombinations[] = array_merge($combination, [$category => $element]);
                }
            }
            $combinations = $newCombinations;
        }

        return $combinations;
    }
    public function executePrompts(Request $request)
    {
        $templateId = $request->input('template_id');
        $prompts = Prompt::where('template_id', $templateId)->get();

        foreach ($prompts as $prompt) {
            GenerateLlmResponseJob::dispatch($prompt);
        }

        return redirect()->route('templates.index')->with('success', 'Prompts encolados para ejecución.');
    }
}