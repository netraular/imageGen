<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;
use App\Models\Template;
use App\Models\Prompt;
use Illuminate\Support\Facades\DB;

class GeneratePromptsBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $templateId;
    protected $combinations;
    protected $categoriesIdMap;
    protected $sentence;
    protected $elementsByCategory;

    public function __construct($templateId, $combinations, $categoriesIdMap, $sentence, $elementsByCategory)
    {
        $this->templateId = $templateId;
        $this->combinations = $combinations;
        $this->categoriesIdMap = $categoriesIdMap;
        $this->sentence = $sentence;
        $this->elementsByCategory = $elementsByCategory;
    }

    public function handle()
    {
        $template = Template::findOrFail($this->templateId);
        $combinationsWithNames = $this->replaceIdsWithNames($this->combinations, $this->categoriesIdMap);

        DB::transaction(function () use ($template, $combinationsWithNames) {
            foreach ($combinationsWithNames as $combination) {
                $promptSentence = $this->sentence;
                foreach ($combination as $category => $element) {
                    $promptSentence = str_replace("{{{$category}}}", $element, $promptSentence);
                }

                Prompt::create([
                    'sentence' => $promptSentence,
                    'template_id' => $template->id,
                ]);
            }
        });
    }

    protected function replaceIdsWithNames(array $combinations, array $categoriesIdMap)
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
                    $mainElementId = $this->elementsByCategory[$category][$combination[$mainCategory]][$elementId];
                    $newCombination[$category] = $mainElementId;
                } else {
                    // Si es una categoría principal, buscar directamente
                    $newCombination[$category] = $this->elementsByCategory[$category][$elementId];
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