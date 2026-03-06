<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Task extends Model
{
    /**
     * @var list<string>
     */
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

    /**
     * @var array<string, string>
     */
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
        static::creating(static function (self $task): void {
            if ($task->hash === null || $task->hash === '') {
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
