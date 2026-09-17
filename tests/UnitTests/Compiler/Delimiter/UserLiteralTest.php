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
class UserliteralTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        if (!method_exists(\Smarty\Smarty::class, 'setLiterals')) {
            $this->markTestSkipped('user literal support');
        } else {
            $this->setUpSmarty(__DIR__);
        }
    }

    public function testInit()
    {
        $this->cleanDirs();
    }

    public function testUserLiteral()
    {
        $this->smarty->setAutoLiteral(true);
        $this->assertEquals('{{ 1 }}', $this->smarty->fetch('userliteral.tpl'));
    }
    public function testUserLiteral1()
    {
        $this->smarty->setAutoLiteral(false);
        $this->smarty->setCompileId(1);
        $this->assertEquals('1', $this->smarty->fetch('userliteral.tpl'));
    }
    public function testUserLiteral2()
    {
        $this->smarty->setAutoLiteral(false);
        $this->smarty->setLiterals(['{{','}}']);
        $this->assertEquals('{{1}}', $this->smarty->fetch('userliteral1.tpl'));
    }
    public function testUserLiteral3()
    {
        $this->smarty->setAutoLiteral(false);
        $this->smarty->setLeftDelimiter('<-');
        $this->smarty->setRightDelimiter('->');
        $this->smarty->setLiterals(['<--','-->']);
        $this->assertEquals('1 <--1-->', $this->smarty->fetch('userliteral2.tpl'));
    }
    public function testUserLiteral4()
    {
        $this->smarty->setAutoLiteral(true);
        $this->smarty->setLeftDelimiter('<-');
        $this->smarty->setRightDelimiter('->');
        $this->smarty->setCompileId(1);
        $this->smarty->setLiterals(['<--','-->']);
        $this->assertEquals('<- 1 -> <--1-->', $this->smarty->fetch('userliteral2.tpl'));
    }
    public function testUserLiteral5()
    {
        $this->smarty->setAutoLiteral(true);
        $this->smarty->setLiterals(['{%']);
        $this->assertEquals(' output: double {%counter} quote', $this->smarty->fetch('userliteraldoublequote.tpl'));
    }
}
