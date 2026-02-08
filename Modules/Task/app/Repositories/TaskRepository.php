<?php

namespace Modules\Task\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Modules\Task\Entities\Task;

class TaskRepository implements TaskRepositoryInterface
{
    protected const CACHE_TTL = 3600; // 60 minutes
    protected const CACHE_TAG = 'tasks';

    /**
     * Get all tasks with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function all(int $perPage = 15): LengthAwarePaginator
    {
        $cacheVersion = Cache::get('tasks:cache:version', 1);
        $cacheKey = "tasks:list:page:{$perPage}:" . request()->get('page', 1) . ":v{$cacheVersion}";

        $cache = $this->getCache();
        return $cache->remember($cacheKey, self::CACHE_TTL, function () use ($perPage) {
            return Task::with(['author', 'assignee', 'comments'])
                ->latest()
                ->paginate($perPage);
        });
    }

    /**
     * Find task by ID
     *
     * @param int $id
     * @return Task|null
     */
    public function findById(int $id): ?Task
    {
        $cacheVersion = Cache::get('tasks:cache:version', 1);
        $cacheKey = "tasks:{$id}:v{$cacheVersion}";

        $cache = $this->getCache();
        return $cache->remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return Task::with(['author', 'assignee', 'comments'])->find($id);
        });
    }

    /**
     * Create a new task
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task
    {
        $task = Task::create($data);
        $this->clearCache();
        return $task->load(['author', 'assignee']);
    }

    /**
     * Update a task
     *
     * @param int $id
     * @param array $data
     * @return Task
     */
    public function update(int $id, array $data): Task
    {
        $task = $this->findById($id);
        if (!$task) {
            throw new \Exception("Task not found");
        }
        $task->update($data);
        $this->clearCache();
        return $task->fresh(['author', 'assignee']);
    }

    /**
     * Delete a task
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $task = $this->findById($id);
        if (!$task) {
            return false;
        }
        $result = $task->delete();
        $this->clearCache();
        return $result;
    }

    /**
     * Get tasks assigned to a user
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAssignedToUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        $cacheVersion = Cache::get('tasks:cache:version', 1);
        $cacheKey = "tasks:assigned:user:{$userId}:page:" . request()->get('page', 1) . ":v{$cacheVersion}";

        $cache = $this->getCache();
        return $cache->remember($cacheKey, self::CACHE_TTL, function () use ($userId, $perPage) {
            return Task::with(['author', 'assignee', 'comments'])
                ->where('assignee_id', $userId)
                ->latest()
                ->paginate($perPage);
        });
    }

    /**
     * Clear cache for tasks
     *
     * @return void
     */
    protected function clearCache(): void
    {
        // Clear all task-related cache keys
        // Note: Database cache doesn't support tags, so we use a version-based approach
        $cacheStore = Cache::getStore();
        
        // Check if cache store supports tags (Redis does, Database doesn't)
        if (method_exists($cacheStore, 'tags')) {
            try {
                // Try to use tags if supported
                Cache::tags([self::CACHE_TAG])->flush();
                return;
            } catch (\BadMethodCallException $e) {
                // Tags not supported, fall through to version-based clearing
            }
        }
        
        // Fallback: Use cache version for database cache
        // Increment the version to invalidate all task caches
        $currentVersion = Cache::get('tasks:cache:version', 1);
        Cache::forever('tasks:cache:version', $currentVersion + 1);
    }

    /**
     * Get cache instance
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getCache()
    {
        $cacheStore = Cache::getStore();
        
        // Check if cache store supports tags (Redis does, Database doesn't)
        if (method_exists($cacheStore, 'tags')) {
            try {
                // Try to use tags if supported
                return Cache::tags([self::CACHE_TAG]);
            } catch (\BadMethodCallException $e) {
                // Tags not supported, use regular cache
                return Cache::store();
            }
        }
        
        // No tags support, use regular cache
        return Cache::store();
    }
}
