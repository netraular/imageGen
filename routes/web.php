<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConceptController;
use App\Http\Controllers\CombinationController;
use App\Http\Controllers\ResponseController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('concepts', ConceptController::class);
Route::resource('combinations', CombinationController::class);
Route::resource('responses', ResponseController::class);

// Rutas específicas para generación de combinaciones y respuestas
Route::post('combinations/generate', [ConceptController::class, 'generateCombinations'])->name('combinations.generate');
Route::post('responses/generate/{combination}', [CombinationController::class, 'generateResponses'])->name('responses.generate');

