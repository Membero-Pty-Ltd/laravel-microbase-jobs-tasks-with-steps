<p align="center">
  <img src="docs/logo.png" width="180">
</p>

# laravel-microbase-jobs-tasks-with-steps

[![CI](https://github.com/Membero-Pty-Ltd/laravel-microbase-jobs-tasks-with-steps/actions/workflows/ci.yml/badge.svg)](https://github.com/Membero-Pty-Ltd/laravel-microbase-jobs-tasks-with-steps/actions/workflows/ci.yml)
[![License](https://img.shields.io/github/license/Membero-Pty-Ltd/laravel-microbase-jobs-tasks-with-steps)](https://github.com/Membero-Pty-Ltd/laravel-microbase-jobs-tasks-with-steps/blob/main/LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4)](#)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-blue)](#)

Lean Laravel API base with:

- Sanctum-based `Access` authentication instead of `User`
- role-protected API areas: `create`, `create-mirror`, `mirror`
- DB-defined `task_types`
- durable `tasks` audit/history table
- queue-driven step execution through `ProcessTaskJob`
- pilot task example: `pilot-task-test`
- feature tests and GitHub CI
- quality gates for formatting, static analysis, tests, dependency audit, docs presence checks, OpenAPI validation, doc drift checks, and coverage reporting

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

## Quality and CI

CI now checks these areas:

- composer manifest validity
- required docs / quality files presence
- OpenAPI file integrity
- drift between routes, tests, README, Postman and OpenAPI
- code style with Laravel Pint
- static analysis with PHPStan + Larastan
- feature tests with Laravel / PHPUnit
- coverage report generation and artifact upload

Dependency audit is also executed in CI, but currently as **warning-only**. This is intentional for the first baseline so known or upstream ecosystem advisories do not block all delivery work while still remaining visible. Rector baseline has also been prepared with `rector.php`; it is intentionally not a blocking CI gate yet because the package is not pinned in the current lock refresh.

### Recommended local commands

```bash
composer quality:docs
composer quality:lint
composer quality:analyse
composer quality:openapi
composer quality:drift
composer quality:test
composer quality:coverage
composer quality:refactor
composer quality
composer quality:audit
```

### Suggested local dev flow before commit / PR

```bash
composer quality:format
composer quality
```

### Initial quality KPIs

Visible / binary KPIs:

- formatting pass/fail
- static analysis pass/fail
- tests pass/fail
- dependency audit pass/fail or warning

Additional simple indicators currently available:

- PHPUnit test count in test output
- PHPStan error count in analysis output
- risky / skipped tests in PHPUnit output when present

Coverage is now available as a dedicated CI job and as a local command that writes Clover and text outputs into `build/coverage/`.

Rector is scaffolded through `rector.php` and `composer quality:refactor`. In the current package it behaves as a non-failing helper until Rector is added during the next intentional `composer.lock` refresh.

### Configuration / workflow locations

- workflow: [`.github/workflows/ci.yml`](.github/workflows/ci.yml)
- Pint config: [`pint.json`](pint.json)
- PHPStan config: [`phpstan.neon`](phpstan.neon)
- Rector baseline: [`rector.php`](rector.php)
- docs presence check: [`scripts/check-docs.php`](scripts/check-docs.php)
- OpenAPI validation: [`scripts/check-openapi.php`](scripts/check-openapi.php)
- drift check: [`scripts/check-doc-drift.php`](scripts/check-doc-drift.php)
- composer scripts: [`composer.json`](composer.json)

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
- the pilot task type is seeded by [`Database\Seeders\PilotTaskTestSeeder`](database/seeders/PilotTaskTestSeeder.php)
- unknown API paths return JSON through API fallback instead of HTML
- constructed using lean way of [CONSTRUCTION](./CONSTRUCTION.md) and automated testing
