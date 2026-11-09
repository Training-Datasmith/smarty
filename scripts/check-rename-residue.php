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

/**
 * @return list<array{path: string, class: string, member: string}>
 */
function parse_allowlist_entries(array $lines): array
{
    $rules = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (preg_match('/^(.+?)\s+([A-Za-z_][A-Za-z0-9_]*)::([A-Za-z_][A-Za-z0-9_]*)$/', $line, $m)) {
            $path = trim($m[1]);
            $class = $m[2];
            $member = $m[3];
            if (str_contains($path, '*') || str_contains($class, '*') || str_contains($member, '*')) {
                fwrite(STDERR, "check-rename-residue: allowlist wildcards are not allowed: {$line}\n");
                exit(1);
            }
            $rules[] = ['path' => $path, 'class' => $class, 'member' => $member];
            continue;
        }
        fwrite(STDERR, "check-rename-residue: invalid allowlist line (expected path Class::member): {$line}\n");
        exit(1);
    }

    return $rules;
}

/**
 * @param list<array{path: string, class: string, member: string}> $rules
 */
function is_allowed_at_site(string $rel, ?string $ownerClass, string $kind, string $token, array $rules): bool
{
    if ($ownerClass === null) {
        return false;
    }
    foreach ($rules as $rule) {
        if ($rel !== $rule['path'] || $ownerClass !== $rule['class'] || $token !== $rule['member']) {
            continue;
        }
        if (!in_array($kind, ['method', 'property'], true)) {
            continue;
        }

        return true;
    }

    return false;
}

function is_phpunit_lifecycle_hook(string $name): bool
{
    return in_array($name, ['setUp', 'tearDown', 'setUpBeforeClass', 'tearDownAfterClass'], true);
}

function class_name_in_phpunit_framework(string $name): bool
{
    return $name === 'PHPUnit_Smarty' || str_starts_with($name, 'PHPUnit\\');
}

function resolve_class_extends_parent(string $contents, string $shortClass): ?string
{
    if (!preg_match('/\bclass\s+' . preg_quote($shortClass, '/') . '\s+extends\s+([A-Za-z0-9_\\\\]+)/', $contents, $m)) {
        return null;
    }

    return ltrim($m[1], '\\');
}

function find_php_class_definition_file(string $repoRoot, string $shortClass): ?string
{
    static $cache = [];
    if (isset($cache[$shortClass])) {
        return $cache[$shortClass];
    }
    foreach (['tests', 'libs', 'src', 'demo'] as $subdir) {
        $root = $repoRoot . '/' . $subdir;
        if (!is_dir($root)) {
            continue;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );
        $pattern = '/\bclass\s+' . preg_quote($shortClass, '/') . '\b/';
        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.php')) {
                continue;
            }
            $contents = file_get_contents($file->getPathname());
            if ($contents !== false && preg_match($pattern, $contents)) {
                $cache[$shortClass] = $file->getPathname();

                return $cache[$shortClass];
            }
        }
    }
    $cache[$shortClass] = null;

    return null;
}

function class_inherits_phpunit_testcase(string $repoRoot, string $contents, string $shortClass): bool
{
    $seen = [];
    $parent = resolve_class_extends_parent($contents, $shortClass);
    while ($parent !== null) {
        if ($parent === 'PHPUnit_Smarty' || $parent === 'PHPUnit\\Framework\\TestCase'
            || str_starts_with($parent, 'PHPUnit\\Framework\\')) {
            return true;
        }
        if (isset($seen[$parent])) {
            return false;
        }
        $seen[$parent] = true;
        $parentFile = find_php_class_definition_file($repoRoot, $parent);
        if ($parentFile === null) {
            return false;
        }
        $parentContents = file_get_contents($parentFile);
        if ($parentContents === false) {
            return false;
        }
        $parent = resolve_class_extends_parent($parentContents, $parent);
    }

    return false;
}

function phpunit_framework_declares_lifecycle_hook(string $methodName): bool
{
    ensure_phpunit_test_bootstrap(dirname(__DIR__));
    $framework = new ReflectionClass(\PHPUnit\Framework\TestCase::class);

    return $framework->hasMethod($methodName);
}

function reflection_class_from_file(string $absPath, string $className): ?ReflectionClass
{
    static $loaded = [];
    if (!isset($loaded[$absPath])) {
        $loaded[$absPath] = false;
        if (is_readable($absPath)) {
            try {
                require_once $absPath;
                $loaded[$absPath] = true;
            } catch (Throwable) {
                return null;
            }
        }
    }
    if (!class_exists($className)) {
        return null;
    }

    try {
        return new ReflectionClass($className);
    } catch (ReflectionException) {
        return null;
    }
}

function class_extends_phpunit_framework(ReflectionClass $ref): bool
{
    for ($cur = $ref; $cur !== false; $cur = $cur->getParentClass()) {
        if (class_name_in_phpunit_framework($cur->getName())) {
            return true;
        }
    }

    return false;
}

