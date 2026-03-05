<?php

namespace Tests\Feature;

use App\Models\Access;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessRoleHelloTest extends TestCase
{
    use RefreshDatabase;

    private function tokenForRole(string $role): string
    {
        $access = Access::factory()->role($role)->create();
        return $access->createToken('test')->plainTextToken;
    }

    public function test_create_hello_requires_token(): void
    {
        $this->getJson('/api/create/hello')
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_create_hello_with_create_role_returns_role(): void
    {
        $token = $this->tokenForRole('create');

        $this->withToken($token)
            ->getJson('/api/create/hello')
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'message' => 'hello',
                'role' => 'create',
            ]);
    }

    public function test_create_hello_with_mirror_role_returns_403_json(): void
    {
        $token = $this->tokenForRole('mirror');

        $this->withToken($token)
            ->getJson('/api/create/hello')
            ->assertStatus(403)
            ->assertJson([
                'ok' => false,
                'error' => 'Wrong access role',
            ]);
    }

    public function test_create_mirror_hello_with_create_mirror_role_returns_role(): void
    {
        $token = $this->tokenForRole('create-mirror');

        $this->withToken($token)
            ->getJson('/api/create-mirror/hello')
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'message' => 'hello',
                'role' => 'create-mirror',
            ]);
    }

    public function test_mirror_hello_with_mirror_role_returns_role(): void
    {
        $token = $this->tokenForRole('mirror');

        $this->withToken($token)
            ->getJson('/api/mirror/hello')
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'message' => 'hello',
                'role' => 'mirror',
            ]);
    }
}
