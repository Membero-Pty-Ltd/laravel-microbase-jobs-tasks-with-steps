<?php

namespace Database\Seeders;

use App\Models\TaskType;
use App\TaskSteps\PTTStepOne;
use App\TaskSteps\PTTStepThree;
use App\TaskSteps\PTTStepTwo;
use Illuminate\Database\Seeder;

class PilotTaskTestSeeder extends Seeder
{
    public function run(): void
    {
        TaskType::query()->updateOrCreate(
            ['code' => 'pilot-task-test'],
            [
                'description' => 'Pilot task test (3 steps: public files, logs files, du -hs storage)',
                'default_retries' => 3,
                'default_queue' => 'default',
                'steps' => [
                    PTTStepOne::class,
                    PTTStepTwo::class,
                    PTTStepThree::class,
                ],
                // schema-like hints; keep it empty for pilot
                'payload' => (object) [],
                'is_enabled' => true,
            ]
        );
    }
}
