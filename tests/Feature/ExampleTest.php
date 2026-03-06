<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_public_hello_returns_successful_response(): void
    {
        $response = $this->get('/api/hello');

        $response->assertStatus(200);
    }
}
