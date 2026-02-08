<?php

namespace Modules\Comment\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Comment\Entities\Comment;
use Modules\Task\Entities\Task;
use Modules\User\Entities\User;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        $this->task = Task::factory()->create(['author_id' => $this->user->id]);
    }

    public function test_user_can_create_comment(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/v1/tasks/{$this->task->id}/comments", [
                'content' => 'This is a test comment',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'content', 'author'],
            ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment',
            'task_id' => $this->task->id,
            'author_id' => $this->user->id,
        ]);
    }

    public function test_user_can_list_comments(): void
    {
        Comment::factory()->count(3)->create([
            'task_id' => $this->task->id,
            'author_id' => $this->user->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/v1/tasks/{$this->task->id}/comments");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'content', 'author'],
                ],
            ]);
    }
}
