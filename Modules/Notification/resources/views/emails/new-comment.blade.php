<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Comment on Your Task</title>
</head>
<body>
    <h2>New Comment on Your Task</h2>
    
    <p>Hello {{ $task->author->name }},</p>
    
    <p>{{ $commentAuthor->name }} has added a new comment on your task:</p>
    
    <h3>Task: {{ $task->title }}</h3>
    <p><strong>Description:</strong> {{ $task->description }}</p>
    
    <h3>Comment:</h3>
    <p>{{ $comment->content }}</p>
    
    <p>Best regards,<br>Task Management System</p>
</body>
</html>
