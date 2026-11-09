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
class TemplateExistsTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
    }

    public function testInit()
    {
        $this->cleanDirs();
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
