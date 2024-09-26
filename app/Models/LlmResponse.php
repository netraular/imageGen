<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LlmResponse extends Model
{
    protected $fillable = [
        'prompt_id',
        'response',
        'source',
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