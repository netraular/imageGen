<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'llm_response_id',
        'image_path',
    ];

    public function llmResponse()
    {
        return $this->belongsTo(LlmResponse::class);
    }
}