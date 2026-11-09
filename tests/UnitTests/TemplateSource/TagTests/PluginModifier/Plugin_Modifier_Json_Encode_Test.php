<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of modifier
 */

namespace UnitTests\TemplateSource\TagTests\PluginModifier;

use PHPUnit_Smarty;
class Plugin_Modifier_Json_Encode_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    /**
     * @dataProvider data_for_default
     */
    public function testDefault($value, $expected)
    {
        $tpl = $this->smarty->create_template('string:{$v|json_encode}');
        $tpl->assign('v', $value);
        $this->assertEquals($expected, $this->smarty->fetch($tpl));
    }

    /**
     * @dataProvider data_for_default
     */
    public function testDefaultAsFunction($value, $expected)
    {
        $tpl = $this->smarty->create_template('string:{json_encode($v)}');
        $tpl->assign('v', $value);
        $this->assertEquals($expected, $this->smarty->fetch($tpl));
    }

    public function data_for_default()
    {
        return [
            ['abc', '"abc"'],
            [['abc'], '["abc"]'],
            [['abc',['a' => 2]], '["abc",{"a":2}]'],
        ];
    }

    /**
     * @dataProvider data_for_force_object
     */
    public function testForceObject($value, $expected)
    {
        $tpl = $this->smarty->create_template('string:{$v|json_encode:16}');
        $tpl->assign('v', $value);
        $this->assertEquals($expected, $this->smarty->fetch($tpl));
    }

    /**
     * @dataProvider data_for_force_object
     */
    public function testForceObjectAsFunction($value, $expected)
    {
        $tpl = $this->smarty->create_template('string:{json_encode($v,16)}');
        $tpl->assign('v', $value);
        $this->assertEquals($expected, $this->smarty->fetch($tpl));
    }

    public function data_for_force_object()
    {
        return [
            ['abc', '"abc"'],
            [['abc'], '{"0":"abc"}'],
            [['abc',['a' => 2]], '{"0":"abc","1":{"a":2}}'],
        ];
    }

}
