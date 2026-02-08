<?php

namespace Modules\Task\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Task\Entities\Task;
use Modules\User\Entities\User;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_user_can_create_task(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/tasks', [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'status' => 'pending',
                'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'title', 'description', 'status'],
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'author_id' => $this->user->id,
        ]);
    }

    public function test_user_can_list_tasks(): void
    {
        Task::factory()->count(3)->create(['author_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/v1/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'status'],
                ],
            ]);
    }

    public function test_user_can_update_own_task(): void
    {
        $task = Task::factory()->create(['author_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/v1/tasks/{$task->id}", [
                'title' => 'Updated Title',
                'status' => 'in-progress',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'in-progress',
        ]);
    }

    public function test_user_cannot_update_other_user_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['author_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/v1/tasks/{$task->id}", [
                'title' => 'Updated Title',
            ]);

        $response->assertStatus(403);
    }
}
