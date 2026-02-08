<?php

namespace Modules\Comment\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Comment\Entities\Comment;

class CommentRepository implements CommentRepositoryInterface
{
    protected const CACHE_TTL = 3600; // 60 minutes

    /**
     * Get all comments for a task
     *
     * @param int $taskId
     * @return Collection
     */
    public function getByTaskId(int $taskId): Collection
    {
        $cacheKey = "comments:task:{$taskId}";

        $cache = $this->getCache($taskId);
        return $cache->remember($cacheKey, self::CACHE_TTL, function () use ($taskId) {
            return Comment::with('author')
                ->where('task_id', $taskId)
                ->latest()
                ->get();
        });
    }

    /**
     * Find comment by ID
     *
     * @param int $id
     * @return Comment|null
     */
    public function findById(int $id): ?Comment
    {
        return Comment::with('author')->find($id);
    }

    /**
     * Create a new comment
     *
     * @param array $data
     * @return Comment
     */
    public function create(array $data): Comment
    {
        $comment = Comment::create($data);
        $this->clearTaskCache($data['task_id']);
        return $comment->load('author');
    }

    /**
     * Update a comment
     *
     * @param int $id
     * @param array $data
     * @return Comment
     */
    public function update(int $id, array $data): Comment
    {
        $comment = $this->findById($id);
        if (!$comment) {
            throw new \Exception("Comment not found");
        }
        $comment->update($data);
        $this->clearTaskCache($comment->task_id);
        return $comment->fresh('author');
    }

    /**
     * Delete a comment
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $comment = $this->findById($id);
        if (!$comment) {
            return false;
        }
        $taskId = $comment->task_id;
        $result = $comment->delete();
        $this->clearTaskCache($taskId);
        return $result;
    }

    /**
     * Clear cache for a specific task's comments and the task itself
     *
     * @param int $taskId
     * @return void
     */
    protected function clearTaskCache(int $taskId): void
    {
        // Clear comment cache for this task
        $cacheKey = "comments:task:{$taskId}";
        Cache::forget($cacheKey);
        
        // Clear task cache (tasks are cached with comments relationship loaded)
        // Increment the task cache version to invalidate all task caches
        $currentVersion = Cache::get('tasks:cache:version', 1);
        Cache::forever('tasks:cache:version', $currentVersion + 1);
    }

    /**
     * Get cache instance
     *
     * @param int $taskId
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getCache(int $taskId)
    {
        $cacheStore = Cache::getStore();
        
        // Check if cache store supports tags (Redis does, Database doesn't)
        if (method_exists($cacheStore, 'tags')) {
            try {
                // Try to use tags if supported
                return Cache::tags(["comments:task:{$taskId}"]);
            } catch (\BadMethodCallException $e) {
                // Tags not supported, use regular cache
                return Cache::store();
            }
        }
        
        // No tags support, use regular cache
        return Cache::store();
    }
}
