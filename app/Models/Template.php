<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'sentence',
    ];

    public function prompts()
    {
        return $this->hasMany(Prompt::class);
    }

    public function getPromptsCount()
    {
        $promptsCount = Prompt::where('template_id', $this->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $totalPrompts = array_sum($promptsCount->toArray());
        $successPrompts = $promptsCount['success'] ?? 0;
        $errorPrompts = $promptsCount['error'] ?? 0;
        $otherPrompts = $totalPrompts - $successPrompts - $errorPrompts;

        return [
            'total' => $totalPrompts,
            'success' => $successPrompts,
            'error' => $errorPrompts,
            'other' => $otherPrompts,
        ];
    }
}