<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportTask extends Model
{
    protected $fillable = [
        'report_id',
        'category_id',
        'description',
        'quantity',
        'unit',
        'start_time',
        'end_time'
    ];
}
