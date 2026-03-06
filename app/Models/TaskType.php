<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'description',
        'default_retries',
        'default_queue',
        'steps',
        'payload',
        'is_enabled',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'steps' => 'array',
        'payload' => 'array',
        'is_enabled' => 'boolean',
        'default_retries' => 'integer',
    ];
}
