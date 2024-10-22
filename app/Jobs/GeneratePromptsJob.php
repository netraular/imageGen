<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Template;
use App\Models\Element;
use App\Models\Prompt;
use Illuminate\Support\Facades\DB;

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
        $combinations = $this->generateCombinations($this->elementsByCategory);
        // Verificar si $combinations es un array
        $combinationsWithNames = $this->replaceIdsWithNames($combinations, $this->elementsByCategory, $this->categoriesIdMap);

        $batchSize = 1000; // Define el tamaño del lote
        $totalCombinations = count($combinationsWithNames);

        DB::transaction(function () use ($template, $combinationsWithNames, $batchSize, $totalCombinations) {
            for ($i = 0; $i < $totalCombinations; $i += $batchSize) {
                $batch = array_slice($combinationsWithNames, $i, $batchSize);

                foreach ($batch as $combination) {
                    $promptSentence = $this->sentence;
                    foreach ($combination as $category => $element) {
                        $promptSentence = str_replace("{{{$category}}}", $element, $promptSentence);
                    }

                    Prompt::create([
                        'sentence' => $promptSentence,
                        'template_id' => $template->id,
                    ]);
                }

                // Confirmar el lote actual
                DB::commit();

                // Comenzar una nueva transacción para el siguiente lote
                DB::beginTransaction();
            }
        });
    }

    protected function generateCombinations(array $elementsByCategory)
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
                //Para cada combinación original sin elementos padres,
                foreach ($mainCombinations as $combination) {
                    //Miro cada categoría con elemento padre
                    foreach ($elements as $mainElementId => $subElements) {
                        //Miro cada elemento con elemento padre
                        foreach ($subElements as $subElementId => $subElementName) {
                            //Si la categoría padre está en la frase
                            if($combination[$categoryParts[0]] == $mainElementId){
                                //Guardo el elemento en una combinación nueva
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
        return $mainCombinations;
    }

    protected function replaceIdsWithNames(array $combinations, array $elementsByCategory, array $categoriesIdMap)
    {
        $combinationsWithNames = [];
        foreach ($combinations as $combination) {
            $newCombination = [];
            foreach ($combination as $category => $elementId) {
                $categoryParts = explode('.', $category);
                $mainCategory = $categoryParts[0];
                $subCategory = isset($categoryParts[1]) ? $categoryParts[1] : null;
    
                if ($subCategory) {
                    // Si es una subcategoría, buscar en el subarray correspondiente
                    $mainElementId = $elementsByCategory[$category][$combination[$mainCategory]][$elementId];
                    // dd($elementsByCategory);
                    // dd($category);
                    // dd($elementsByCategory[$category]);
    
                    // dd($mainCategory);
                    // dd($combination);
                    // dd($combination[$mainCategory]);
    
                    // dd($elementsByCategory[$category][$combination[$mainCategory]]);
                    // dd($elementId);
                    // dd($elementsByCategory[$category][$combination[$mainCategory]][$elementId]);
    
                    $newCombination[$category] = $mainElementId;
                } else {
                    // Si es una categoría principal, buscar directamente
                    $newCombination[$category] = $elementsByCategory[$category][$elementId];
                }
            }
            $combinationsWithNames[] = $newCombination;
        }
    
        // Reemplazar las claves de las combinaciones con los valores de $categoriesIdMap
        $finalCombinationsWithNames = [];
        foreach ($combinationsWithNames as $combination) {
            $newCombination = [];
    
            foreach ($combination as $key => $value) {
                if (isset($categoriesIdMap[$key])) {
                    $newCombination[$categoriesIdMap[$key]] = $value;
                } else {
                    $newCombination[$key] = $value;
                }
            }
    
            $finalCombinationsWithNames[] = $newCombination;
        }
        return $finalCombinationsWithNames;
    }
}