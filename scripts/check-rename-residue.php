<?php

declare(strict_types=1);

/**
 * Fail if rename residue remains (old names, class/file mismatch, camelCase repo-owned identifiers).
 */

$scanRoots = ['src', 'tests', 'libs', 'demo'];
$scanFiles = ['composer.json', 'phpunit.xml', 'Makefile'];
$grammarGlobs = ['src/Lexer/*.plex', 'src/Parser/*.y'];

$ignorePaths = [
    'CHANGELOG.md',
    'docs/',
    'vendor/',
    'scripts/rename-residue-patterns.txt',
    'scripts/rename-residue-allowlist.txt',
    'scripts/codemod-tests-studly-snake.py',
];

$patternFile = __DIR__ . '/rename-residue-patterns.txt';
$allowlistFile = __DIR__ . '/rename-residue-allowlist.txt';

function load_lines(string $path): array
{
    if (!is_readable($path)) {
        fwrite(STDERR, "check-rename-residue: missing {$path}.\n");
        exit(1);
    }
    $lines = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        $lines[] = $line;
    }

    return $lines;
}

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

function is_allowed_token(string $token, array $allowlist): bool
{
    foreach ($allowlist as $entry) {
        if ($token === $entry) {
            return true;
        }
        if (str_contains($entry, '::') && str_starts_with($entry, $token)) {
            return true;
        }
    }

    return false;
}

function camel_case_repo_owned(string $token): bool
{
    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $token)) {
        return false;
    }
    if (str_starts_with($token, 'test') && strlen($token) > 4) {
        return false;
    }
    if (str_starts_with($token, '__')) {
        return false;
    }
    if (str_contains($token, '_')) {
        return false;
    }

    return (bool) preg_match('/[a-z0-9][A-Z]/', $token);
}

function scan_tree(string $repoRoot, string $root, array $patterns, array $ignorePaths, ?callable $lineFilter = null): array
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
        foreach ($ignorePaths as $ignore) {
            if ($ignore !== '' && str_starts_with($rel, $ignore)) {
                continue 2;
            }
        }
        if (str_contains($rel, '/templates_c/')) {
            continue;
        }
        if (!preg_match('/\.(php|json|xml|y|plex|md|plex)$/', $rel) && !in_array(basename($rel), ['Makefile'], true)) {
            continue;
        }
        $contents = file_get_contents($path);
        if ($contents === false) {
            continue;
        }
        foreach (preg_split('/\R/', $contents) as $lineno => $line) {
            if (preg_match('/^\s*(\/\/|\*|#)/', $line)) {
                continue;
            }
            if ($lineFilter !== null && $lineFilter($rel, $line) === false) {
                continue;
            }
            foreach (residue_in_text($line, $patterns) as $hit) {
                $failures[] = "{$rel}: old name '{$hit}'";
            }
        }
    }

    return $failures;
}

function scan_test_class_basenames(string $repoRoot): array
{
    $failures = [];
    $tests = $repoRoot . '/tests';
    $testClassRe = '/^\s*(?:abstract\s+|final\s+)?class\s+([A-Za-z_][A-Za-z0-9_]*)\s+extends\s+(?:\\\\)?PHPUnit(?:_Smarty|\\\\Framework\\\\TestCase)/m';
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tests, FilesystemIterator::SKIP_DOTS)
    );
    /** @var SplFileInfo $file */
    foreach ($iterator as $file) {
        if (!$file->isFile() || !str_ends_with($file->getFilename(), '.php')) {
            continue;
        }
        $path = $file->getPathname();
        if (str_contains($path, '/templates_c/')) {
            continue;
        }
        $basename = $file->getBasename('.php');
        if (!str_ends_with($basename, '_Test') && !str_ends_with($basename, 'Test')) {
            continue;
        }
        $contents = file_get_contents($path);
        if ($contents === false || !preg_match($testClassRe, $contents, $m)) {
            continue;
        }
        if ($m[1] !== $basename) {
            $rel = str_replace('\\', '/', substr($path, strlen($repoRoot) + 1));
            $failures[] = "{$rel}: test class '{$m[1]}' does not match file '{$basename}'";
        }
    }

    return $failures;
}

