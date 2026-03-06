<?php

declare(strict_types=1);

$requiredFiles = [
    'README.md',
    'CONSTRUCTION.md',
    'docs/command-line.md',
    'docs/database.md',
    'docs/openapi/openapi.yaml',
    'docs/openapi/openapi.json',
    'docs/postman/laravel-microbase-jobs-tasks-with-steps.postman_collection.json',
    'docs/postman/laravel-microbase-jobs-tasks-with-steps.postman_environment.json',
    '.github/workflows/ci.yml',
    'phpstan.neon',
    'pint.json',
    'rector.php',
    'scripts/check-openapi.php',
    'scripts/check-doc-drift.php',
];

$missingFiles = array_values(array_filter(
    $requiredFiles,
    static fn (string $file): bool => ! file_exists($file)
));

if ($missingFiles !== []) {
    fwrite(STDERR, "Missing required documentation / quality files:\n");

    foreach ($missingFiles as $file) {
        fwrite(STDERR, "- {$file}\n");
    }

    exit(1);
}

fwrite(STDOUT, "Documentation and quality files check passed.\n");
