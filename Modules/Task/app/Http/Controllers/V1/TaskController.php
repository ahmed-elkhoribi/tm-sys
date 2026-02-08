<?php

namespace Modules\Task\Http\Controllers\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Task\Http\Requests\V1\StoreTaskRequest;
use Modules\Task\Http\Requests\V1\UpdateTaskRequest;
use Modules\Task\Http\Resources\V1\TaskCollection;
use Modules\Task\Http\Resources\V1\TaskResource;
use Modules\Task\Services\TaskService;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display a listing of tasks
     *
     * @return TaskCollection
     */
    public function index(): TaskCollection
    {
        $tasks = $this->taskService->getAllTasks();
        return new TaskCollection($tasks);
    }

    /**
     * Store a newly created task
     *
     * @param StoreTaskRequest $request
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->validated(), $request->user()->id);

        return response()->json([
            'message' => 'Task created successfully',
            'data' => new TaskResource($task),
        ], 201);
    }

    /**
     * Display the specified task
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $task = $this->taskService->getTaskById($id);
            return response()->json([
                'data' => new TaskResource($task),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }

    /**
     * Update the specified task
     *
     * @param UpdateTaskRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->updateTask($id, $request->validated(), $request->user()->id);

            return response()->json([
                'message' => 'Task updated successfully',
                'data' => new TaskResource($task),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }

    /**
     * Remove the specified task
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->taskService->deleteTask($id, request()->user()->id);

            return response()->json([
                'message' => 'Task deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }

    /**
     * Get tasks assigned to the authenticated user
     *
     * @return TaskCollection
     */
    public function assignedToMe(): TaskCollection
    {
        $tasks = $this->taskService->getAssignedTasks(request()->user()->id);
        return new TaskCollection($tasks);
    }
}
