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
class Modifier_Issue327Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->register_plugin('modifier', 'substr', 'substr');
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    public function testModifier327()
    {
        $this->assertEquals('hello you', $this->smarty->fetch('string:{"hello world"|substr:0:-5|cat:"you"}'));
    }

}
