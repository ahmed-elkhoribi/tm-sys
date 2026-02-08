# Task Management System API

A Laravel-based task management system built with modular architecture, implementing repository pattern, caching, queue-based notifications, and comprehensive testing.

## Features

- **User Authentication**: Register, login, and logout using Laravel Sanctum
- **Task Management**: Full CRUD operations for tasks with status tracking
- **Task Assignment**: Assign tasks to users
- **Comments**: Add, edit, and delete comments on tasks
- **Email Notifications**: Automatic email notifications to task authors when new comments are added
- **Caching**: Redis-based caching for improved performance
- **Queue Management**: Asynchronous email processing using Laravel queues

## Requirements

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Redis (for caching)
- Laravel Herd (or any PHP server)

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd task-manager
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ts_sys
DB_USERNAME=root
DB_PASSWORD=root

QUEUE_CONNECTION=database
CACHE_STORE=database
# CACHE_DRIVER=redis  # Uncomment if you have Redis installed
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Note for Email Testing**: For development and testing, you can use [Mailtrap](https://mailtrap.io/) as a fake SMTP server. Mailtrap provides a sandbox environment to test emails without sending them to real recipients. To use Mailtrap:

1. Sign up for a free account at https://mailtrap.io/
2. Go to your Inboxes → SMTP Settings
3. Select "Laravel" from the integration dropdown
4. Copy the credentials and update your `.env` file:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your-mailtrap-username
   MAIL_PASSWORD=your-mailtrap-password
   MAIL_ENCRYPTION=null
   # OR use port 587 with TLS:
   # MAIL_PORT=587
   # MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@example.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```
   
   **Important**: Mailtrap port 2525 typically doesn't use encryption. If you're using port 2525, set `MAIL_ENCRYPTION=null` or remove the line. If you prefer TLS encryption, use port 587 instead.

All emails sent by the application will be captured in your Mailtrap inbox for testing purposes.

**Alternative: Log Mailer for Testing**: If you're having issues with Mailtrap or SMTP configuration, you can use the log mailer as an alternative for testing. Simply set in your `.env` file:
   ```env
   MAIL_MAILER=log
   ```
   With this configuration, all emails will be written to `storage/logs/laravel.log` instead of being sent via SMTP. You can view the complete email content (headers, body, HTML) in the log file to verify that emails are being generated correctly. This is useful for:
   - Testing email templates and content
   - Debugging email generation issues
   - Verifying that the notification system is working without needing SMTP configuration
   
   **Note**: The default mailer in `config/mail.php` is already set to `log`, so if you don't configure SMTP settings, emails will automatically be logged to `storage/logs/laravel.log`.

6. Run migrations:
```bash
php artisan migrate
```

7. Start the queue worker (in a separate terminal):
```bash
php artisan queue:work
```

**Important**: The queue worker must be running for email notifications to be sent. Email notifications are processed asynchronously through the queue system.

## Postman Collection

A complete Postman collection is provided in the `Docs/` folder for easy API testing. The collection includes all API endpoints with pre-configured requests and examples.

### Importing Postman Collection and Environment

1. **Import the Environment**:
   - Open Postman
   - Click on "Environments" in the left sidebar
   - Click "Import" button
   - Select the file: `Docs/task_sys.postman_environment.json`
   - The environment will be imported as "task_sys"

2. **Import the Collection**:
   - Click on "Collections" in the left sidebar
   - Click "Import" button
   - Select the file: `Docs/Task Manager.postman_collection.json`
   - The collection will be imported as "Task Manager"

3. **Configure the Environment**:
   - Select the "task_sys" environment from the environment dropdown (top right)
   - Click on the environment name to edit it
   - Set the `base_url` variable to your application URL:
     - For local development with Laravel Herd: `http://task-manager.test` (or your Herd domain)
     - For Laravel Sail: `http://localhost`
     - For custom setup: Your application's base URL (e.g., `http://localhost:8000`)
   - Save the environment

### Using the Collection

Once imported and configured:

1. **Select the Environment**: Make sure "task_sys" is selected in the environment dropdown (top right of Postman)

2. **Test Authentication**:
   - Start with "Users → Register User" to create a new account
   - Use "Users → Login" to get an authentication token
   - Copy the token from the response

3. **Set Authentication Token**:
   - After logging in, the token is automatically saved to the environment variable `token` (if configured in the collection)
   - Or manually set it: Go to "task_sys" environment → Add variable `token` → Paste your token value
   - All authenticated requests will automatically use this token

4. **Explore Endpoints**:
   - The collection is organized by modules: Users, Tasks, Comments, Notifications
   - Each endpoint includes example request bodies
   - Modify the examples as needed for your testing

### Collection Structure

The Postman collection includes:

- **Users**: Registration, Login, Logout
- **Tasks**: CRUD operations, List tasks, Get assigned tasks
- **Comments**: Create, Update, Delete, List comments for a task
- **Notifications**: Full CRUD operations for notifications

All endpoints use the `{{base_url}}` environment variable, so you only need to update it once in the environment settings.

## Project Structure

The project uses a modular architecture with the following modules:

```
Modules/
├── User/          # Authentication and user management
├── Task/          # Task CRUD operations
├── Comment/       # Comment management
└── Notification/  # Email notifications
```

Each module follows this structure:
```
ModuleName/
├── app/
│   ├── Entities/          # Models
│   ├── Repositories/     # Repository interfaces and implementations
│   ├── Services/         # Business logic
│   ├── Http/
│   │   ├── Controllers/  # API controllers
│   │   ├── Requests/     # Form request validation
│   │   └── Resources/    # API resources
│   ├── Events/           # Events
│   ├── Listeners/        # Event listeners
│   └── Jobs/             # Queue jobs
├── database/
│   └── migrations/       # Database migrations
└── routes/
    └── api.php           # API routes
```

## API Endpoints

**Note**: All API endpoints are versioned. The current version is `v1`, so all endpoints are prefixed with `/api/v1/`.

### Authentication

#### Register User
```http
POST /api/v1/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login
```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

#### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

### Tasks

#### List All Tasks
```http
GET /api/v1/tasks
Authorization: Bearer {token}
```

#### Create Task
```http
POST /api/v1/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Complete project",
    "description": "Finish the task management system",
    "status": "pending",
    "due_date": "2024-12-31 23:59:59",
    "assignee_id": 2
}
```

**Status values**: `pending`, `in-progress`, `completed`

#### Get Task
```http
GET /api/v1/tasks/{id}
Authorization: Bearer {token}
```

#### Update Task
```http
PUT /api/v1/tasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Updated title",
    "status": "in-progress"
}
```

#### Delete Task
```http
DELETE /api/v1/tasks/{id}
Authorization: Bearer {token}
```

#### Get Tasks Assigned to Me
```http
GET /api/v1/tasks/assigned-to-me
Authorization: Bearer {token}
```

### Comments

#### Get Comments for Task
```http
GET /api/v1/tasks/{taskId}/comments
Authorization: Bearer {token}
```

#### Create Comment
```http
POST /api/v1/tasks/{taskId}/comments
Authorization: Bearer {token}
Content-Type: application/json

{
    "content": "This is a comment"
}
```

#### Update Comment
```http
PUT /api/v1/comments/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "content": "Updated comment"
}
```

#### Delete Comment
```http
DELETE /api/v1/comments/{id}
Authorization: Bearer {token}
```

## Architecture

### Repository Pattern

All data access is abstracted through repository interfaces and implementations:

- `RepositoryInterface`: Defines the contract
- `Repository`: Implements the interface with caching support

### Service Layer

Business logic is handled in service classes that use repositories via dependency injection.

### Caching Strategy

- **Tasks**: Cached with keys like `tasks:list:page:{page}` and `tasks:{id}`
- **Comments**: Cached per task with key `comments:task:{taskId}`
- Cache TTL: 60 minutes (3600 seconds)
- Automatic cache invalidation on create/update/delete operations

**Note**: The application works with both Redis and Database cache drivers:
- **Redis** (recommended): Supports cache tags for efficient bulk invalidation
- **Database**: Works without Redis, uses manual key-based invalidation (currently configured)

To use Redis, install Redis server and PHP Redis extension, then set `CACHE_STORE=redis` in `.env`.

### Queue System

Email notifications are processed asynchronously:
- Event: `CommentAddedEvent` fires when a comment is created
- Listener: `SendCommentNotificationListener` handles the event
- Job: `SendCommentNotificationJob` queues the email sending
- Queue Driver: Database (configurable in `.env`)

## Running Tests

```bash
php artisan test
```

## Queue Worker

To process queued jobs (email notifications), run:

```bash
php artisan queue:work
```

For production, use a process manager like Supervisor to keep the queue worker running.

## Database Schema

### users
- id
- name
- email
- email_verified_at
- password
- remember_token
- timestamps

### tasks
- id
- title
- description
- status (enum: pending, in-progress, completed)
- due_date
- author_id (foreign key to users)
- assignee_id (foreign key to users, nullable)
- timestamps

### comments
- id
- content
- task_id (foreign key to tasks)
- author_id (foreign key to users)
- timestamps

## Best Practices Implemented

1. **Modular Architecture**: Using nwidart/laravel-modules
2. **Repository Pattern**: Data access abstraction
3. **Service Layer**: Business logic separation
4. **Form Requests**: Request validation
5. **API Resources**: Consistent API responses
6. **Dependency Injection**: Laravel's service container
7. **Events & Listeners**: Decoupled notification system
8. **Queues**: Async email processing
9. **Caching**: Performance optimization with proper invalidation
10. **Testing**: Comprehensive test coverage

## License

MIT
