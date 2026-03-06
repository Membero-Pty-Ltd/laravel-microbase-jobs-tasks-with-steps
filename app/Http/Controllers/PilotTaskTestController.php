<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\ProcessTaskJob;
use App\Models\Access;
use App\Models\Task;
use App\Models\TaskType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PilotTaskTestController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $access = $request->user();

        if (! $access instanceof Access) {
            throw new AccessDeniedHttpException('Authenticated Access token is required.');
        }

        $taskType = TaskType::query()
            ->where('code', 'pilot-task-test')
            ->where('is_enabled', true)
            ->firstOrFail();

        $role = $access->role === 'mirror' ? 'mirror' : 'create';

        $task = Task::query()->create([
            'task_type_id' => $taskType->id,
            'access_id' => $access->id,
            'role' => $role,
            'status' => 'queued',
            'progress' => 0,
            'payload' => [],
            'result' => null,
            'error' => null,
        ]);

        ProcessTaskJob::dispatch($task->id)->onQueue($taskType->default_queue);

        return response()->json([
            'ok' => true,
            'hash' => $task->hash,
        ]);
    }

    public function show(Request $request): JsonResponse
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
