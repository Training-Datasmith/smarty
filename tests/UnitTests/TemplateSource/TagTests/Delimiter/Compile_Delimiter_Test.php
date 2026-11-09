<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compilation of delimiter tags
 *

 * @author  Uwe Tews
 */

/**
 * class for delimiter tags tests
 *
 * @run TestsInSeparateProcess
 *
 *
 */
class Compile_Delimiter_Test extends PHPUnit_Smarty
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
     * test delimiter tag test
     */
    public function testLeftDelimiter()
    {
        $tpl = $this->smarty->create_template('string:x{ldelim}x');
        $this->assertEquals('x{x', $this->smarty->fetch($tpl));
    }

    public function testRightDelimiter()
    {
        $tpl = $this->smarty->create_template('string:x{rdelim}x');
        $this->assertEquals('x}x', $this->smarty->fetch($tpl));
    }

    public function testLeftDelimiterError()
    {
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('nocache option not allowed');
        $tpl = $this->smarty->create_template('string:x{ldelim nocache}x');
        $this->assertEquals('x{x', $this->smarty->fetch($tpl));
    }

}
