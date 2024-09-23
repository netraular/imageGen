<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LlmResponse extends Model
{
    protected $table = 'llm_responses';

    protected $fillable = [
        'combination_id',
        'response',
        'source',
    ];

    public function combination()
    {
        return $this->belongsTo(Combination::class);
    }
}