function scan_camel_case(string $repoRoot, array $allowlist): array
{
    $failures = [];
    foreach (['tests', 'libs'] as $subdir) {
        $root = $repoRoot . '/' . $subdir;
        if (!is_dir($root)) {
            continue;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );
        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.php')) {
                continue;
            }
            $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($repoRoot) + 1));
            if (str_contains($rel, '/templates_c/') || preg_match('#/cache/[^/]+\.tpl\.php$#', $rel)) {
                continue;
            }
            $contents = file_get_contents($file->getPathname());
            if ($contents === false) {
                continue;
            }
            foreach (preg_split('/\R/', $contents) as $line) {
                if (preg_match('/^\s*(\/\/|\*|#)/', $line)) {
                    continue;
                }
                $code_line = preg_replace('/\'[^\']*\'|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"/', '', $line) ?? $line;
                if (preg_match('/\bclass\s+([A-Za-z_][A-Za-z0-9_]*)/', $code_line, $m)) {
                    $token = $m[1];
                    if (camel_case_repo_owned($token) && !is_allowed_token($token, $allowlist)) {
                        $failures[] = "{$rel}: camelCase repo-owned class '{$token}'";
                    }
                }
                if (preg_match('/function\s+&?\s*([A-Za-z_][A-Za-z0-9_]*)\s*\(/', $code_line, $m)) {
                    $token = $m[1];
                    if (str_starts_with($token, 'test')) {
                        continue;
                    }
                    if (camel_case_repo_owned($token) && !is_allowed_token($token, $allowlist)) {
                        $failures[] = "{$rel}: camelCase repo-owned method '{$token}'";
                    }
                }
                if (preg_match('/\$(\w+)/', $code_line, $m)) {
                    $token = $m[1];
                    if (camel_case_repo_owned($token) && !is_allowed_token($token, $allowlist)) {
                        $failures[] = "{$rel}: camelCase repo-owned property '\${$token}'";
                    }
                }
            }
        }
    }

    return $failures;
}

$patterns = load_lines($patternFile);
$allowlist = load_lines($allowlistFile);

$demo = $argv[1] ?? '';
if ($demo === '--demo-inject' || getenv('CHECK_RENAME_RESIDUE_DEMO') === 'assignGlobal') {
    $hits = residue_in_text('function assignGlobal(', $patterns);
    if ($hits === []) {
        fwrite(STDERR, "check-rename-residue: demo inject should have matched assignGlobal.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-rename-residue: demo inject matched assignGlobal as expected.\n");
    exit(0);
}
if ($demo === '--demo-class-mismatch') {
    if (scan_test_class_basenames(dirname(__DIR__)) !== []) {
        fwrite(STDERR, "check-rename-residue: class-mismatch demo expected zero mismatches while violations remain.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-rename-residue: demo class/file basename check passed as expected.\n");
    exit(0);
}
if ($demo === '--demo-grammar-old') {
    $hits = residue_in_text('TemplateLexer', ['TemplateLexer', 'TemplateParser', 'ConfigfileLexer', 'ConfigfileParser']);
    if ($hits === []) {
        fwrite(STDERR, "check-rename-residue: grammar demo should detect old lexer/parser names.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-rename-residue: demo grammar inject matched TemplateLexer as expected.\n");
    exit(0);
}
if ($demo === '--demo-camel') {
    $probe = 'repoOwnedCamelProbe';
    if (!camel_case_repo_owned($probe) || is_allowed_token($probe, $allowlist)) {
        fwrite(STDERR, "check-rename-residue: camel demo misconfigured.\n");
        exit(1);
    }
    $synthetic = "function {$probe}() {}\n";
    if (residue_in_text($synthetic, [$probe]) === []) {
        fwrite(STDERR, "check-rename-residue: camel demo should match injected identifier.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-rename-residue: demo camelCase detector matched {$probe} as expected.\n");
    exit(0);
}

$repoRoot = dirname(__DIR__);
$failures = scan_test_class_basenames($repoRoot);

$grammarPatterns = ['TemplateLexer', 'TemplateParser', 'ConfigfileLexer', 'ConfigfileParser', 'mergePrefixCode', 'compileChildBlock', 'getTagStackCount'];
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
    foreach (residue_in_text($contents, array_merge($patterns, $grammarPatterns)) as $hit) {
        $failures[] = "{$rel}: old name '{$hit}'";
    }
}

foreach (glob($repoRoot . '/src/Lexer/*.plex') ?: [] as $path) {
    $rel = str_replace('\\', '/', substr($path, strlen($repoRoot) + 1));
    $contents = (string) file_get_contents($path);
    foreach (residue_in_text($contents, $grammarPatterns) as $hit) {
        $failures[] = "{$rel}: old name '{$hit}'";
    }
}

foreach (glob($repoRoot . '/src/Parser/*.y') ?: [] as $path) {
    $rel = str_replace('\\', '/', substr($path, strlen($repoRoot) + 1));
    $contents = (string) file_get_contents($path);
    foreach (residue_in_text($contents, $grammarPatterns) as $hit) {
        $failures[] = "{$rel}: old name '{$hit}'";
    }
}

$failures = array_merge($failures, scan_camel_case($repoRoot, $allowlist));

foreach (array_unique($failures) as $failure) {
    fwrite(STDERR, "check-rename-residue: {$failure}\n");
}

if ($failures !== []) {
    exit(1);
}

fwrite(STDOUT, "check-rename-residue: no old repo-owned names remain.\n");
exit(0);
