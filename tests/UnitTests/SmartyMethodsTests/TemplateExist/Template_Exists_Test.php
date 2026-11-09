<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for template_exists method
 *

 * @author  Uwe Tews
 */

/**
 * class for template_exists tests
 *
 *
 *
 *
 */
class Template_Exists_Test extends PHPUnit_Smarty
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
     * test $smarty->template_exists true
     */
    public function testSmartyTemplateExists()
    {
        $this->assertTrue($this->smarty->template_exists('helloworld.tpl'));
    }

    /**
     * test $smarty->template_exists false
     */
    public function testSmartyTemplateNotExists()
    {
        $this->assertFalse($this->smarty->template_exists('notthere.tpl'));
    }
}
