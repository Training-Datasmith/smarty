<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of generic getter/setter
 *

 * @author  Uwe Tews
 */

/**
 * class for generic getter/setter tests
 *
 *
 *
 *
 */
class Getter_Setter_Test extends PHPUnit_Smarty
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
     * test setter on Smarty object
     */
    public function testSmartySetter()
    {
        $this->smarty->set_left_delimiter('<{');
        $this->smarty->set_right_delimiter('}>');
        $this->assertEquals('<{', $this->smarty->get_left_delimiter());
        $this->assertEquals('}>', $this->smarty->get_right_delimiter());
    }

    /**
     * test getter on Smarty object
     */
    public function testSmartyGetter()
    {
        $this->smarty->set_left_delimiter('<{');
        $this->smarty->set_right_delimiter('}>');
        $this->assertEquals('<{', $this->smarty->get_left_delimiter());
        $this->assertEquals('}>', $this->smarty->get_right_delimiter());
    }

    /**
     * test setter on Template object
     */
    public function testTemplateSetter()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $tpl->set_left_delimiter('<{');
        $tpl->set_right_delimiter('}>');
        $this->assertEquals('<{', $tpl->get_left_delimiter());
        $this->assertEquals('}>', $tpl->get_right_delimiter());
        $this->assertEquals('{', $this->smarty->get_left_delimiter());
        $this->assertEquals('}', $this->smarty->get_right_delimiter());
    }

    /**
     * test getter on Template object
     */
    public function testTemplateGetter()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $tpl->set_left_delimiter('<{');
        $tpl->set_right_delimiter('}>');
        $this->assertEquals('<{', $tpl->get_left_delimiter());
        $this->assertEquals('}>', $tpl->get_right_delimiter());
    }
}
