<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of modifier
 */

namespace UnitTests\TemplateSource\TagTests\PluginModifier;

use PHPUnit_Smarty;

class PluginModifierImplodeTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
    }
    /**
     * @deprecated
     */
    public function testDefault()
    {
        $tpl = $this->smarty->create_template('string:{$v|implode}');
        $tpl->assign('v', ['1', '2']);
        $this->assertEquals('12', $this->smarty->fetch($tpl));
    }
    /**
     * @deprecated
     */
    public function testWithSeparator()
    {
        $tpl = $this->smarty->create_template('string:{$v|implode:","}');
        $tpl->assign('v', ['a', 'b']);
        $this->assertEquals('a,b', $this->smarty->fetch($tpl));
    }
    /**
     * @deprecated
     */
    public function testLegacyArgumentOrder()
    {
        $tpl = $this->smarty->create_template('string:{","|implode:$v}');
        $tpl->assign('v', ['a', 'b']);
        $this->assertEquals('a,b', $this->smarty->fetch($tpl));
    }
    /**
     * @deprecated
     */
    public function testInConditional()
    {
        $tpl = $this->smarty->create_template('string:{if implode($v) == "abc"}good{else}bad{/if}');
        $tpl->assign('v', ['a','b','c']);
        $this->assertEquals('good', $this->smarty->fetch($tpl));
    }
    /**
     * @deprecated
     */
    public function testInConditionalWithSeparator()
    {
        $tpl = $this->smarty->create_template('string:{if implode($v, "-") == "a-b-c"}good{else}bad{/if}');
        $tpl->assign('v', ['a','b','c']);
        $this->assertEquals('good', $this->smarty->fetch($tpl));
    }

}
