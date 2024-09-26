<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LlmResponse extends Model
{
    protected $fillable = [
        'generation_id',
        'response',
        'source',
    ];

    public function generation()
    {
        return $this->belongsTo(Generation::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}