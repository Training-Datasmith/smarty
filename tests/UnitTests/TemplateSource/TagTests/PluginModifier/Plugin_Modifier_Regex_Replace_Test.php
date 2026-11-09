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
class Plugin_Modifier_Regex_Replace_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $tpl = $this->smarty->create_template('string:{"Infertility unlikely to\nbe passed on, experts say."|regex_replace:"/[\r\t\n]/":" "}');
        $this->assertEquals('Infertility unlikely to be passed on, experts say.', $this->smarty->fetch($tpl));
    }

    public function testUmlautsNewlines()
    {
        $tpl = $this->smarty->create_template('string:{"Infertility unlikely tö\näe passed on, experts say."|regex_replace:"/[\r\t\n]/u":" "}');
        $this->assertEquals('Infertility unlikely tö äe passed on, experts say.', $this->smarty->fetch($tpl));
    }

    public function testUmlautsReplace()
    {
        $tpl = $this->smarty->create_template('string:{"Infertility unlikely tä be passed on, experts say."|regex_replace:"#[ä]#u":"ae"}');
        $this->assertEquals('Infertility unlikely tae be passed on, experts say.', $this->smarty->fetch($tpl));
    }
}
