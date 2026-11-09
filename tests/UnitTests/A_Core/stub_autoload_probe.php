<?php

declare(strict_types=1);

/**
 * Standalone probe: load Smarty via libs/Smarty.class.php only (no Composer autoload).
 */

$repo_root = dirname(__DIR__, 3);
require $repo_root . '/libs/Smarty.class.php';

$required = [
    'Smarty\\Block_Handler\\Base',
    'Smarty\\Function_Handler\\Base',
    'Smarty\\Parse_Tree\\Base',
];

foreach ($required as $class) {
    if (!class_exists($class)) {
        fwrite(STDERR, "stub autoload probe: class not loaded: {$class}\n");
        exit(1);
    }
}

exit(0);
