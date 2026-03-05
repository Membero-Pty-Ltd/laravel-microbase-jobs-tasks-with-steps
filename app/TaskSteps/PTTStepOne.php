<?php

namespace App\TaskSteps;

use App\Contracts\TaskStep;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;

class PTTStepOne implements TaskStep
{
    public function handle(Task $task): Task
    {
        $files = Storage::disk('public')->allFiles();

        $result = $task->result ?? [];
        $result['ptt_step_one'] = [
            'disk' => 'public',
            'count' => count($files),
            // keep it reasonable for JSON payloads
            'files_sample' => array_slice($files, 0, 200),
        ];

        $task->result = $result;
        $task->save();

        return $task;
    }
}
