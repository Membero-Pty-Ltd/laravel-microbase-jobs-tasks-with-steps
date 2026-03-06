# Database documentation

This service uses Laravel core tables plus project-specific tables for access control and task orchestration.

## Relationship overview

- `accesses` 1:N `personal_access_tokens` via Sanctum morph relation (`tokenable_type`, `tokenable_id`)
- `accesses` 1:N `tasks`
- `task_types` 1:N `tasks`
- `jobs` stores queued `ProcessTaskJob` payloads for asynchronous execution
- `failed_jobs` stores queue failures

## Tables

### `accesses`

Purpose: API principals used instead of a standard `users` table.

Fields:

- `id` bigint, primary key
- `created_at`, `updated_at`
- `description` varchar(70), unique
- `role` enum: `create`, `create-mirror`, `mirror`

Notes:

- rows are created by `php artisan access:create`
- each row can own Sanctum personal access tokens

### `personal_access_tokens`

Purpose: Laravel Sanctum token storage.

Fields:

- `id` bigint, primary key
- `tokenable_type`, `tokenable_id`
- `name`
- `token` varchar(64), unique, hashed token value
- `abilities` nullable text
- `last_used_at` nullable timestamp
- `expires_at` nullable timestamp
- `created_at`, `updated_at`

Notes:

- tokens for this project belong to `App\\Models\\Access`
- API authentication uses `auth:sanctum`

### `task_types`

Purpose: registry of allowed task definitions and their execution metadata.

Fields:

- `id` bigint, primary key
- `created_at`, `updated_at`
- `code` varchar(30), unique
- `description` nullable string
- `default_retries` unsigned tinyint, default `3`
- `default_queue` string, default `default`
- `steps` json
- `payload` json
- `is_enabled` boolean, default `true`

Notes:

- seeded pilot type code: `pilot-task-test`
- `steps` stores ordered PHP class names implementing `App\\Contracts\\TaskStep`
- `payload` stores schema-like hints for task creation

### `tasks`

Purpose: durable audit/history table for requested work, independent from queue cleanup.

Fields:

- `id` bigint, primary key
- `created_at`, `updated_at`
- `started_at` nullable timestamp
- `finished_at` nullable timestamp
- `hash` char(26), unique ULID-style identifier used in API
- `task_type_id` foreign key to `task_types.id`
- `access_id` nullable foreign key to `accesses.id`
- `role` enum: `create`, `mirror`
- `status` enum: `queued`, `running`, `success`, `failed`, `canceled`; default `queued`
- `step` nullable varchar(120)
- `progress` unsigned tinyint, default `0`
- `payload` json
- `result` nullable json
- `error` nullable json

Indexes:

- unique `hash`
- index on `status`

Notes:

- `hash` is auto-generated in `App\\Models\\Task` with `Str::ulid()`
- `result` is populated incrementally by task steps
- `error` stores failure summary when job execution throws

### `jobs`

Purpose: Laravel database queue backend.

Fields:

- `id` bigint, primary key
- `queue` indexed string
- `payload` longtext
- `attempts` unsigned tinyint
- `reserved_at` nullable unsigned integer
- `available_at` unsigned integer
- `created_at` unsigned integer

Notes:

- `ProcessTaskJob` is dispatched here when `/api/pilot-task-test` is called

### `failed_jobs`

Purpose: queue failure audit.

Fields:

- `id` bigint, primary key
- `uuid` unique string
- `connection` text
- `queue` text
- `payload` longtext
- `exception` longtext
- `failed_at` timestamp

### `job_batches`

Purpose: Laravel batch bookkeeping table.

Fields:

- `id` string, primary key
- `name`
- `total_jobs`
- `pending_jobs`
- `failed_jobs`
- `failed_job_ids`
- `options` nullable mediumtext
- `cancelled_at`
- `created_at`
- `finished_at`

### `cache`

Purpose: Laravel database cache store.

Fields:

- `key` string, primary key
- `value` mediumtext
- `expiration` indexed integer

### `cache_locks`

Purpose: Laravel cache lock storage.

Fields:

- `key` string, primary key
- `owner` string
- `expiration` indexed integer

## Pilot task result structure

For `pilot-task-test`, `tasks.result` is expected to contain:

- `ptt_step_one`
  - `disk`
  - `count`
  - `files_sample`
- `ptt_step_two`
  - `path`
  - `count`
  - `files_sample`
- `ptt_step_three`
  - `cmd`
  - `successful`
  - `exit_code`
  - `output`
  - `error_output`
