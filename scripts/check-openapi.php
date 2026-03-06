<?php

declare(strict_types=1);

$openApiJsonPath = 'docs/openapi/openapi.json';
$openApiYamlPath = 'docs/openapi/openapi.yaml';

if (! file_exists($openApiJsonPath) || ! file_exists($openApiYamlPath)) {
    fwrite(STDERR, 'OpenAPI files are missing.'.PHP_EOL);
    exit(1);
}

$json = json_decode((string) file_get_contents($openApiJsonPath), true);

if (! is_array($json)) {
    fwrite(STDERR, 'OpenAPI JSON is not valid JSON.'.PHP_EOL);
    exit(1);
}

$requiredTopLevelKeys = ['openapi', 'info', 'servers', 'paths', 'components'];

foreach ($requiredTopLevelKeys as $key) {
    if (! array_key_exists($key, $json)) {
        fwrite(STDERR, "OpenAPI JSON missing top-level key: {$key}".PHP_EOL);
        exit(1);
    }
}

$expectedPaths = [
    '/api/hello' => ['get'],
    '/api/create/hello' => ['get'],
    '/api/create-mirror/hello' => ['get'],
    '/api/mirror/hello' => ['get'],
    '/api/pilot-task-test' => ['get', 'post'],
];

foreach ($expectedPaths as $path => $methods) {
    if (! isset($json['paths'][$path]) || ! is_array($json['paths'][$path])) {
        fwrite(STDERR, "OpenAPI JSON missing path: {$path}".PHP_EOL);
        exit(1);
    }

    foreach ($methods as $method) {
        if (! isset($json['paths'][$path][$method]) || ! is_array($json['paths'][$path][$method])) {
            fwrite(STDERR, "OpenAPI JSON missing operation {$method} {$path}".PHP_EOL);
            exit(1);
        }

        if (! isset($json['paths'][$path][$method]['responses']) || ! is_array($json['paths'][$path][$method]['responses'])) {
            fwrite(STDERR, "OpenAPI JSON missing responses for {$method} {$path}".PHP_EOL);
            exit(1);
        }
    }
}

$expectedServers = [
    'http://127.0.0.1:8080',
    'http://0.0.0.0:80',
];

$serverUrls = array_map(
    static fn (array $server): string => (string) ($server['url'] ?? ''),
    array_values(array_filter($json['servers'], 'is_array'))
);

foreach ($expectedServers as $expectedServer) {
    if (! in_array($expectedServer, $serverUrls, true)) {
        fwrite(STDERR, "OpenAPI JSON missing expected server: {$expectedServer}".PHP_EOL);
        exit(1);
    }
}

$protectedPaths = [
    '/api/create/hello',
    '/api/create-mirror/hello',
    '/api/mirror/hello',
    '/api/pilot-task-test',
];

foreach ($protectedPaths as $path) {
    foreach ($json['paths'][$path] as $method => $operation) {
        if (! in_array($method, ['get', 'post', 'put', 'patch', 'delete'], true)) {
            continue;
        }

        if (! isset($operation['security']) || ! is_array($operation['security']) || $operation['security'] === []) {
            fwrite(STDERR, "Protected operation missing security definition: {$method} {$path}".PHP_EOL);
            exit(1);
        }
    }
}

$yaml = (string) file_get_contents($openApiYamlPath);

foreach (array_keys($expectedPaths) as $path) {
    if (! str_contains($yaml, $path.':')) {
        fwrite(STDERR, "OpenAPI YAML missing path: {$path}".PHP_EOL);
        exit(1);
    }
}

if (! str_contains($yaml, 'openapi: 3.1.0')) {
    fwrite(STDERR, 'OpenAPI YAML missing version header.'.PHP_EOL);
    exit(1);
}

fwrite(STDOUT, 'OpenAPI validation check passed.'.PHP_EOL);
