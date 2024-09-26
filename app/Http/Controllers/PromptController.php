<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prompt;
use App\Models\Template;

class PromptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prompts = Prompt::all();
        return view('prompts.index', compact('prompts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templates = Template::all();
        return view('prompts.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sentence' => 'required|string',
            'template_id' => 'required|exists:templates,id',
        ]);

        Prompt::create($request->all());

        return redirect()->route('prompts.index')->with('success', 'Prompt created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Prompt $prompt)
    {
        return view('prompts.show', compact('prompt'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prompt $prompt)
    {
        $templates = Template::all();
        return view('prompts.edit', compact('prompt', 'templates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prompt $prompt)
    {
        $request->validate([
            'sentence' => 'required|string',
            'template_id' => 'required|exists:templates,id',
        ]);

        $prompt->update($request->all());

        return redirect()->route('prompts.index')->with('success', 'Prompt updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prompt $prompt)
    {
        $prompt->delete();

        return redirect()->route('prompts.index')->with('success', 'Prompt deleted successfully.');
    }
}