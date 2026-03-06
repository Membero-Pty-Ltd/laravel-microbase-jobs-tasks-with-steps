<?php

declare(strict_types=1);

$routesContents = (string) file_get_contents('routes/api.php');
$readmeContents = (string) file_get_contents('README.md');
$openApi = json_decode((string) file_get_contents('docs/openapi/openapi.json'), true);
$postman = json_decode((string) file_get_contents('docs/postman/laravel-microbase-jobs-tasks-with-steps.postman_collection.json'), true);
$testFiles = glob('tests/Feature/*.php');
$testsContents = '';

foreach ($testFiles as $testFile) {
    $testsContents .= '\n'.(string) file_get_contents($testFile);
}

if (! is_array($openApi) || ! isset($openApi['paths']) || ! is_array($openApi['paths'])) {
    fwrite(STDERR, 'OpenAPI JSON cannot be used for drift checks.'.PHP_EOL);
    exit(1);
}

if (! is_array($postman)) {
    fwrite(STDERR, 'Postman collection cannot be used for drift checks.'.PHP_EOL);
    exit(1);
}

$expectedEndpoints = [
    [
        'method' => 'GET',
        'path' => '/api/hello',
        'route_fragment' => 'Route::get(\'/hello\'',
    ],
    [
        'method' => 'GET',
        'path' => '/api/create/hello',
        'route_fragment' => 'Route::prefix(\'create\')',
    ],
    [
        'method' => 'GET',
        'path' => '/api/create-mirror/hello',
        'route_fragment' => 'Route::prefix(\'create-mirror\')',
    ],
    [
        'method' => 'GET',
        'path' => '/api/mirror/hello',
        'route_fragment' => 'Route::prefix(\'mirror\')',
    ],
    [
        'method' => 'POST',
        'path' => '/api/pilot-task-test',
        'route_fragment' => 'Route::post(\'/pilot-task-test\'',
    ],
    [
        'method' => 'GET',
        'path' => '/api/pilot-task-test',
        'route_fragment' => 'Route::get(\'/pilot-task-test\'',
    ],
];

$postmanItems = [];
$walker = function (array $items) use (&$walker, &$postmanItems): void {
    foreach ($items as $item) {
        if (isset($item['request']) && is_array($item['request'])) {
            $method = strtoupper((string) ($item['request']['method'] ?? 'GET'));
            $path = '/'.implode('/', array_map('strval', $item['request']['url']['path'] ?? []));
            $postmanItems[$method.' '.$path] = true;

            continue;
        }

        if (isset($item['item']) && is_array($item['item'])) {
            $walker($item['item']);
        }
    }
};
$walker(is_array($postman['item'] ?? null) ? $postman['item'] : []);

foreach ($expectedEndpoints as $endpoint) {
    $method = $endpoint['method'];
    $path = $endpoint['path'];
    $key = $method.' '.$path;

    if (! str_contains($routesContents, $endpoint['route_fragment'])) {
        fwrite(STDERR, "routes/api.php missing endpoint fragment for {$key}".PHP_EOL);
        exit(1);
    }

    if (
        ! str_contains($readmeContents, '`'.$method.' '.$path.'`')
        && ! str_contains($readmeContents, '`'.$method.' '.$path.'?')
        && ! str_contains($readmeContents, '`'.$path.'`')
        && ! str_contains($readmeContents, '`'.$path.'?')
    ) {
        fwrite(STDERR, "README missing endpoint reference: {$key}".PHP_EOL);
        exit(1);
    }

    $openApiPath = $openApi['paths'][$path] ?? null;

    if (! is_array($openApiPath) || ! array_key_exists(strtolower($method), $openApiPath)) {
        fwrite(STDERR, "OpenAPI missing endpoint: {$key}".PHP_EOL);
        exit(1);
    }

    if (! isset($postmanItems[$key])) {
        fwrite(STDERR, "Postman missing endpoint: {$key}".PHP_EOL);
        exit(1);
    }

    if (! str_contains($testsContents, $path)) {
        fwrite(STDERR, "Feature tests missing endpoint reference: {$path}".PHP_EOL);
        exit(1);
    }
}

if (! str_contains($testsContents, '/api/mirror/donkey')) {
    fwrite(STDERR, 'Feature tests missing fallback drift reference.'.PHP_EOL);
    exit(1);
}

fwrite(STDOUT, 'Documentation drift check passed.'.PHP_EOL);
