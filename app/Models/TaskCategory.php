<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'has_batch',
        'has_claim',
        'has_time_range',
        'has_sheets'
    ];
}
