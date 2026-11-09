<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests undefined Smarty special variable
 *

 * @author  Uwe Tews
 */

/**
 * class for undefined Smarty special variable tests
 *
 *
 *
 *
 */
class Smarty_Error_Test extends PHPUnit_Smarty
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
     * test undefined Smarty special variable
     */
    public function testSmartyError()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('$smarty.foo is not defined');
        $this->assertEquals(\Smarty\Smarty::SMARTY_VERSION, $this->smarty->fetch('error.tpl'));
    }
}