function ensure_phpunit_test_bootstrap(string $repoRoot): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $bootstrap = $repoRoot . '/tests/Bootstrap.php';
    if (is_readable($bootstrap)) {
        require_once $bootstrap;
    }
    $done = true;
}

function phpunit_lifecycle_hook_overrides_framework(
    string $repoRoot,
    string $absPath,
    string $className,
    string $methodName,
    string $contents,
    string $ownerClassShort
): bool {
    if (!is_phpunit_lifecycle_hook($methodName) || !phpunit_framework_declares_lifecycle_hook($methodName)) {
        return false;
    }
    ensure_phpunit_test_bootstrap($repoRoot);
    $ref = reflection_class_from_file($absPath, $className);
    if ($ref !== null && $ref->hasMethod($methodName) && class_extends_phpunit_framework($ref)) {
        for ($parent = $ref->getParentClass(); $parent !== false; $parent = $parent->getParentClass()) {
            if (class_name_in_phpunit_framework($parent->getName()) && $parent->hasMethod($methodName)) {
                return true;
            }
        }
    }

    return class_inherits_phpunit_testcase($repoRoot, $contents, $ownerClassShort);
}

/**
 * @param list<array{path: string, class: string, member: string}> $rules
 */
function camel_case_method_allowed(
    string $repoRoot,
    string $rel,
    ?string $ownerClass,
    string $token,
    string $contents,
    ?string $absPath,
    array $allowRules,
    ?string $ownerFqn = null
): bool {
    if (is_allowed_at_site($rel, $ownerClass, 'method', $token, $allowRules)) {
        return true;
    }
    if ($ownerClass === null || !is_phpunit_lifecycle_hook($token)) {
        return false;
    }
    if ($absPath !== null && $absPath !== '' && is_readable($absPath)) {
        $reflectClass = $ownerFqn ?? $ownerClass;

        return phpunit_lifecycle_hook_overrides_framework(
            $repoRoot,
            $absPath,
            $reflectClass,
            $token,
            $contents,
            $ownerClass
        );
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

function test_class_basename_mismatch(string $rel, string $contents): ?string
{
    $basename = basename($rel, '.php');
    if (!str_ends_with($basename, '_Test') && !str_ends_with($basename, 'Test')) {
        return null;
    }
    $testClassRe = '/^\s*(?:abstract\s+|final\s+)?class\s+([A-Za-z_][A-Za-z0-9_]*)\s+extends\s+(?:\\\\)?PHPUnit(?:_Smarty|\\\\Framework\\\\TestCase)/m';
    if (!preg_match($testClassRe, $contents, $m)) {
        return null;
    }
    if ($m[1] === $basename) {
        return null;
    }

    return "{$rel}: test class '{$m[1]}' does not match file '{$basename}'";
}

function scan_test_class_basenames(string $repoRoot): array
{
    $failures = [];
    $tests = $repoRoot . '/tests';
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
        $rel = str_replace('\\', '/', substr($path, strlen($repoRoot) + 1));
        $contents = file_get_contents($path);
        if ($contents === false) {
            continue;
        }
        $mismatch = test_class_basename_mismatch($rel, $contents);
        if ($mismatch !== null) {
            $failures[] = $mismatch;
        }
    }

    return $failures;
}

/**
 * @param list<array{path: string, class: string, member: string}> $allowRules
 */
function scan_camel_case_contents(
    string $repoRoot,
    string $rel,
    string $contents,
    array $allowRules,
    ?string $absPath = null
): array {
    $failures = [];
    $namespace = '';
    $ownerClass = null;
    $ownerFqn = null;
    foreach (preg_split('/\R/', $contents) as $line) {
        if (preg_match('/^\s*(\/\/|\*|#)/', $line)) {
            continue;
        }
        $code_line = preg_replace('/\'[^\']*\'|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"/', '', $line) ?? $line;
        if (preg_match('/^\s*namespace\s+([^;{]+)\s*;/', $code_line, $m)) {
            $namespace = trim($m[1], " \t\\");
        }
        if (preg_match('/\bclass\s+([A-Za-z_][A-Za-z0-9_]*)/', $code_line, $m)) {
            $ownerClass = $m[1];
            $ownerFqn = $namespace === '' ? $ownerClass : $namespace . '\\' . $ownerClass;
            $token = $m[1];
            if (camel_case_repo_owned($token)) {
                $failures[] = "{$rel}: camelCase repo-owned class '{$token}'";
            }
        }
        if (preg_match('/function\s+&?\s*([A-Za-z_][A-Za-z0-9_]*)\s*\(/', $code_line, $m)) {
            $token = $m[1];
            if (str_starts_with($token, 'test')) {
                continue;
            }
            if (camel_case_repo_owned($token)
                && !camel_case_method_allowed(
                    $repoRoot,
                    $rel,
                    $ownerClass,
                    $token,
                    $contents,
                    $absPath,
                    $allowRules,
                    $ownerFqn
                )) {
                $failures[] = "{$rel}: camelCase repo-owned method '{$token}'";
            }
        }
        if (preg_match('/\$(\w+)/', $code_line, $m)) {
            $token = $m[1];
            if (camel_case_repo_owned($token) && !is_allowed_at_site($rel, $ownerClass, 'property', $token, $allowRules)) {
                $failures[] = "{$rel}: camelCase repo-owned property '\${$token}'";
            }
        }
    }

    return $failures;
}

/**
 * @param list<array{path: string, class: string, member: string}> $allowRules
 */
function scan_camel_case(string $repoRoot, array $allowRules): array
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
            $absPath = $file->getPathname();
            $rel = str_replace('\\', '/', substr($absPath, strlen($repoRoot) + 1));
            if (str_contains($rel, '/templates_c/') || preg_match('#/cache/[^/]+\.tpl\.php$#', $rel)) {
                continue;
            }
            $contents = file_get_contents($absPath);
            if ($contents === false) {
                continue;
            }
            $failures = array_merge(
                $failures,
                scan_camel_case_contents($repoRoot, $rel, $contents, $allowRules, $absPath)
            );
        }
    }

    return $failures;
}

