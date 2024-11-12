<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LlmResponse;
use App\Models\Prompt;
use App\Jobs\GenerateLlmResponseBatchJob;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

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
        $prompts = Prompt::all();
        return view('llm_responses.create', compact('prompts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'prompt_id' => 'required|exists:prompts,id',
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
        $prompts = Prompt::all();
        return view('llm_responses.edit', compact('llmResponse', 'prompts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LlmResponse $llmResponse)
    {
        $request->validate([
            'prompt_id' => 'required|exists:prompts,id',
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

    public function regenerate(LlmResponse $llmResponse)
    {
        // Log cuando se inicia la regeneración
        Log::channel('llmApi')->info('Regeneración iniciada para LLM Response ID: ' . $llmResponse->id);

        // Actualizar el estado a "pending"
        $llmResponse->update([
            'status' => 'pending',
        ]);

        // Encolar el job
        GenerateLlmResponseBatchJob::dispatch($llmResponse->prompt);

        return redirect()->route('llm_responses.index')->with('success', 'LLM Response regenerated successfully.');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLlmResponses(Request $request)
    {
        $llmResponses = LlmResponse::with('prompt'); // Eager loading para evitar N+1

        return DataTables::of($llmResponses)
            ->addIndexColumn()
            ->addColumn('prompt_sentence', function($llmResponse) {
                return $llmResponse->prompt->sentence;
            })
            ->addColumn('actions', function($llmResponse) {
                return '<a href="'.route('llm_responses.edit', $llmResponse->id).'" class="btn btn-sm btn-warning">Editar</a> '.
                    '<form action="'.route('llm_responses.destroy', $llmResponse->id).'" method="POST" style="display:inline;" onsubmit="return confirmDelete();">'.
                    csrf_field().
                    method_field('DELETE').
                    '<button type="submit" class="btn btn-sm btn-danger">Eliminar</button></form> '.
                    '<form action="'.route('llm_responses.regenerate', $llmResponse->id).'" method="POST" style="display:inline;">'.
                    csrf_field().
                    '<button type="submit" class="btn btn-sm btn-primary">Regenerar</button></form>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getLlmResponsesByPrompt(Request $request)
    {
        $promptId = $request->input('prompt_id');
        $llmResponses = LlmResponse::where('prompt_id', $promptId)->get();

        return response()->json(['data' => $llmResponses]);
    }
}