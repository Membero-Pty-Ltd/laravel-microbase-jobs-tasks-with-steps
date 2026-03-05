<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    protected $fillable = [
        'code',
        'description',
        'default_retries',
        'default_queue',
        'steps',
        'payload',
        'is_enabled',
    ];

    protected $casts = [
        'steps' => 'array',
        'payload' => 'array',
        'is_enabled' => 'boolean',
        'default_retries' => 'integer',
    ];
}
