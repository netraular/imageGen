<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    protected $fillable = [
        'sentence',
        'template_id',
        'status',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function llmResponses()
    {
        return $this->hasMany(LlmResponse::class);
    }
    public static function deletePromptsByTemplateId($templateId)
    {
        self::where('template_id', $templateId)->delete();
    }
}