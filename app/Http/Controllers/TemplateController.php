<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Prompt;

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
        $this->generatePrompts($template);

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
    protected function generatePrompts(Template $template)
    {
        // Aquí iría la lógica para generar los prompts basados en la plantilla
        // Por ejemplo, podrías crear varios prompts con la misma frase de la plantilla
        // y asignarlos a la plantilla recién creada.

        // Ejemplo básico:
        Prompt::create([
            'sentence' => $template->sentence,
            'template_id' => $template->id,
        ]);

        // Puedes agregar más lógica aquí según tus necesidades
    }
}