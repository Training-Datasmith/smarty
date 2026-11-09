<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests appendByRef method
 *

 * @author  Uwe Tews
 */

/**
 * class for appendByRef tests
 *
 *
 *
 *
 */
class LiteralTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
    }

    public function testInit()
    {
        $this->cleanDirs();
    }
    /*
     *  Test literal tag
     */
    public function testLiteralTag()
    {
        $tpl = $this->smarty->create_template('string:{literal} {$foo} {/literal}');
        $this->assertEquals(' {$foo} ', $this->smarty->fetch($tpl));
    }

    /*
    *  Test auto literal space
    */
    public function testAutoLiteralSpace()
    {
        $tpl = $this->smarty->create_template('string: { $foo} ');
        $tpl->assign('foo', 'literal');
        $this->assertEquals(' { $foo} ', $this->smarty->fetch($tpl));
    }

    /*
    *  Test auto literal line break
    */
    public function testAutoLiteralLineBreak()
    {
        $tpl = $this->smarty->create_template("string: {\n\$foo} ");
        $tpl->assign('foo', 'literal');
        $this->assertEquals(" {\n\$foo} ", $this->smarty->fetch($tpl));
    }

    /*
    *  Test auto literal disabled
    */
    public function testAutoLiteralDisabled()
    {
        $this->smarty->set_auto_literal(false);
        $tpl = $this->smarty->create_template('string:  { $foo} ');
        $tpl->assign('foo', 'literal');
        $this->assertEquals('  literal ', $this->smarty->fetch($tpl));
    }
}
