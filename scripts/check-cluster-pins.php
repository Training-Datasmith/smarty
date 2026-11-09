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

if (!function_exists('run_check_cluster_pins')) {
    /**
     * @param array<string, array{alias: string, reference: string}> $map
     */
    function run_check_cluster_pins(array $map): bool
    {
        $installedPhp = __DIR__ . '/../vendor/composer/installed.php';
        if (!is_readable($installedPhp)) {
            fwrite(STDERR, "check-cluster-pins: vendor/composer/installed.php not found; run composer update first.\n");
            return false;
        }

        /** @var array{versions: array<string, array{reference?: string, aliases?: list<string>, install_path?: string}>} $installed */
        $installed = require $installedPhp;
        $versions = $installed['versions'] ?? [];

        $installedJsonPath = __DIR__ . '/../vendor/composer/installed.json';
        if (!is_readable($installedJsonPath)) {
            fwrite(STDERR, "check-cluster-pins: vendor/composer/installed.json not found; run composer update first.\n");
            return false;
        }

        $raw = file_get_contents($installedJsonPath);
        if ($raw === false) {
            fwrite(STDERR, "check-cluster-pins: could not read installed.json.\n");
            return false;
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

if (($argc > 1 && $argv[1] === '--wrong-sha-demo') || getenv('CHECK_CLUSTER_PINS_WRONG_SHA_DEMO') === '1') {
    $demo = $expected;
    $demo['symfony/polyfill-mbstring']['reference'] = '0000000000000000000000000000000000000000';
    if (run_check_cluster_pins($demo)) {
        fwrite(STDERR, "check-cluster-pins: wrong-SHA demo should have failed.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-cluster-pins: wrong installed.php SHA demo failed as expected.\n");

    $demo = $expected;
    $demo['symfony/polyfill-mbstring']['reference'] = '1111111111111111111111111111111111111111';
    if (run_check_cluster_pins($demo)) {
        fwrite(STDERR, "check-cluster-pins: wrong installed.json SHA demo should have failed.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-cluster-pins: wrong installed.json SHA demo failed as expected.\n");
    exit(0);
}

if (!run_check_cluster_pins($expected)) {
    exit(1);
}

fwrite(STDOUT, "check-cluster-pins: all cluster packages match expected SHAs and aliases.\n");
exit(0);
