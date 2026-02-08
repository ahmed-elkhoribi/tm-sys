<?php

namespace Modules\Task\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Task\Entities\Task;

interface TaskRepositoryInterface
{
    /**
     * Get all tasks with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function all(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find task by ID
     *
     * @param int $id
     * @return Task|null
     */
    public function findById(int $id): ?Task;

    /**
     * Create a new task
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task;

    /**
     * Update a task
     *
     * @param int $id
     * @param array $data
     * @return Task
     */
    public function update(int $id, array $data): Task;

    /**
     * Delete a task
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get tasks assigned to a user
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAssignedToUser(int $userId, int $perPage = 15): LengthAwarePaginator;
}
