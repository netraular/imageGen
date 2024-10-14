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

        $user->update($request->except('email'));
        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}