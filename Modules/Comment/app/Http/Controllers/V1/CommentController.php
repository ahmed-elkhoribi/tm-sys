<?php

namespace Modules\Comment\Http\Controllers\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Comment\Http\Requests\V1\StoreCommentRequest;
use Modules\Comment\Http\Requests\V1\UpdateCommentRequest;
use Modules\Comment\Http\Resources\V1\CommentCollection;
use Modules\Comment\Http\Resources\V1\CommentResource;
use Modules\Comment\Services\CommentService;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService
    ) {}

    /**
     * Get comments for a task
     *
     * @param int $taskId
     * @return CommentCollection
     */
    public function index(int $taskId): CommentCollection
    {
        $comments = $this->commentService->getCommentsByTaskId($taskId);
        return new CommentCollection($comments);
    }

    /**
     * Store a newly created comment
     *
     * @param StoreCommentRequest $request
     * @param int $taskId
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request, int $taskId): JsonResponse
    {
        $data = $request->validated();
        $data['task_id'] = $taskId;
        $comment = $this->commentService->createComment($data, $request->user()->id);

        return response()->json([
            'message' => 'Comment created successfully',
            'data' => new CommentResource($comment),
        ], 201);
    }

    /**
     * Update the specified comment
     *
     * @param UpdateCommentRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCommentRequest $request, int $id): JsonResponse
    {
        try {
            $comment = $this->commentService->updateComment($id, $request->validated(), $request->user()->id);

            return response()->json([
                'message' => 'Comment updated successfully',
                'data' => new CommentResource($comment),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }

    /**
     * Remove the specified comment
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->commentService->deleteComment($id, request()->user()->id);

            return response()->json([
                'message' => 'Comment deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }
}
