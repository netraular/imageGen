<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ValueController;
use App\Http\Controllers\CombinationController;
use App\Http\Controllers\GenerationController;
use App\Http\Controllers\LlmResponseController;
use App\Http\Controllers\ImageController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('categories', CategoryController::class);
Route::resource('values', ValueController::class);
Route::resource('combinations', CombinationController::class);
Route::resource('generations', GenerationController::class);
Route::resource('llm_responses', LlmResponseController::class);
Route::resource('images', ImageController::class);

// Rutas específicas para generación de combinaciones y respuestas
Route::post('combinations/generate', [CombinationController::class, 'generateResponses'])->name('combinations.generate');
Route::post('generations/generate', [GenerationController::class, 'generateResponses'])->name('generations.generate');