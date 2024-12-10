<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskCategory extends Model
{
    protected $fillable = ['value', 'label', 'details', 'fields'];

    protected $casts = [
        'fields' => 'array'
    ];

    // public function tasks()
    // {
    //     return $this->hasMany(Task::class,);
    // }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'category', 'value');
    }
}
