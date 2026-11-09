<?php

declare(strict_types=1);

/**
 * Ensure PSR-4 paths match namespaces for stub autoloading via libs/Smarty.class.php.
 */
class Smarty_Stub_Autoload_Test extends PHPUnit_Smarty
{
    public function test_smarty_class_stub_loads_three_renamed_namespace_trees(): void
    {
        $root = dirname(__DIR__, 3);
        $probe = $root . '/tests/UnitTests/A_Core/stub_autoload_probe.php';
        $this->assertFileExists($probe);

        $cmd = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($probe);
        $output = [];
        $status = 0;
        exec($cmd . ' 2>&1', $output, $status);
        $this->assertSame(0, $status, implode("\n", $output));
    }
}
