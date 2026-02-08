<?php

namespace Modules\Task\Services;

use Modules\Task\Entities\Task;
use Modules\Task\Repositories\TaskRepositoryInterface;

class TaskService
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepository
    ) {}

    /**
     * Get all tasks
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllTasks(int $perPage = 15)
    {
        return $this->taskRepository->all($perPage);
    }

    /**
     * Get task by ID
     *
     * @param int $id
     * @return Task
     * @throws \Exception
     */
    public function getTaskById(int $id): Task
    {
        $task = $this->taskRepository->findById($id);
        if (!$task) {
            throw new \Exception('Task not found', 404);
        }
        return $task;
    }

    /**
     * Create a new task
     *
     * @param array $data
     * @param int $authorId
     * @return Task
     */
    public function createTask(array $data, int $authorId): Task
    {
        $data['author_id'] = $authorId;
        return $this->taskRepository->create($data);
    }

    /**
     * Update a task
     *
     * @param int $id
     * @param array $data
     * @param int $userId
     * @return Task
     * @throws \Exception
     */
    public function updateTask(int $id, array $data, int $userId): Task
    {
        $task = $this->taskRepository->findById($id);
        if (!$task) {
            throw new \Exception('Task not found', 404);
        }

        if ($task->author_id !== $userId) {
            throw new \Exception('Unauthorized to update this task', 403);
        }

        return $this->taskRepository->update($id, $data);
    }

    /**
     * Delete a task
     *
     * @param int $id
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
    public function deleteTask(int $id, int $userId): bool
    {
        $task = $this->taskRepository->findById($id);
        if (!$task) {
            throw new \Exception('Task not found', 404);
        }

        if ($task->author_id !== $userId) {
            throw new \Exception('Unauthorized to delete this task', 403);
        }

        return $this->taskRepository->delete($id);
    }

    /**
     * Get tasks assigned to user
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAssignedTasks(int $userId, int $perPage = 15)
    {
        return $this->taskRepository->getAssignedToUser($userId, $perPage);
    }
}
