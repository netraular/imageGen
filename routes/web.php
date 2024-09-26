<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\LlmResponseController;
use App\Http\Controllers\ImageController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('categories', CategoryController::class);
Route::resource('elements', ElementController::class);
Route::resource('templates', TemplateController::class);
Route::resource('prompts', PromptController::class);
Route::resource('llm_responses', LlmResponseController::class);
Route::resource('images', ImageController::class);

// Rutas específicas para generación de combinaciones y respuestas
Route::post('templates/generate', [TemplateController::class, 'generatePrompts'])->name('templates.generate');
Route::post('prompts/generate', [PromptController::class, 'generateLlmResponses'])->name('prompts.generate');