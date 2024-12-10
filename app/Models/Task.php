<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['category', 'batch', 'claim', 'email', 'reports_id'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function category()
    {
        return $this->belongsTo(taskCategory::class, 'task_categories_id', 'value');
    }

}
