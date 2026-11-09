<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests register/unregister function plugins
 *

 * @author  Uwe Tews
 */

use Smarty\Smarty;

/**
 * class for register/unregister function plugins methods tests
 *
 *
 * @preserveGlobalState    disabled
 *
 *
 */
class RegisterFunctionTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
    }

    public function testInit()
    {
        $this->cleanDirs();
    }

    /**
     * test register_plugin method for function
     */
    public function testRegisterFunction()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction', 'myfunction');
        $this->assertEquals(
            'myfunction',
            $this->smarty->get_registered_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction')[0]
        );
        $this->assertEquals('hello world 1', $this->smarty->fetch('eval:{testfunction value=1}'));
    }

    /**
     * test register_plugin method for function case-sensitive
     */
    public function testRegisterFunctionCaseInsensitive()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_FUNCTION, 'testFunction', 'myfunction');
        $this->assertEquals(
            'myfunction',
            $this->smarty->get_registered_plugin(Smarty::PLUGIN_FUNCTION, 'testFunction')[0]
        );
        $this->assertEquals('hello world 1', $this->smarty->fetch('eval:{testFunction value=1}'));
    }

    /**
     * test register_plugin method for function  class
     */
    public function testRegisterFunctionClass()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction', ['myfunctionclass', 'execute']);
        $this->assertEquals('hello world 2', $this->smarty->fetch('eval:{testfunction value=2}'));
    }

    /**
     * test register_plugin method for function object
     */
    public function testRegisterFunctionObject()
    {
        $myfunction_object = new myfunctionclass();
        $this->smarty->register_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction', [$myfunction_object, 'execute']);
        $this->assertEquals('hello world 3', $this->smarty->fetch('eval:{testfunction value=3}'));
    }

    /**
     * test register_plugin method for function cached
     *
     *
     *
     */
    public function testRegisterFunctionCaching1()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_force_compile(true);
        $this->smarty->assign('x', 0);
        $this->smarty->assign('y', 10);
        $this->smarty->register_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction', 'myfunction');
        $this->assertEquals('hello world 0 10', $this->smarty->fetch('test_register_function.tpl'));
    }

    /**
     * test register_plugin method for function cached
     *
     *
     *
     *
     */
    public function testRegisterFunctionCaching2()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->assign('x', 1);
        $this->smarty->assign('y', 20);
        $this->smarty->register_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction', 'myfunction');
        $this->assertEquals('hello world 0 10', $this->smarty->fetch('test_register_function.tpl'));
    }

    /**
     * test register_plugin method for function not cached
     *
     *
     *
     *
     */
    public function testRegisterFunctionCaching3()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_force_compile(true);
        $this->smarty->assign('x', 2);
        $this->smarty->assign('y', 30);
        $this->smarty->register_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction', 'myfunction', false);
        $this->assertEquals('hello world 2 30', $this->smarty->fetch('test_register_function.tpl'));
    }

    /**
     * test register_plugin method for function not cached
     *
     *
     *
     *
     */
    public function testRegisterFunctionCaching4()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->assign('x', 3);
        $this->smarty->assign('y', 40);
        $this->smarty->register_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction', 'myfunction', false);
        $this->assertEquals('hello world 3 30', $this->smarty->fetch('test_register_function.tpl'));
    }

    /**
     * test unregister_plugin method for function
     */
    public function testUnregisterFunction()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction', 'myfunction');
        $this->smarty->unregister_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction');
        $this->assertNull($this->smarty->get_registered_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction'));
    }

    /**
     * test unregister_plugin method for function not registered
     */
    public function testUnregisterFunctionNotRegistered()
    {
        $this->smarty->unregister_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction');
        $this->assertNull($this->smarty->get_registered_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction'));
    }

    /**
     * test unregister_plugin method for function other registered
     */
    public function testUnregisterFunctionOtherRegistered()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testfunction', 'myfunction');
        $this->smarty->unregister_plugin(Smarty::PLUGIN_FUNCTION, 'testfunction');
        $this->assertIsArray($this->smarty->get_registered_plugin(Smarty::PLUGIN_BLOCK, 'testfunction'));
    }

    /**
     * Test case (in)sensitivy of plugin functions
     * @param $registerName
     * @param $templateString
     * @return void
     * @throws \Smarty\Exception
     * @group issue907
     * @dataProvider dataProviderForCaseSensitivity
     */
    public function testCaseSensitivity($registerName, $templateString)
    {
        $this->smarty->register_plugin(
            Smarty::PLUGIN_FUNCTION,
            $registerName,
            function ($params, $smarty) {
                return 'function-output';
            }
        );
        $this->assertEquals('function-output', $this->smarty->fetch('string:' . $templateString));
    }

    public function dataProviderForCaseSensitivity()
    {
        return [
            ['customTag', '{customTag}'],
            ['customtag', '{customtag}'],
        ];
    }

    /**
     * test register_plugin for function name ending in 'close' #1122
     */
    public function testRegisterFunctionEndingInClose()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_FUNCTION, 'window_close', 'myfunction');
        $this->assertEquals('hello world 1', $this->smarty->fetch('eval:{window_close value=1}'));
    }

}

function myfunction($params, $smarty)
{
    return "hello world $params[value]";
}

class myfunctionclass
{
    public static function execute($params, $smarty)
    {
        return "hello world $params[value]";
    }
}
