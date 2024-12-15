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
        'has_sheets',
        'has_email',
        'has_form',
        'has_dor_date'
    ];

    protected $casts = [
        'has_batch' => 'boolean',
        'has_claim' => 'boolean',
        'has_time_range' => 'boolean',
        'has_sheets' => 'boolean',
        'has_email' => 'boolean',
        'has_form' => 'boolean',
        'has_dor_date' => 'boolean'
    ];
}
