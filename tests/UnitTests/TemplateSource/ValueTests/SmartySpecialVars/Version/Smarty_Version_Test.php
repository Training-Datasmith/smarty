<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests {$smarty.version}
 *

 * @author  Uwe Tews
 */

/**
 * class for {$smarty.version} tests
 *
 *
 *
 *
 */
class Smarty_Version_Test extends PHPUnit_Smarty
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
     * test {$smarty.version}
     *
     */
    public function testSmartyVersion()
    {
        $this->assertEquals(\Smarty\Smarty::SMARTY_VERSION, $this->smarty->fetch('version.tpl'));
    }
}
