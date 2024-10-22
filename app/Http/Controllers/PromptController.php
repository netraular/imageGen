<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prompt;
use App\Models\Template;
use Yajra\DataTables\Facades\DataTables;

class PromptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('prompts.index');
    }

    public function getPrompts(Request $request)
    {
        $prompts = Prompt::with('template'); // Eager loading para evitar N+1

        return DataTables::of($prompts)
            ->addIndexColumn()
            ->addColumn('template_sentence', function($prompt) {
                return $prompt->template->sentence;
            })
            ->addColumn('actions', function($prompt) {
                return '<a href="'.route('prompts.edit', $prompt->id).'" class="btn btn-sm btn-warning">Editar</a> '.
                       '<form action="'.route('prompts.destroy', $prompt->id).'" method="POST" style="display:inline;" onsubmit="return confirmDelete();">'.
                       csrf_field().
                       method_field('DELETE').
                       '<button type="submit" class="btn btn-sm btn-danger">Eliminar</button></form>';
            })
            ->rawColumns(['actions'])
            ->make(true);
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

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $prompts = Prompt::with('template');

        return DataTables::of($prompts)
            ->addColumn('actions', function ($prompt) {
                return '<a href="' . route('prompts.edit', $prompt->id) . '" class="btn btn-sm btn-warning">Editar</a> ' .
                       '<form action="' . route('prompts.destroy', $prompt->id) . '" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

}