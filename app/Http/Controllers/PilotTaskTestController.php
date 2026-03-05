<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTaskJob;
use App\Models\Task;
use App\Models\TaskType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PilotTaskTestController extends Controller
{
    public function create(Request $request)
    {
        /** @var \App\Models\Access $access */
        $access = $request->user();

        $taskType = TaskType::query()
            ->where('code', 'pilot-task-test')
            ->where('is_enabled', true)
            ->firstOrFail();

        $role = in_array($access->role, ['mirror'], true) ? 'mirror' : 'create';

        $task = Task::query()->create([
            'task_type_id' => $taskType->id,
            'access_id' => $access->id,
            'role' => $role,
            'status' => 'queued',
            'progress' => 0,
            'payload' => (object) [],
            'result' => null,
            'error' => null,
        ]);

        ProcessTaskJob::dispatch($task->id)->onQueue($taskType->default_queue);

        return response()->json([
            'ok' => true,
            'hash' => $task->hash,
        ]);
    }

    public function show(Request $request)
    {
        $data = $request->validate([
            'hash' => ['required', 'string', 'size:26'],
        ]);

        $task = Task::query()
            ->with('taskType')
            ->where('hash', $data['hash'])
            ->firstOrFail();

        return response()->json([
            'ok' => true,
            'task' => $task,
        ]);
    }
}
