<?php

namespace Modules\Comment\Services;

use Modules\Comment\Entities\Comment;
use Modules\Comment\Repositories\CommentRepositoryInterface;
use Modules\Notification\Events\CommentAddedEvent;

class CommentService
{
    public function __construct(
        protected CommentRepositoryInterface $commentRepository
    ) {}

    /**
     * Get comments for a task
     *
     * @param int $taskId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCommentsByTaskId(int $taskId)
    {
        return $this->commentRepository->getByTaskId($taskId);
    }

    /**
     * Get comment by ID
     *
     * @param int $id
     * @return Comment
     * @throws \Exception
     */
    public function getCommentById(int $id): Comment
    {
        $comment = $this->commentRepository->findById($id);
        if (!$comment) {
            throw new \Exception('Comment not found', 404);
        }
        return $comment;
    }

    /**
     * Create a new comment
     *
     * @param array $data
     * @param int $authorId
     * @return Comment
     */
    public function createComment(array $data, int $authorId): Comment
    {
        $data['author_id'] = $authorId;
        $comment = $this->commentRepository->create($data);

        // Load relationships before firing event (for queue serialization)
        $comment->load(['task.author', 'author']);

        // Fire event for notification
        event(new CommentAddedEvent($comment));

        return $comment;
    }

    /**
     * Update a comment
     *
     * @param int $id
     * @param array $data
     * @param int $userId
     * @return Comment
     * @throws \Exception
     */
    public function updateComment(int $id, array $data, int $userId): Comment
    {
        $comment = $this->commentRepository->findById($id);
        if (!$comment) {
            throw new \Exception('Comment not found', 404);
        }

        if ($comment->author_id !== $userId) {
            throw new \Exception('Unauthorized to update this comment', 403);
        }

        return $this->commentRepository->update($id, $data);
    }

    /**
     * Delete a comment
     *
     * @param int $id
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
    public function deleteComment(int $id, int $userId): bool
    {
        $comment = $this->commentRepository->findById($id);
        if (!$comment) {
            throw new \Exception('Comment not found', 404);
        }

        if ($comment->author_id !== $userId) {
            throw new \Exception('Unauthorized to delete this comment', 403);
        }

        return $this->commentRepository->delete($id);
    }
}
