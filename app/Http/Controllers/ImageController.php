<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\LlmResponse;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = Image::all();
        return view('images.index', compact('images'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $llmResponses = LlmResponse::all();
        return view('images.create', compact('llmResponses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'llm_response_id' => 'required|exists:llm_responses,id',
            'image_path' => 'required|string',
        ]);

        Image::create($request->all());

        return redirect()->route('images.index')->with('success', 'Image created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        return view('images.show', compact('image'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        $llmResponses = LlmResponse::all();
        return view('images.edit', compact('image', 'llmResponses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        $request->validate([
            'llm_response_id' => 'required|exists:llm_responses,id',
            'image_path' => 'required|string',
        ]);

        $image->update($request->all());

        return redirect()->route('images.index')->with('success', 'Image updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        $image->delete();

        return redirect()->route('images.index')->with('success', 'Image deleted successfully.');
    }
}