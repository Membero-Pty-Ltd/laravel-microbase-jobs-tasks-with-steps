<?php

namespace Tests\Feature;

use Tests\TestCase;

class JsonNotFoundTest extends TestCase
{
    public function test_fallback_returns_json_not_found(): void
    {
        $this->getJson('/api/mirror/donkey')
            ->assertStatus(404)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'ok' => false,
                'error' => 'Not Found',
                'path' => 'api/mirror/donkey',
            ]);
    }
}
