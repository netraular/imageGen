<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
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
        return $this->belongsTo(Value::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Value::class, 'parent_id');
    }
}