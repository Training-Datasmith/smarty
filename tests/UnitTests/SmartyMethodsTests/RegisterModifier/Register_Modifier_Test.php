<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests register->modifier / unregister->modifier methods
 *

 * @author  Uwe Tews
 */

/**
 * class for register->modifier / unregister->modifier methods tests
 *
 *
 *
 *
 */
class Register_Modifier_Test extends PHPUnit_Smarty
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
     * test register->modifier method for function
     */
    public function testRegisterModifier()
    {
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier', 'mymodifier');
        $this->assertEquals('mymodifier', $this->smarty->get_registered_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier')[0]);
        $this->smarty->assign('foo', 'foo');
        $this->smarty->assign('bar', 'bar');
        $this->assertEquals('foo function blar bar', $this->smarty->fetch('eval:{$foo|testmodifier:blar:$bar}'));
    }

    /**
     * test register->modifier method for classes
     */
    public function testRegisterModifierClass()
    {
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier', ['mymodifierclass', 'static_method']);
        $this->smarty->assign('foo', 'foo');
        $this->smarty->assign('bar', 'bar');
        $this->assertEquals('foo static blar bar', $this->smarty->fetch('eval:{$foo|testmodifier:blar:$bar}'));
    }

    /**
     * test register->modifier method for objects
     */
    public function testRegisterModifierObject()
    {
        $obj = new mymodifierclass();
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier', [$obj, 'object_method']);
        $this->smarty->assign('foo', 'foo');
        $this->smarty->assign('bar', 'bar');
        $this->assertEquals('foo object blar bar', $this->smarty->fetch('eval:{$foo|testmodifier:blar:$bar}'));
    }

    /**
     * test unregister->modifier method
     */
    public function testUnregisterModifier()
    {
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier', 'mymodifier');
        $this->smarty->unregister_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier');
        $this->assertNull($this->smarty->get_registered_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier'));
    }

    /**
     * test unregister->modifier method not registered
     */
    public function testUnregisterModifierNotRegistered()
    {
        $this->smarty->unregister_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier');
        $this->assertNull($this->smarty->get_registered_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier'));
    }

    /**
     * test unregister->modifier method other registered
     */
    public function testUnregisterModifierOtherRegistered()
    {
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_BLOCK, 'testmodifier', 'mymodifier');
        $this->smarty->unregister_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier');
        $this->assertIsArray($this->smarty->get_registered_plugin(\Smarty\Smarty::PLUGIN_BLOCK, 'testmodifier'));
    }

    /**
     * test cannot call native PHP fuctions by default
     * @dataProvider data_unknown_modifiers
     */
    public function testNativePHPModifiers($template, $expected_Value)
    {
        $this->clean_dirs();
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('unknown modifier');
        $this->smarty->fetch('string:' . $template);
    }

    public function data_unknown_modifiers(): array
    {
        return [
            ['{" blah"|ltrim:" "}', 'blah'],
            ['{"blah"|strrev}', 'halb'],
            ['{"blah"|ucfirst}', 'Blah'],
            ['{"blah"|md5}', md5('blah')],
        ];
    }

    /**
     * test register wildcard modifier using extension
     * @dataProvider data_unknown_modifiers
     */
    public function testUnregisterModifiers($template, $expected_Value)
    {
        $this->clean_dirs();
        $this->smarty->add_extension(new Wildcard_Extension());
        $this->assertEquals($expected_Value, $this->smarty->fetch('string:' . $template));
    }

    /**
     * test register wildcard modifier using set_extensions
     * @dataProvider data_unknown_modifiers
     */
    public function testSetExtensions($template, $expected_Value)
    {
        $this->clean_dirs();
        $this->smarty->set_extensions([
            new \Smarty\Extension\Core_Extension(),
            new Wildcard_Extension(),
        ]);
        $this->assertEquals($expected_Value, $this->smarty->fetch('string:' . $template));
    }

    public function testRegisterNativePhpFuncAsString()
    {
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'strrev', 'strrev');
        $this->smarty->assign('my_var', 'andersom');
        $this->assertEquals('mosredna', $this->smarty->fetch('string:{strrev($my_var)}'));
    }

    public function testRegisterNativePhpFuncUnderDifferentName()
    {
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'k_xyz_a', 'strrev');
        $this->smarty->assign('my_var', 'andersom');
        $this->assertEquals('mosredna', $this->smarty->fetch('string:{k_xyz_a($my_var)}'));
    }

}

class Wildcard_Extension extends \Smarty\Extension\Base
{
    public function get_modifier_callback(string $modifier_Name)
    {
        if (is_callable($modifier_Name)) {
            return $modifier_Name;
        }
        return null;
    }

}

function mymodifier($a, $b, $c)
{
    return "$a function $b $c";
}

class mymodifierclass
{
    public static function static_method($a, $b, $c)
    {
        return "$a static $b $c";
    }

    public function object_method($a, $b, $c)
    {
        return "$a object $b $c";
    }
}
