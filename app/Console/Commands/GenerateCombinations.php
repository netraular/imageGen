<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Concept;
use App\Models\Combination;

class GenerateCombinations extends Command
{
    protected $signature = 'generate:combinations';
    protected $description = 'Generate combinations from concepts';

    public function handle()
    {
        $concepts = Concept::all();
        foreach ($concepts as $concept) {
            // Lógica para generar combinaciones
            $combination = new Combination();
            $combination->description = "Generated combination for concept: " . $concept->name;
            $combination->save();
        }

        $this->info('Combinations generated successfully.');
    }
}
