<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'profile_photo' => 'nullable|string',
            'llm_api_key' => 'nullable|string',
            'llm_service_name' => 'nullable|string',
            'comfyui_url' => 'nullable|url',
        ]);

        $data = $request->except('email', 'llm_api_key', 'comfyui_url');

        if ($request->filled('llm_api_key')) {
            $data['llm_api_key'] = $request->llm_api_key;
        }

        if ($request->filled('comfyui_url')) {
            $data['comfyui_url'] = $request->comfyui_url;
        }

        $user->update($data);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}