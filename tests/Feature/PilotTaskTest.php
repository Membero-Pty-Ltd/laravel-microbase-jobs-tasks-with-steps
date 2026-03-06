<?php

namespace Tests\Feature;

use App\Models\Access;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PilotTaskTest extends TestCase
{
    use RefreshDatabase;

    private function token(string $role = 'create'): string
    {
        $access = Access::factory()->role($role)->create();

        return $access->createToken('test')->plainTextToken;
    }

    public function test_get_requires_token(): void
    {
        $this->getJson('/api/pilot-task-test?hash=01ARZ3NDEKTSV4RRFFQ69G5FAV')
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_get_validates_hash(): void
    {
        $token = $this->token();

        $this->withToken($token)
            ->getJson('/api/pilot-task-test?hash=too-short')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['hash']);
    }

    public function test_pilot_task_can_be_created_and_processed(): void
    {
        // Seed the pilot task type (task_types row)
        Artisan::call('db:seed', ['--class' => 'Database\Seeders\PilotTaskTestSeeder']);

        $token = $this->token();

        $resp = $this->withToken($token)->postJson('/api/pilot-task-test', []);
        $resp->assertOk()->assertJson(['ok' => true])->assertJsonStructure(['hash']);
        $hash = $resp->json('hash');

        // Process the queued job (database queue)
        Artisan::call('queue:work', [
            '--once' => true,
            '--stop-when-empty' => true,
            '--sleep' => 0,
            '--tries' => 1,
        ]);

        $get = $this->withToken($token)->getJson('/api/pilot-task-test?hash='.$hash);
        $get->assertOk()
            ->assertJson(['ok' => true])
            ->assertJsonPath('task.hash', $hash)
            ->assertJsonPath('task.status', 'success');

        $task = $get->json('task');
        $this->assertNotEmpty($task['result'] ?? null);
        $this->assertArrayHasKey('ptt_step_one', $task['result']);
        $this->assertArrayHasKey('ptt_step_two', $task['result']);
        $this->assertArrayHasKey('ptt_step_three', $task['result']);
    }
}
