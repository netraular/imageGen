<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Concept;
use App\Models\Combination;
use App\Jobs\GenerateLlmResponseJob;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Comando para generar combinaciones
Artisan::command('generate:combinations', function () {
    $concepts = Concept::all();
    foreach ($concepts as $concept) {
        // Lógica para generar combinaciones
        $combination = new Combination();
        $combination->description = "Generated combination for concept: " . $concept->name;
        $combination->save();
    }

    $this->info('Combinations generated successfully.');
})->purpose('Generate combinations from concepts')->daily();

// Comando para generar respuestas
Artisan::command('generate:responses', function () {
    $combinations = Combination::where('is_generated', false)->get();
    foreach ($combinations as $combination) {
        GenerateLlmResponseJob::dispatch($combination);
    }

    $this->info('Responses generation jobs dispatched.');
})->purpose('Generate responses for ungenerated combinations')->everyMinute();

Artisan::command('schedule:run', function (Schedule $schedule) {
    // Generar combinaciones diariamente
    $schedule->command('generate:combinations')->daily();

    // Generar respuestas para combinaciones no generadas cada minuto
    $schedule->command('generate:responses')->everyMinute();
})->purpose('Run the scheduler');