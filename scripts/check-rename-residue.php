<?php

declare(strict_types=1);

/**
 * Fail if any old repo-owned name from the snake_case rename remains in the tree.
 */

$scanRoots = [
    'src',
    'tests',
    'libs',
    'demo',
];
$scanFiles = [
    'composer.json',
    'phpunit.xml',
];

$ignorePaths = [
    'CHANGELOG.md',
    'docs/',
    'scripts/rename-residue-patterns.txt',
    'vendor/',
];

$externalAllow = [
    'PHPUnit',
    'TestCase',
    'setUp',
    'tearDown',
    'setUpBeforeClass',
    'tearDownAfterClass',
    'setUpSmarty',
    'assert',
    'expectException',
    'markTestSkipped',
    'PDO',
    'PDOException',
    'Exception',
    'DateTime',
    'HtmlSelectDate',
    'HtmlSelectTime',
];

$patternFile = __DIR__ . '/rename-residue-patterns.txt';
if (!is_readable($patternFile)) {
    fwrite(STDERR, "check-rename-residue: missing {$patternFile}.\n");
    exit(1);
}

$patterns = [];
foreach (file($patternFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }
    $patterns[] = $line;
}

/**
 * @param list<string> $patterns
 */
function residue_in_text(string $text, array $patterns): array
{
    $hits = [];
    foreach ($patterns as $pattern) {
        if (preg_match('/\b' . preg_quote($pattern, '/') . '\b/', $text)) {
            $hits[] = $pattern;
        }
    }

    return $hits;
}

/**
 * @param list<string> $patterns
 */
function scan_tree(string $repoRoot, string $root, array $patterns, array $ignorePaths): array
{
    $failures = [];
    $rootReal = realpath($root);
  if ($rootReal === false) {
        return ['Could not resolve root: ' . $root];
    }

    $repoRootReal = realpath($repoRoot);
    if ($repoRootReal === false) {
        return ['Could not resolve repo root: ' . $repoRoot];
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootReal, FilesystemIterator::SKIP_DOTS)
    );

    /** @var SplFileInfo $file */
    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $path = $file->getPathname();
        $rel = str_replace('\\', '/', substr($path, strlen($repoRootReal) + 1));
        if (str_starts_with($rel, 'scripts/rename-residue-patterns.txt')) {
            continue;
        }
        foreach ($ignorePaths as $ignore) {
            if ($ignore !== '' && str_starts_with($rel, $ignore)) {
                continue 2;
            }
        }
        if (!str_ends_with($path, '.php') && !str_ends_with($path, '.json') && !str_ends_with($path, '.xml')) {
            continue;
        }
        $contents = file_get_contents($path);
        if ($contents === false) {
            continue;
        }
        foreach (preg_split('/\R/', $contents) as $line) {
            if (preg_match('/^\s*(\/\/|\*|#)/', $line)) {
                continue;
            }
            foreach (residue_in_text($line, $patterns) as $hit) {
                $failures[] = "{$rel}: old name '{$hit}'";
            }
        }
    }

    return $failures;
}

$demo = ($argc > 1 && $argv[1] === '--demo-inject') || getenv('CHECK_RENAME_RESIDUE_DEMO') === '1';
if ($demo) {
    $hits = residue_in_text('function assignGlobal(', $patterns);
    if ($hits === []) {
        fwrite(STDERR, "check-rename-residue: demo inject should have matched assignGlobal.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-rename-residue: demo inject matched assignGlobal as expected.\n");
    exit(0);
}

$repoRoot = dirname(__DIR__);
$failures = [];
foreach ($scanRoots as $subdir) {
    $path = $repoRoot . '/' . $subdir;
    if (!is_dir($path)) {
        continue;
    }
    $failures = array_merge($failures, scan_tree($repoRoot, $path, $patterns, $ignorePaths));
}

foreach ($scanFiles as $rel) {
    $path = $repoRoot . '/' . $rel;
    if (!is_readable($path)) {
        continue;
    }
    $contents = file_get_contents($path);
    if ($contents === false) {
        continue;
    }
    foreach (residue_in_text($contents, $patterns) as $hit) {
        $failures[] = "{$rel}: old name '{$hit}'";
    }
}

foreach (array_unique($failures) as $failure) {
    fwrite(STDERR, "check-rename-residue: {$failure}\n");
}

if ($failures !== []) {
    exit(1);
}

fwrite(STDOUT, "check-rename-residue: no old repo-owned names remain.\n");
exit(0);
