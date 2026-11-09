<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of modifier
 */

/**
 * class for modifier tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Plugin_Modifier_Nl2br_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $tpl = $this->smarty->create_template('string:{$v|nl2br}');
        $tpl->assign('v', "Line1\nLine2");
        $this->assertEquals("Line1<br />\nLine2", $this->smarty->fetch($tpl));
    }

    public function testNoXHTML()
    {
        $tpl = $this->smarty->create_template('string:{$v|nl2br:false}');
        $tpl->assign('v', "Line1\nLine2");
        $this->assertEquals("Line1<br>\nLine2", $this->smarty->fetch($tpl));
    }
}
