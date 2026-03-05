<?php

namespace App\TaskSteps;

use App\Contracts\TaskStep;
use App\Models\Task;
use Illuminate\Support\Facades\Process;

class PTTStepThree implements TaskStep
{
    public function handle(Task $task): Task
    {
        $path = storage_path();
        $process = Process::run(['du', '-hs', $path]);

        $result = $task->result ?? [];
        $result['ptt_step_three'] = [
            'cmd' => 'du -hs ' . $path,
            'successful' => $process->successful(),
            'exit_code' => $process->exitCode(),
            'output' => trim($process->output()),
            'error_output' => trim($process->errorOutput()),
        ];

        $task->result = array_merge($task->result ?? [], $result);
        $task->save();

        return $task;
    }
}
