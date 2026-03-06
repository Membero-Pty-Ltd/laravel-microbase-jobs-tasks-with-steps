# laravel-microbase-jobs-tasks-with-steps

[![CI](https://github.com/Membero-Pty-Ltd/laravel-microbase-jobs-tasks-with-steps/actions/workflows/ci.yml/badge.svg)](https://github.com/Membero-Pty-Ltd/laravel-microbase-jobs-tasks-with-steps/actions/workflows/ci.yml)
[![License](https://img.shields.io/github/license/Membero-Pty-Ltd/laravel-microbase-jobs-tasks-with-steps)](https://github.com/Membero-Pty-Ltd/laravel-microbase-jobs-tasks-with-steps/blob/main/LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4)](#)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-red)](#)

Lean Laravel API base with:

- Sanctum-based `Access` authentication instead of `User`
- role-protected API areas: `create`, `create-mirror`, `mirror`
- DB-defined `task_types`
- durable `tasks` audit/history table
- queue-driven step execution through `ProcessTaskJob`
- pilot task example: `pilot-task-test`
- feature tests and GitHub CI

## API overview

Available endpoints:

- `GET /api/hello`
- `GET /api/create/hello`
- `GET /api/create-mirror/hello`
- `GET /api/mirror/hello`
- `POST /api/pilot-task-test`
- `GET /api/pilot-task-test?hash=...`

## Local run

Example for a multi-service host binding on `127.0.0.1:8080`:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8080
```

In a second shell, run queue worker so tasks can finish:

```bash
php artisan queue:work
```

## Standalone Docker binding

When this service is exposed as a standalone containerized service, bind it as:

- `0.0.0.0:80`

That binding is also used in the documentation examples and OpenAPI server list.

## Access creation

Create an API principal and get a Bearer token:

```bash
php artisan access:create --role=create --description="local create token"
```

Returned `token` should be used as:

```http
Authorization: Bearer 1|plainTextToken...
```

## Testing

Testing uses `.env.testing`, SQLite file DB, and database queue.

```bash
php artisan key:generate --env=testing
php artisan migrate --env=testing
php artisan db:seed --env=testing
php artisan test
```

## Documentation

- [docs](docs/)

### Postman

- [collection](docs/postman/laravel-microbase-jobs-tasks-with-steps.postman_collection.json)
- [environment](docs/postman/laravel-microbase-jobs-tasks-with-steps.postman_environment.json)

### OpenAPI

- [YAML](docs/openapi/openapi.yaml)
- [JSON](docs/openapi/openapi.json)

### Database docs

- [docs/database.md](docs/database.md)

### Command line usage

- [docs/command-line.md](docs/command-line.md)

## Notes

- `POST /api/pilot-task-test` creates a `tasks` row and dispatches `App\Jobs\ProcessTaskJob`
- the pilot task type is seeded by [Database\Seeders\PilotTaskTestSeeder](database\seeders\PilotTaskTestSeeder.php)
- unknown API paths return JSON through API fallback instead of HTML
