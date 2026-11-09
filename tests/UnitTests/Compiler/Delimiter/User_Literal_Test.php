<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of delimiter
 *

 * @author  Uwe Tews
 */

/**
 * class for delimiter tests
 *
 *
 *
 *
 */
class User_Literal_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        if (!method_exists(\Smarty\Smarty::class, 'set_literals')) {
            $this->markTestSkipped('user literal support');
        } else {
            $this->set_up_smarty(__DIR__);
        }
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    public function testUserLiteral()
    {
        $this->smarty->set_auto_literal(true);
        $this->assertEquals('{{ 1 }}', $this->smarty->fetch('userliteral.tpl'));
    }
    public function testUserLiteral1()
    {
        $this->smarty->set_auto_literal(false);
        $this->smarty->set_compile_id(1);
        $this->assertEquals('1', $this->smarty->fetch('userliteral.tpl'));
    }
    public function testUserLiteral2()
    {
        $this->smarty->set_auto_literal(false);
        $this->smarty->set_literals(['{{','}}']);
        $this->assertEquals('{{1}}', $this->smarty->fetch('userliteral1.tpl'));
    }
    public function testUserLiteral3()
    {
        $this->smarty->set_auto_literal(false);
        $this->smarty->set_left_delimiter('<-');
        $this->smarty->set_right_delimiter('->');
        $this->smarty->set_literals(['<--','-->']);
        $this->assertEquals('1 <--1-->', $this->smarty->fetch('userliteral2.tpl'));
    }
    public function testUserLiteral4()
    {
        $this->smarty->set_auto_literal(true);
        $this->smarty->set_left_delimiter('<-');
        $this->smarty->set_right_delimiter('->');
        $this->smarty->set_compile_id(1);
        $this->smarty->set_literals(['<--','-->']);
        $this->assertEquals('<- 1 -> <--1-->', $this->smarty->fetch('userliteral2.tpl'));
    }
    public function testUserLiteral5()
    {
        $this->smarty->set_auto_literal(true);
        $this->smarty->set_literals(['{%']);
        $this->assertEquals(' output: double {%counter} quote', $this->smarty->fetch('userliteraldoublequote.tpl'));
    }
}
