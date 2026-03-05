<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Task extends Model
{
    protected $fillable = [
        'started_at',
        'finished_at',
        'hash',
        'task_type_id',
        'access_id',
        'role',
        'status',
        'step',
        'progress',
        'payload',
        'result',
        'error',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'payload' => 'array',
        'result' => 'array',
        'error' => 'array',
        'progress' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $task) {
            if (empty($task->hash)) {
                // ULID is 26 chars.
                $task->hash = (string) Str::ulid();
            }
        });
    }

    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class);
    }

    public function access(): BelongsTo
    {
        return $this->belongsTo(Access::class);
    }
}
