# Command line documentation

This project uses Laravel Artisan for local administration, access provisioning, seeding, migrations, queue processing, and test execution.

## Custom command

### `php artisan access:create`

Creates a new `accesses` row and mints a Laravel Sanctum token for it.

Options:

- `--role=` or `-r=` required; allowed values: `create`, `create-mirror`, `mirror`
- `--description=` or `-d=` required; must be unique, max 70 characters

Example:

```bash
php artisan access:create --role=create --description="local create token"
```

Example output:

```json
{
  "id": 1,
  "description": "local create token",
  "role": "create",
  "token": "1|plainTextSanctumToken..."
}
```

Notes:

- the returned token is the value to use in `Authorization: Bearer ...`
- the token is stored hashed in `personal_access_tokens`; only the plain text value from command output can be used by API clients
- `description` is unique in `accesses`

## Queue and task processing

The pilot task endpoint creates a `tasks` row and dispatches `App\\Jobs\\ProcessTaskJob` to the configured queue.

### Process one queued job

```bash
php artisan queue:work --once --stop-when-empty --sleep=0 --tries=1
```

Useful for local verification of a single task.

### Process queue continuously

```bash
php artisan queue:work
```

Use this when running the API locally and you want queued tasks to be consumed continuously.

## Database and seed commands

### Run migrations

```bash
php artisan migrate
```

### Seed all default seeders

```bash
php artisan db:seed
```

This runs `Database\\Seeders\\DatabaseSeeder`, which currently includes `PilotTaskTestSeeder`.

### Seed only pilot task type

```bash
php artisan db:seed --class=Database\\Seeders\\PilotTaskTestSeeder
```

This creates or updates the `pilot-task-test` entry in `task_types`.

## Testing commands

### Prepare testing app key

```bash
php artisan key:generate --env=testing
```

### Migrate testing database

```bash
php artisan migrate --env=testing
```

### Seed testing database

```bash
php artisan db:seed --env=testing
```

### Run test suite

```bash
php artisan test
```

## Helpful built-in Artisan commands for this project

### List routes

```bash
php artisan route:list
```

### Show available commands

```bash
php artisan list
```

### Clear cached configuration

```bash
php artisan config:clear
```
