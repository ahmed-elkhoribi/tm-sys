<?php

namespace Modules\Comment\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Comment\Entities\Comment;

interface CommentRepositoryInterface
{
    /**
     * Get all comments for a task
     *
     * @param int $taskId
     * @return Collection
     */
    public function getByTaskId(int $taskId): Collection;

    /**
     * Find comment by ID
     *
     * @param int $id
     * @return Comment|null
     */
    public function findById(int $id): ?Comment;

    /**
     * Create a new comment
     *
     * @param array $data
     * @return Comment
     */
    public function create(array $data): Comment;

    /**
     * Update a comment
     *
     * @param int $id
     * @param array $data
     * @return Comment
     */
    public function update(int $id, array $data): Comment;

    /**
     * Delete a comment
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
