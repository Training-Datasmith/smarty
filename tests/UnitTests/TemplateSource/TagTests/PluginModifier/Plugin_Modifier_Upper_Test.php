<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of modifier
 *

 * @author  Rodney Rehm
 */

/**
 * class for modifier tests
 *
 *
 *
 *
 */
class Plugin_Modifier_Upper_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $result = "IF STRIKE ISN'T SETTLED QUICKLY IT MAY LAST A WHILE.";
        $tpl = $this->smarty->create_template('string:{"If Strike isn\'t Settled Quickly it may Last a While."|upper}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

    public function testUmlauts()
    {
        $result = "IF STRIKE ISN'T SÄTTLED ÜQUICKLY IT MAY LAST A WHILE.";
        $tpl = $this->smarty->create_template('string:{"If Strike isn\'t Sättled ÜQuickly it may Last a While."|upper}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

}
