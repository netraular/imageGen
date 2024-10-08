<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'parent_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function parent()
    {
        return $this->belongsTo(Element::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Element::class, 'parent_id');
    }
}