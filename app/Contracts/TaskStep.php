<?php

namespace App\Contracts;

use App\Models\Task;

interface TaskStep
{
    /**
     * Execute a single step.
     *
     * Implementations should update the $task (progress/result/etc.), persist if needed,
     * and return the (possibly refreshed) Task instance.
     */
    public function handle(Task $task): Task;
}
