<?php

declare(strict_types=1);
/*
* This file is part of the Smarty PHPUnit tests.
*
*/
/*
 * Smarty PHPUnit Config
 */
define('individualFolders', true);

/**
 * @param non-empty-string $name
 */
function smarty_test_env_enabled(string $name): bool
{
    $value = getenv($name);
    if ($value === false || $value === '') {
        return false;
    }

    return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
}

$pdoCacheTests = smarty_test_env_enabled('SMARTY_ENABLE_PDO_CACHE_TESTS');

define('MysqlCacheEnable', false);
define('PdoCacheEnable', $pdoCacheTests);
define('PdoGzipCacheEnable', $pdoCacheTests);
define('MysqlResourceEnable', false);
define('DB_DSN', getenv('SMARTY_TEST_DB_DSN') ?: 'mysql:dbname=test;host=localhost');
define('DB_USER', getenv('SMARTY_TEST_DB_USER') ?: 'root');
define('DB_PASSWD', getenv('SMARTY_TEST_DB_PASSWD') !== false ? getenv('SMARTY_TEST_DB_PASSWD') : '');
