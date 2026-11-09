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
 */
class AutoliteralTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
        $this->smarty->add_plugins_dir('../../__shared/PHPunitplugins/');
    }

    public function testInit()
    {
        $this->cleanDirs();
    }

    /**
     * test '{ ' delimiter
     */
    public function testSetAutoliteral()
    {
        $this->smarty->set_auto_literal(true);
        $this->smarty->assign('i', 'foo');
        $this->assertEquals('{ $i}foo', $this->smarty->fetch('autoliteral.tpl'));
    }

    public function testSetAutoliteral2()
    {
        $this->smarty->set_auto_literal(false);
        $this->smarty->set_compile_id(1);
        $this->smarty->assign('i', 'bar');
        $this->assertEquals('barbar', $this->smarty->fetch('autoliteral.tpl'));
    }
    /**
     * test '{ ' delimiter in double quotes auto_literal true
     */
    public function testSetAutoliteralDoublequote()
    {
        $this->smarty->set_auto_literal(true);
        $this->assertEquals(' output: double { counter} 1 quote', $this->smarty->fetch('autoliteraldoublequote.tpl'));
    }
    /**
     * test '{ ' delimiter in double quotes auto_literal false
     */
    public function testSetAutoliteralDoublequote2()
    {
        $this->smarty->set_auto_literal(false);
        $this->smarty->set_compile_id(1);
        $this->assertEquals(' output: double 1 2 quote', $this->smarty->fetch('autoliteraldoublequote.tpl'));
    }

    /**
     * test '{{ ' delimiter in double quotes auto_literal true
     */
    public function testSetAutoliteralDoublequote3()
    {
        $this->smarty->set_auto_literal(true);
        $this->assertEquals(' output: double {{ counter} {{ counter}}  quote', $this->smarty->fetch('autoliteraldoublequote2.tpl'));
    }

    public function testSetAutoliteralBlock()
    {
        $this->smarty->set_auto_literal(true);
        $this->assertEquals('{ dummyblock}foo{ /dummyblock}', $this->smarty->fetch('autoliteralblock.tpl'));
    }
    public function testSetAutoliteralBlock1()
    {
        $this->smarty->set_auto_literal(false);
        $this->smarty->set_compile_id(1);
        $this->assertEquals('foo', $this->smarty->fetch('autoliteralblock.tpl'));
    }

}
