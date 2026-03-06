# Construction docs | db-forge

This is basically a simplified task list

## Build laravel-api-base (to be used for both microservices)

### Access

- [x] Access is achieved with simple Sactum tokens, we do not use User (removed) table for this 
- [x] There is a database table Access (UNIQ description,role = enum[create,create-mirror,mirror])
- [x] Console command to create access rows 
  - artisan access:create 
    - -r|--role=role
    - -d|--description="desc rip tion"
    - token is a Sanctum personal access token returned as {token_id}|{plain_text_token}
    - returns access row presentation
    - description must be uniq

- [x] /api/- Middlewares
  - GET /api/hello - does not require access role, returns 200|{"ok":true,"message":"hello"}
  - [x] /api/create/- - should use AccessRoleCreate Middleware
  - [x] /api/create/hello should return {"ok":true,"message":"hello", "role":"create"}
  - [x] /api/create-mirror/*
    - should use AccessRoleCreateMirror Middleware
  - [x] /api/create/hello should return {"ok":true,"message":"hello", "role":"create-mirror"}
  - [x] /api/mirror/*
    - should use AccessRoleMirror Middleware
  - [x] /api/create/hello should return {"ok":true,"message":"hello", "role":"mirror"}
  - [x] 404 only returned if passed so AccessRoleMirror should be first

### Tasks Pilot for better understanding of architecture

- [x] I want to introduce a `task_types` db structure and `TaskType` model
  - id (PK)
  - dates()
  - code (VARCHAR:30, UNIQUE)
  - description (VARCHAR, nullable)
  - default_retries (TINYINT, not null, default:3)
  - default_queue (VARCHAR, not null, default:'default')
  - steps (JSON, not null)  
    // list of allowed steps, ideally matching Job class / internal step names
  - payload (JSON, not null)  
    // “schema-like” requirements for API initialization (validation hints)
  - is_enabled (TINYINT/BOOL, not null, default:1)

- [x] I want to introduce a `tasks` db structure and `Task` model  
  (audit + history, independent of `jobs` table cleanup)
  - id (PK)
  - dates()
  - started_at (ts, nullable)
  - finished_at (ts, nullable)
  - hash (CHAR:26, UNIQUE)  
    // ULID recommended, used in URLs and logs
  - task_type_id (FK -> task_types.id)
  - access_id (FK -> accesses.id, nullable)  
    // who requested (your Access model)
  - role (ENUM: 'create','mirror')  
    // who/what context triggered it (matches your Access role)
  - status (ENUM: 'queued','running','success','failed','canceled') default:'queued'
  - step (VARCHAR:120, nullable)  
    // current step name
  - progress (TINYINT, default:0)  
    // 0..100
  - payload (JSON, not null)  
    // actual payload for this task instance
  - result (JSON, nullable)  
    // outputs: file paths, sizes, checksums, etc.
  - error (JSON, nullable)

  - Indexes:
    - UNIQUE(hash)
    - INDEX(status)

- [x] I want to introduce new job for processing tasks
  - [x] it processes tasks in such a way that
    - every step from task type is a special class that has a patern to receive most current task object
    - works with the task object and returns it when finished

- [x] I want to introduce pilot-task-test (code) type of task using Seeder
  - it should have 3 steps PTTStepOne, PTTStepTwo, PTTStepThree
    - I recommend that there is a special directory for App\TaskSteps
    - PTTStepOne to find all files in storage public
    - PTTStepTwo to find all files in storage logs
    - PTTStepThree to check du -hs on storage
    - each step should updated the result and have specific object attribute for each task

- [x] I want to introduce 
  - POST /api/pilot-task-test
    - works for any authorized Access, simplify by allowing any Sanctum approved access
    - creates new task of our pilot type
    - returns hash
    - should initialize job for this task
  - GET /api/pilot-task-test
    - requires payload {hash: TASK HASH}
    - returns task row

### Automated tests & CI (required)

- [x] We want repeatable automated verification locally and in GitHub CI.
- [x] I want all endpoints to be covered, all edge or types of payload and result to be covered.
- [x] I want this to be done by Laravel Testing Framework and best praxeology in Github CI.

#### Local test run

> Implemented: `.env.testing` + SQLite file DB (`database/testing.sqlite`) and `QUEUE_CONNECTION=database`.

- [x] proper configuration
- Use testing env:
  - .env.testing must exist
  - prefer SQLite file DB (fast, no external services)

Commands:

- composer install
- php artisan key:generate --env=testing
- php artisan migrate --env=testing
- php artisan db:seed --env=testing
- php artisan test

#### Required Feature Tests (PHPUnit)

- [x] test overview

- [x] Public:
  - GET /api/hello returns 200 JSON {"ok":true,"message":"hello"}

- [x] Auth / roles:
  - GET /api/create/hello without token returns 401 JSON
  - with Access token role=create returns 200 and {"role":"create"}
  - with Access token role=mirror returns 403 JSON

- [x] JSON Not Found:
  - GET /api/mirror/donkey returns 404 JSON (no HTML)

- [x] Pilot task:
  - POST /api/pilot-task-test with any valid Access token returns 200 and hash
  - Task gets queued/processed in tests (prefer QUEUE_CONNECTION=database in .env.testing)
  - GET /api/pilot-task-test returns task with status=success and result populated by steps

- [x] all test cases covered following original requirements not only overview list

#### CI (GitHub Actions)

- [x] integration with continues process

- On every push/PR:
  - install dependencies
  - run migrations + seed
  - run php artisan test

### Documentation Generation

The system core has been implemented and verified.  
Next step is to generate basic developer and testing documentation.

#### Postman

- [x] Prepare a Postman collection for API testing
- [x] Include example requests for all endpoints
- [x] Add descriptions and example payloads
- [x] Include environment variables (base URL, tokens)

#### OpenAPI

- [x] Generate OpenAPI specification (YAML and/or JSON)
- [x] Base it on existing routes and request validation
- [x] Ensure it matches real API responses
- [x] Store spec in /docs/openapi/

#### Command Line

- [x] Document available Artisan commands
- [x] Explain the purpose and usage of:
  - access:create
  - any task/job related commands
- [x] Provide example usage

#### Database Documentation

- [x] Generate documentation for database schema
- [x] Include tables, fields, and relationships
- [x] Briefly explain purpose of each table
- [x] Store documentation in /docs/database.md

#### README

- [x] Update README.md
- [x] Explain how to run the service:
  - locally
  - in Docker
- [x] Provide example bindings:
  - 0.0.0.0:80 (standalone service)
  - 127.0.0.1:8080 (multi-service host)
- [x] Link all documentation sections:
  - Postman
  - OpenAPI
  - Database docs
  - Command line usage

  ***UNTIL HERE THIS HAS BEEN BUILT AND TESTED***