<?php

declare(strict_types=1);

/**
 * Fail if cluster packages are not installed at the expected index SHAs and aliases.
 */

$expected = [
    'symfony/polyfill-mbstring' => [
        'alias' => '1.33.99',
        'reference' => '2cecae254e4ffc2c20ac379c5cd23d91c82109df',
    ],
];

if (!function_exists('cluster_pins_load_installed')) {
    /**
     * @return array{versions: array<string, array<string, mixed>>, jsonRefs: array<string, string>}|null
     */
    function cluster_pins_load_installed(): ?array
    {
        $installedPhp = __DIR__ . '/../vendor/composer/installed.php';
        if (!is_readable($installedPhp)) {
            fwrite(STDERR, "check-cluster-pins: vendor/composer/installed.php not found; run composer update first.\n");
            return null;
        }

        /** @var array{versions: array<string, array{reference?: string, aliases?: list<string>, install_path?: string}>} $installed */
        $installed = require $installedPhp;
        $versions = $installed['versions'] ?? [];

        $installedJsonPath = __DIR__ . '/../vendor/composer/installed.json';
        if (!is_readable($installedJsonPath)) {
            fwrite(STDERR, "check-cluster-pins: vendor/composer/installed.json not found; run composer update first.\n");
            return null;
        }

        $raw = file_get_contents($installedJsonPath);
        if ($raw === false) {
            fwrite(STDERR, "check-cluster-pins: could not read installed.json.\n");
            return null;
        }

        /** @var array{packages?: list<array<string, mixed>>, 'packages-dev'?: list<array<string, mixed>>} $installedJson */
        $installedJson = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        $jsonRefs = [];
        foreach (array_merge($installedJson['packages'] ?? [], $installedJson['packages-dev'] ?? []) as $package) {
            if (!isset($package['name'], $package['source']['reference'])) {
                continue;
            }
            $jsonRefs[(string) $package['name']] = strtolower((string) $package['source']['reference']);
        }

        return ['versions' => $versions, 'jsonRefs' => $jsonRefs];
    }
}

if (!function_exists('check_cluster_php_references')) {
    /**
     * @param array<string, array{alias: string, reference: string}> $map
     */
    function check_cluster_php_references(array $map, array $versions): bool
    {
        $failed = false;
        foreach ($map as $name => $want) {
            if (!isset($versions[$name])) {
                fwrite(STDERR, sprintf("check-cluster-pins: %s is not installed.\n", $name));
                $failed = true;
                continue;
            }

            $pkg = $versions[$name];
            $aliases = $pkg['aliases'] ?? [];
            if (!in_array($want['alias'], $aliases, true)) {
                fwrite(STDERR, sprintf(
                    "check-cluster-pins: %s alias mismatch (expected %s in aliases %s).\n",
                    $name,
                    $want['alias'],
                    json_encode($aliases)
                ));
                $failed = true;
            }

            $reference = $pkg['reference'] ?? null;
            if (!is_string($reference) || $reference === '') {
                fwrite(STDERR, sprintf("check-cluster-pins: %s has no reference in installed.php.\n", $name));
                $failed = true;
            } elseif (!hash_equals($want['reference'], $reference)) {
                fwrite(STDERR, sprintf(
                    "check-cluster-pins: %s installed.php reference mismatch (expected %s, got %s).\n",
                    $name,
                    $want['reference'],
                    $reference
                ));
                $failed = true;
            }
        }

        return !$failed;
    }
}

if (!function_exists('check_cluster_json_references')) {
    /**
     * @param array<string, array{alias: string, reference: string}> $map
     * @param array<string, string> $jsonRefs
     */
    function check_cluster_json_references(array $map, array $jsonRefs): bool
    {
        $failed = false;
        foreach ($map as $name => $want) {
            if (!isset($jsonRefs[$name])) {
                fwrite(STDERR, sprintf("check-cluster-pins: %s is missing from installed.json.\n", $name));
                $failed = true;
                continue;
            }

            if (!hash_equals(strtolower($want['reference']), $jsonRefs[$name])) {
                fwrite(STDERR, sprintf(
                    "check-cluster-pins: %s installed.json source.reference mismatch (expected %s, got %s).\n",
                    $name,
                    $want['reference'],
                    $jsonRefs[$name]
                ));
                $failed = true;
            }
        }

        return !$failed;
    }
}

if (($argc > 1 && $argv[1] === '--wrong-installed-php-demo') || getenv('CHECK_CLUSTER_PINS_WRONG_PHP_DEMO') === '1') {
    $loaded = cluster_pins_load_installed();
    if ($loaded === null) {
        exit(1);
    }
    $demo = $expected;
    $demo['symfony/polyfill-mbstring']['reference'] = '0000000000000000000000000000000000000000';
    $phpFailed = !check_cluster_php_references($demo, $loaded['versions']);
    $jsonOk = check_cluster_json_references($expected, $loaded['jsonRefs']);
    if (!$phpFailed) {
        fwrite(STDERR, "check-cluster-pins: wrong installed.php demo should have failed.\n");
        exit(1);
    }
    if (!$jsonOk) {
        fwrite(STDERR, "check-cluster-pins: wrong installed.php demo should not affect installed.json check.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-cluster-pins: wrong installed.php SHA demo failed as expected.\n");
    fwrite(STDOUT, "check-cluster-pins: installed.json check still passes during php demo.\n");
    exit(0);
}

if (($argc > 1 && $argv[1] === '--wrong-installed-json-demo') || getenv('CHECK_CLUSTER_PINS_WRONG_JSON_DEMO') === '1') {
    $loaded = cluster_pins_load_installed();
    if ($loaded === null) {
        exit(1);
    }
    $demo = $expected;
    $demo['symfony/polyfill-mbstring']['reference'] = '1111111111111111111111111111111111111111';
    $jsonFailed = !check_cluster_json_references($demo, $loaded['jsonRefs']);
    $phpOk = check_cluster_php_references($expected, $loaded['versions']);
    if (!$jsonFailed) {
        fwrite(STDERR, "check-cluster-pins: wrong installed.json demo should have failed.\n");
        exit(1);
    }
    if (!$phpOk) {
        fwrite(STDERR, "check-cluster-pins: wrong installed.json demo should not affect installed.php check.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-cluster-pins: wrong installed.json SHA demo failed as expected.\n");
    fwrite(STDOUT, "check-cluster-pins: installed.php check still passes during json demo.\n");
    exit(0);
}

$loaded = cluster_pins_load_installed();
if ($loaded === null) {
    exit(1);
}

$phpOk = check_cluster_php_references($expected, $loaded['versions']);
$jsonOk = check_cluster_json_references($expected, $loaded['jsonRefs']);
if (!$phpOk || !$jsonOk) {
    exit(1);
}

fwrite(STDOUT, "check-cluster-pins: all cluster packages match expected SHAs and aliases.\n");
exit(0);
