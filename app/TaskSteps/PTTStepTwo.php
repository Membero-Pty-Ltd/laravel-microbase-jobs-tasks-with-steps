<?php

namespace App\TaskSteps;

use App\Contracts\TaskStep;
use App\Models\Task;
use Illuminate\Support\Facades\File;

class PTTStepTwo implements TaskStep
{
    public function handle(Task $task): Task
    {
        $dir = storage_path('logs');
        $files = File::exists($dir)
            ? collect(File::allFiles($dir))->map(fn ($f) => $f->getRealPath())->values()->all()
            : [];

        $result = $task->result ?? [];
        $result['ptt_step_two'] = [
            'path' => $dir,
            'count' => count($files),
            'files_sample' => array_slice($files, 0, 200),
        ];

        $task->result = array_merge($task->result ?? [], $result);
        $task->save();

        return $task;
    }
}
