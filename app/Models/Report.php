<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['name', 'date'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
