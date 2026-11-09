<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compiler errors
 *

 * @author  Uwe Tews
 */

/**
 * class for compiler tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Compile_Error_Test extends PHPUnit_Smarty
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
     * test none existing template file error
     */
    public function testNoneExistingTemplateError()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('no.tpl');
        $this->smarty->fetch('eval:{include file=\'no.tpl\'}');
    }

    /**
     * test unkown tag error
     */
    public function testUnknownTagError()
    {
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('unknown tag \'unknown\'');
        $this->smarty->fetch('eval:{unknown}');
    }

    /**
     * test unclosed tag error
     */
    public function testUnclosedTagError()
    {
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('unclosed {if} tag');
        $this->smarty->fetch('eval:{if true}');
    }

    /**
     * test syntax error
     */
    public function testSyntaxError()
    {
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('Unexpected "}"');
        $this->smarty->fetch('eval:{assign var=}');
    }
}
