<?php

namespace Modules\Comment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Comment\Entities\Comment;
use Modules\Task\Entities\Task;
use Modules\User\Entities\User;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'content' => fake()->paragraph(),
            'task_id' => Task::factory(),
            'author_id' => User::factory(),
        ];
    }
}
