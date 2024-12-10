<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'report_date',
        'total_tasks'
    ];



    protected $casts = [
        'report_date' => 'date'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ReportTask::class);
    }
}
