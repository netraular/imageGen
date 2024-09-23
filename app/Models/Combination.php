<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combination extends Model
{
    protected $fillable = [
        'description',
        'is_generated',
    ];

    public function llmResponses()
    {
        return $this->hasMany(LlmResponse::class);
    }
}