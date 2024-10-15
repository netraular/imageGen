<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'order',
    ];

    public function elements()
    {
        return $this->hasMany(Element::class);
    }
}