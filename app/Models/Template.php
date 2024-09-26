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
}