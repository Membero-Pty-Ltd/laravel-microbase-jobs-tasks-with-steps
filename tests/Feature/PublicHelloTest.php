<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicHelloTest extends TestCase
{
    public function test_public_hello_returns_expected_json(): void
    {
        $this->getJson('/api/hello')
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'message' => 'hello',
            ]);
    }
}
