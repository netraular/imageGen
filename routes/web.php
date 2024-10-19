<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\LlmResponseController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProfileController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('elements', ElementController::class);
    Route::post('elements/bulkDelete', [ElementController::class, 'bulkDelete'])->name('elements.bulkDelete');
    Route::get('/elements/parent-elements/{categoryId}', [ElementController::class, 'getParentElementsByCategory']);
    Route::resource('templates', TemplateController::class);
    Route::resource('prompts', PromptController::class);
    Route::resource('llm_responses', LlmResponseController::class);
    Route::post('llm_responses/{llmResponse}/regenerate', [LlmResponseController::class, 'regenerate'])->name('llm_responses.regenerate');
    Route::resource('images', ImageController::class);

    // Specific routes for generation and execution
    Route::post('templates/generate', [TemplateController::class, 'generatePrompts'])->name('templates.generate');
    Route::post('prompts/generate', [PromptController::class, 'generateLlmResponses'])->name('prompts.generate');
    Route::post('templates/executePrompts', [TemplateController::class, 'executePrompts'])->name('templates.executePrompts');

    // Profile routes
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});