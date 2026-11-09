<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of function calls
 *

 * @author  Uwe Tews
 */

/**
 * class for function tests
 *
 *
 *
 *
 */
class Function_Test extends PHPUnit_Smarty
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
     * test unknown function error
     */
    public function testUnknownFunction()
    {
        $this->smarty->enable_security();
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('unknown modifier');
        $this->smarty->fetch('eval:{unknown()}');
    }
}
