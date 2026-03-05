<?php

namespace Database\Factories;

use App\Models\Access;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Access>
 */
class AccessFactory extends Factory
{
    protected $model = Access::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->unique()->sentence(3),
            'role' => 'create',
        ];
    }

    public function role(string $role): self
    {
        return $this->state(fn () => ['role' => $role]);
    }
}
