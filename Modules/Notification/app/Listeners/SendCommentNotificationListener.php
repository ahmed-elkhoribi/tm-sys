<?php

namespace Modules\Notification\Listeners;

use Modules\Notification\Events\CommentAddedEvent;
use Modules\Notification\Jobs\SendCommentNotificationJob;

class SendCommentNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentAddedEvent $event): void
    {
        SendCommentNotificationJob::dispatch($event->comment);
    }
}
