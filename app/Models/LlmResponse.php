<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LlmResponse extends Model
{
    protected $fillable = [
        'prompt_id',
        'response',
        'source',
        'status',
        'execution_id', // Agregar execution_id a la propiedad $fillable
    ];

    public function prompt()
    {
        return $this->belongsTo(Prompt::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}