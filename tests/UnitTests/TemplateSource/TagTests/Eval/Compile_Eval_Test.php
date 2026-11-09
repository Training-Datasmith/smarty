<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compilation of {eval} tag
 *

 * @author  Uwe Tews
 */

/**
 * class for {eval} tag tests
 *
 *
 *
 *
 */
class Compile_Eval_Test extends PHPUnit_Smarty
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
     * test eval tag
     */
    public function testEval1()
    {
        $tpl = $this->smarty->create_template("string:{eval var='hello world'}");
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }

    public function testEval2()
    {
        $tpl = $this->smarty->create_template("string:{eval var='hello world' assign=foo}{\$foo}");
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }

    public function testEval3()
    {
        $tpl = $this->smarty->create_template("string:{eval var='hello world' assign=foo}");
        $this->assertEquals('', $this->smarty->fetch($tpl));
    }
}
