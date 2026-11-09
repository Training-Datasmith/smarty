<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for tag attributes
 *

 * @author  Uwe Tews
 */

/**
 * class for tag attribute tests
 *
 *
 *
 *
 */
class Attribute_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->load_plugins_dir_for_tests('../../../__shared/PHPunitplugins/');
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    /**
     * test required attribute
     */
    public function testRequiredAttributeVar()
    {
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('missing \'var\' attribute');
        $this->smarty->fetch('string:{assign value=1}');
    }

    /**
     * test unexpected attribute
     */
    public function testUnexpectedAttribute()
    {
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('unexpected \'bar\' attribute');
        $this->smarty->fetch('string:{assign var=foo value=1 bar=2}');
    }

    /**
     * test illegal option value
     */
    public function testIllegalOptionValue()
    {
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('for option flag \'nocache\'');
        $this->expectExceptionMessage('illegal value');
        $this->smarty->fetch('string:{assign var=foo value=1 nocache=buh}');
    }

    /**
     * test too many shorthands
     */
    public function testTooManyShorthands()
    {
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('too many shorthand attributes');
        $this->smarty->fetch('string:{assign foo 1 2}');
    }

    /**
      */
    public function testNumericParams()
    {
        $this->assertEquals('array(\'a\'=>\'pa\',0=>\'isnull\',)', $this->strip($this->smarty->fetch('string:{getparams a=\'pa\' 0=isnull}')));
    }

}