$patterns = load_lines($patternFile);
$allowRules = parse_allowlist_entries(load_lines($allowlistFile));

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
    $repoRoot = dirname(__DIR__);
    if (scan_test_class_basenames($repoRoot) !== []) {
        fwrite(STDERR, "check-rename-residue: class-mismatch demo expected clean tree before synthetic inject.\n");
        exit(1);
    }
    $demoRel = 'tests/UnitTests/_Residue_Demo_Class_Mismatch_Test.php';
    $demoContents = "<?php\n\nclass Wrong_Basename_Test extends PHPUnit_Smarty {}\n";
    $mismatch = test_class_basename_mismatch($demoRel, $demoContents);
    if ($mismatch === null) {
        fwrite(STDERR, "check-rename-residue: class-mismatch demo should detect injected basename mismatch.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-rename-residue: demo class/file basename mismatch detected as expected.\n");
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
if ($demo === '--demo-allowlist-site') {
    $repoRoot = dirname(__DIR__);
    $okRel = 'tests/_Residue_Allowlist_Demo_Ok.php';
    $badRel = 'tests/_Residue_Allowlist_Demo_Bad.php';
    $class = 'Allowlist_Demo_Fixture';
    $otherClass = 'Allowlist_Demo_Other_Fixture';
    $method = 'siteScopedCamel';
    $demoRules = parse_allowlist_entries(["{$okRel} {$class}::{$method}"]);
    $snippet = "<?php\nclass {$class} {\n    public function {$method}() {}\n}\n";
    if (scan_camel_case_contents($repoRoot, $okRel, $snippet, $demoRules) !== []) {
        fwrite(STDERR, "check-rename-residue: allowlist demo should accept token at listed site.\n");
        exit(1);
    }
    if (scan_camel_case_contents($repoRoot, $badRel, $snippet, $demoRules) === []) {
        fwrite(STDERR, "check-rename-residue: allowlist demo should reject same token at unlisted file.\n");
        exit(1);
    }
    $sameFileSnippet = "<?php\nclass {$class} {\n    public function {$method}() {}\n}\n"
        . "class {$otherClass} {\n    public function {$method}() {}\n}\n";
    if (scan_camel_case_contents($repoRoot, $okRel, $sameFileSnippet, $demoRules) === []) {
        fwrite(STDERR, "check-rename-residue: allowlist demo should reject same token on unlisted class in listed file.\n");
        exit(1);
    }
    $hookSnippet = "<?php\nclass Not_A_Test_Fixture {\n    public function setUp(): void {}\n}\n";
    if (scan_camel_case_contents($repoRoot, $okRel, $hookSnippet, $demoRules) === []) {
        fwrite(STDERR, "check-rename-residue: allowlist demo should reject PHPUnit hook name without PHPUnit parent.\n");
        exit(1);
    }
    fwrite(STDOUT, "check-rename-residue: demo site-scoped allowlist accepted listed site and rejected other file/class/hook.\n");
    exit(0);
}
if ($demo === '--demo-camel') {
    $probe = 'repoOwnedCamelProbe';
    if (!camel_case_repo_owned($probe) || is_allowed_at_site('tests/fake.php', null, 'method', $probe, $allowRules)) {
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

$failures = array_merge($failures, scan_camel_case($repoRoot, $allowRules));

foreach (array_unique($failures) as $failure) {
    fwrite(STDERR, "check-rename-residue: {$failure}\n");
}

if ($failures !== []) {
    exit(1);
}

fwrite(STDOUT, "check-rename-residue: no old repo-owned names remain.\n");
exit(0);
