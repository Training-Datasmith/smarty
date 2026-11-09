<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compiler plugin
 *

 * @author  Uwe Tews
 */

/**
 * class for compiler plugin tests
 */
class Compiler_Plugin_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testInit()
    {
        $this->clean_dirs();
    }
    /**
     * test compiler plugin
     */
    public function testCompilerPlugin()
    {
        $this->load_plugins_dir_for_tests(__DIR__ . '/PHPunitplugins/');
        $this->assertEquals('test output', $this->smarty->fetch('eval:{test data="test output"}{/test}'));
    }
}
