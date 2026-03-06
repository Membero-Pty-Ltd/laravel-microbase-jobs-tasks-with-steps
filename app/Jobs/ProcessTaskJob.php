<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\TaskStep;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class ProcessTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $taskId) {}

    public function handle(): void
    {
        /** @var Task $task */
        $task = Task::query()->with('taskType')->findOrFail($this->taskId);

        if ($task->status === 'canceled') {
            return;
        }

        $task->status = 'running';
        $task->started_at ??= now();
        $task->save();

        /** @var array<int, class-string> $steps */
        $steps = Arr::wrap($task->taskType->steps ?? []);
        $total = max(count($steps), 1);

        try {
            foreach ($steps as $index => $stepClass) {
                $task->refresh();

                if ($task->status === 'canceled') {
                    return;
                }

                $task->step = $stepClass;
                $task->progress = (int) floor(($index / $total) * 100);
                $task->save();

                $step = app($stepClass);

                if (! ($step instanceof TaskStep)) {
                    throw new RuntimeException("Task step [{$stepClass}] must implement ".TaskStep::class);
                }

                $task = $step->handle($task);
            }

            $task->refresh();
            $task->progress = 100;
            $task->status = 'success';
            $task->finished_at = now();
            $task->step = null;
            $task->save();
        } catch (Throwable $e) {
            Log::error('Task failed', [
                'task_id' => $task->id,
                'hash' => $task->hash,
                'exception' => $e,
            ]);

            $task->refresh();
            $task->status = 'failed';
            $task->finished_at = now();
            $task->error = [
                'message' => $e->getMessage(),
                'class' => $e::class,
            ];
            $task->save();

            throw $e;
        }
    }
}
