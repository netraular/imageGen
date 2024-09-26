<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LlmResponse;
use App\Models\Generation;

class LlmResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $llmResponses = LlmResponse::all();
        return view('llm_responses.index', compact('llmResponses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $generations = Generation::all();
        return view('llm_responses.create', compact('generations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'generation_id' => 'required|exists:generations,id',
            'response' => 'required|string',
            'source' => 'required|string',
        ]);

        LlmResponse::create($request->all());

        return redirect()->route('llm_responses.index')->with('success', 'LLM Response created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LlmResponse $llmResponse)
    {
        return view('llm_responses.show', compact('llmResponse'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LlmResponse $llmResponse)
    {
        $generations = Generation::all();
        return view('llm_responses.edit', compact('llmResponse', 'generations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LlmResponse $llmResponse)
    {
        $request->validate([
            'generation_id' => 'required|exists:generations,id',
            'response' => 'required|string',
            'source' => 'required|string',
        ]);

        $llmResponse->update($request->all());

        return redirect()->route('llm_responses.index')->with('success', 'LLM Response updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LlmResponse $llmResponse)
    {
        $llmResponse->delete();

        return redirect()->route('llm_responses.index')->with('success', 'LLM Response deleted successfully.');
    }
}