<?php

namespace Modules\Notification\Services;

use Modules\Comment\Entities\Comment;
use Modules\Notification\Mail\NewCommentNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send comment notification to task author
     *
     * @param Comment $comment
     * @return void
     */
    public function sendCommentNotification(Comment $comment): void
    {
        // Reload the comment with relationships since it may have been serialized for the queue
        $comment->load(['task.author', 'author']);

        $task = $comment->task;

        // Check if task exists
        if (!$task) {
            Log::warning('Task not found for comment', ['comment_id' => $comment->id]);
            return;
        }

        $taskAuthor = $task->author;

        // Check if task author exists
        if (!$taskAuthor) {
            Log::warning('Task author not found', ['task_id' => $task->id, 'comment_id' => $comment->id]);
            return;
        }

        // Only send notification if comment author is not the task author
        if ($taskAuthor->id !== $comment->author_id) {
            try {
                Mail::to($taskAuthor->email)->send(new NewCommentNotification($comment));
                Log::info('Comment notification sent', [
                    'comment_id' => $comment->id,
                    'task_id' => $task->id,
                    'recipient' => $taskAuthor->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send comment notification', [
                    'comment_id' => $comment->id,
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } else {
            Log::info('Comment notification skipped - comment author is task author', [
                'comment_id' => $comment->id,
                'task_id' => $task->id
            ]);
        }
    }
}
