<?php

namespace Modules\Comment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Comment\Database\Factories\CommentFactory;
use Modules\Task\Entities\Task;
use Modules\User\Entities\User;

class Comment extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return CommentFactory::new();
    }

    protected $fillable = [
        'content',
        'task_id',
        'author_id',
    ];

    /**
     * Get the task that owns the comment
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the author of the comment
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
