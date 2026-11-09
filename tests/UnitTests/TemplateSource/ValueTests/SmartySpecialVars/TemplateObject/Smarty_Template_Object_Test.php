<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests {$smarty.template_objects}
 *

 * @author  Uwe Tews
 */

/**
 * class for {$smarty.template_objects} tests
 *
 *
 *
 *
 */
class Smarty_Template_Object_Test extends PHPUnit_Smarty
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
     * test {$smarty.template_objects}
     *
     */
    public function testSmartyTempalteObject()
    {
        $this->assertEquals('okay', $this->smarty->fetch('template_object.tpl'));
    }
}
