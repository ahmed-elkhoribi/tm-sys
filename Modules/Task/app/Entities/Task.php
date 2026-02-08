<?php

namespace Modules\Task\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Comment\Entities\Comment;
use Modules\Task\Database\Factories\TaskFactory;
use Modules\User\Entities\User;

class Task extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return TaskFactory::new();
    }

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'author_id',
        'assignee_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Get the author of the task
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the assignee of the task
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Get the comments for the task
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
