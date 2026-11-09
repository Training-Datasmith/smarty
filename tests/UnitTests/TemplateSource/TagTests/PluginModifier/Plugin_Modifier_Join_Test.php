<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of modifier
 */

namespace UnitTests\TemplateSource\TagTests\PluginModifier;

use PHPUnit_Smarty;
class Plugin_Modifier_Join_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $tpl = $this->smarty->create_template('string:{$v|join}');
        $tpl->assign('v', ['1', '2']);
        $this->assertEquals('12', $this->smarty->fetch($tpl));
    }
    public function testWithSeparator()
    {
        $tpl = $this->smarty->create_template('string:{$v|join:","}');
        $tpl->assign('v', ['a', 'b']);
        $this->assertEquals('a,b', $this->smarty->fetch($tpl));
    }

    public function testInConditional()
    {
        $tpl = $this->smarty->create_template('string:{if join($v) == "abc"}good{else}bad{/if}');
        $tpl->assign('v', ['a','b','c']);
        $this->assertEquals('good', $this->smarty->fetch($tpl));
    }

    public function testInConditionalWithSeparator()
    {
        $tpl = $this->smarty->create_template('string:{if join($v, "-") == "a-b-c"}good{else}bad{/if}');
        $tpl->assign('v', ['a','b','c']);
        $this->assertEquals('good', $this->smarty->fetch($tpl));
    }

}
