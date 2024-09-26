<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combination extends Model
{
    protected $fillable = [
        'sentence',
    ];

    public function generations()
    {
        return $this->hasMany(Generation::class);
    }
}