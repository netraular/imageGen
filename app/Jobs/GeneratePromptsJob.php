<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Template;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Bus;
use App\Jobs\GeneratePromptsBatchJob;

class GeneratePromptsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $templateId;
    protected $elementsByCategory;
    protected $categoriesIdMap;
    protected $sentence;

    public function __construct($templateId, $elementsByCategory, $categoriesIdMap, $sentence)
    {
        $this->templateId = $templateId;
        $this->elementsByCategory = $elementsByCategory;
        $this->categoriesIdMap = $categoriesIdMap;
        $this->sentence = $sentence;
    }

    public function handle()
    {
        $template = Template::findOrFail($this->templateId);
        $batches = $this->generateCombinations($this->elementsByCategory);

        $jobs = [];
        foreach ($batches as $batch) {
            $jobs[] = new GeneratePromptsBatchJob($this->templateId, $batch, $this->categoriesIdMap, $this->sentence, $this->elementsByCategory);
        }

        // Se crea el batch sin necesidad de llamar a withBatchId()
        Bus::batch($jobs)
            ->name('Generate Prompts')
            ->dispatch();
    }

    protected function generateCombinations(array $elementsByCategory, $batchSize = 1000)
    {
        // Paso 1: Generar combinaciones para las categorías principales (sin puntos)
        $mainCombinations = [[]];
        foreach ($elementsByCategory as $category => $elements) {
            $categoryParts = explode('.', $category);
            // Si la categoría no tiene punto, es una categoría principal
            if (count($categoryParts) == 1) {
                $newCombinations = [];
                foreach ($mainCombinations as $combination) {
                    foreach ($elements as $elementId => $elementName) {
                        $newCombination = $combination;
                        $newCombination[$category] = $elementId;
                        $newCombinations[] = $newCombination;
                    }
                }
                $mainCombinations = $newCombinations;
            }
        }

        // Paso 2: Generar combinaciones para las subcategorías (con puntos)
        foreach ($elementsByCategory as $category => $elements) {
            $categoryParts = explode('.', $category);
            // Si la categoría tiene punto, es una subcategoría
            if (count($categoryParts) > 1) {
                $newCombinations = [];
                // Para cada combinación original sin elementos padres,
                foreach ($mainCombinations as $combination) {
                    // Miro cada categoría con elemento padre
                    foreach ($elements as $mainElementId => $subElements) {
                        // Miro cada elemento con elemento padre
                        foreach ($subElements as $subElementId => $subElementName) {
                            // Si la categoría padre está en la frase
                            if ($combination[$categoryParts[0]] == $mainElementId) {
                                // Guardo el elemento en una combinación nueva
                                $newCombination = $combination;
                                $newCombination[$category] = $subElementId;
                                $newCombinations[] = $newCombination;
                            }
                        }
                    }
                }
                $mainCombinations = $newCombinations;
            }
        }

        // Dividir las combinaciones en lotes
        $batches = [];
        $totalCombinations = count($mainCombinations);
        for ($i = 0; $i < $totalCombinations; $i += $batchSize) {
            $batches[] = array_slice($mainCombinations, $i, $batchSize);
        }

        return $batches;
    }
}