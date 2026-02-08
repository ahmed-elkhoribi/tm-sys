<?php

namespace Modules\Task\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Task\Entities\Task;
use Modules\User\Entities\User;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'in-progress', 'completed']),
            'due_date' => fake()->dateTimeBetween('now', '+1 year'),
            'author_id' => User::factory(),
            'assignee_id' => null,
        ];
    }
}
