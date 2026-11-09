<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of modifier
 */

namespace UnitTests\TemplateSource\TagTests\PluginModifier;

use PHPUnit_Smarty;

/**
 * class for modifier tests
 *
 * @runTestsInSeparateProcess
 * @preserveGlobalState disabled
 * @backupStaticAttributes enabled
 */
class Plugin_Modifier_Strip_Tags_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $tpl = $this->smarty->create_template('string:{$x|strip_tags}');
        $tpl->assign('x', '<b>hi</b>');
        $this->assertEquals(' hi ', $this->smarty->fetch($tpl));
    }

    public function testParam1()
    {
        $tpl = $this->smarty->create_template('string:{$x|strip_tags:false}');
        $tpl->assign('x', '<b>hi</b>');
        $this->assertEquals('hi', $this->smarty->fetch($tpl));
    }

    public function testInputIsFalsy0()
    {
        $tpl = $this->smarty->create_template('string:{$x|strip_tags}');
        $tpl->assign('x', 0);
        $this->assertEquals('0', $this->smarty->fetch($tpl));
    }

    public function testInputIsFalsy1()
    {
        $tpl = $this->smarty->create_template('string:{$x|strip_tags}');
        $tpl->assign('x', '');
        $this->assertEquals('', $this->smarty->fetch($tpl));
    }

}
