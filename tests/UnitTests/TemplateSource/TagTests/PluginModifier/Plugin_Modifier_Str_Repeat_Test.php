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
class Plugin_Modifier_Str_Repeat_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $tpl = $this->smarty->create_template('string:{$v|str_repeat:2}');
        $tpl->assign('v', 'foo');
        $this->assertEquals('foofoo', $this->smarty->fetch($tpl));
    }

    public function testZeroTimes()
    {
        $tpl = $this->smarty->create_template('string:{$v|str_repeat:0}');
        $tpl->assign('v', 'foo');
        $this->assertEquals('', $this->smarty->fetch($tpl));
    }
}
