<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_basic_math_still_works(): void
    {
        $left = 40;
        $right = 2;

        $this->assertSame(42, $left + $right);
    }
}
