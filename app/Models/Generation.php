<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    protected $fillable = [
        'sentence',
        'combination_id',
    ];

    public function combination()
    {
        return $this->belongsTo(Combination::class);
    }

    public function llmResponses()
    {
        return $this->hasMany(LlmResponse::class);
    }
}