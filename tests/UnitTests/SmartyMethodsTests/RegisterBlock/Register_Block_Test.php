<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests register->block / unregister->block methods
 *

 * @author  Uwe Tews
 */

use Smarty\Compiler\Template;
use Smarty\Smarty;

/**
 * class for register->block / unregister->block methods tests
 *
 *
 *
 *
 */
class RegisterBlockTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
        $this->smarty->disable_security();
    }

    public function testInit()
    {
        $this->cleanDirs();
    }
    /**
     * test register_plugin method for block function
     */
    public function testRegisterBlockFunction()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testblock', 'myblock');
        $this->smarty->assign('value', 1);
        $this->assertEquals('function hello world 1 1 function hello world 1 2 function hello world 1 3 ', $this->smarty->fetch('eval:{testblock}hello world {$value}{/testblock}'));
    }

    public function testRegisterBlockFunctionModifier1()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testblock', 'myblock');
        $this->smarty->assign('value', 1);
        $this->assertEquals(strtoupper('function hello world 1 1 function hello world 1 2 function hello world 1 3 '), $this->smarty->fetch('eval:{testblock}hello world {$value}{/testblock|upper}'));
    }

    public function testRegisterBlockFunctionModifier2()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testblock', 'myblock');
        $this->smarty->assign('value', 1);
        $this->assertEquals(strtoupper('function hello world 1 1 function hello world 1 2 function hello world 1 3 '), $this->smarty->fetch('eval:{testblock}hello world {$value}{/testblock|default:""|upper}'));
    }

    public function testRegisterBlockFunctionWrapper()
    {
        $this->smarty->register_plugin('block', 'testblock', 'myblock');
        $this->smarty->assign('value', 1);
        $this->assertEquals('function hello world 1 1 function hello world 1 2 function hello world 1 3 ', $this->smarty->fetch('eval:{testblock}hello world {$value}{/testblock}'));
    }

    /**
     * test register_plugin method for block class
     */
    public function testRegisterBlockClass()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testblock', ['myblockclass', 'static_method']);
        $this->smarty->assign('value', 2);
        $this->assertEquals('static hello world 2 1 static hello world 2 2 static hello world 2 3 ', $this->smarty->fetch('eval:{testblock}hello world {$value}{/testblock}'));
    }

    public function testRegisterBlockClassWrapper()
    {
        $this->smarty->register_plugin('block', 'testblock', ['myblockclass', 'static_method']);
        $this->smarty->assign('value', 2);
        $this->assertEquals('static hello world 2 1 static hello world 2 2 static hello world 2 3 ', $this->smarty->fetch('eval:{testblock}hello world {$value}{/testblock}'));
    }

    /**
     * test register_plugin method for block object
     */
    public function testRegisterBlockObject()
    {
        $myblock_object = new myblockclass();
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testblock', [$myblock_object, 'object_method']);
        $this->smarty->assign('value', 3);
        $this->assertEquals('object hello world 3 1 object hello world 3 2 object hello world 3 3 ', $this->smarty->fetch('eval:{testblock}hello world {$value}{/testblock}'));
    }

    public function testRegisterBlockObjectWrapper()
    {
        $myblock_object = new myblockclass();
        $this->smarty->register_plugin('block', 'testblock', [$myblock_object, 'object_method']);
        $this->smarty->assign('value', 3);
        $this->assertEquals('object hello world 3 1 object hello world 3 2 object hello world 3 3 ', $this->smarty->fetch('eval:{testblock}hello world {$value}{/testblock}'));
    }

    /**
     * test register_plugin method for block with caching
     */
    public function testRegisterBlockCaching1()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_force_compile(true);
        $this->smarty->assign('x', 1);
        $this->smarty->assign('y', 10);
        $this->smarty->assign('z', 100);
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testblock', 'myblockcache');
        $this->assertEquals('1 10 100', $this->smarty->fetch('test_register_block.tpl'));
    }

    /**
     *
     *
     *
     */
    public function testRegisterBlockCaching2()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->assign('x', 2);
        $this->smarty->assign('y', 20);
        $this->smarty->assign('z', 200);
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testblock', 'myblockcache');
        $this->assertEquals('1 10 100', $this->smarty->fetch('test_register_block.tpl'));
    }

    /**
     *
     *
     *
     */
    public function testRegisterBlockCaching3()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_force_compile(true);
        $this->smarty->assign('x', 3);
        $this->smarty->assign('y', 30);
        $this->smarty->assign('z', 300);
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testblock', 'myblockcache', false);
        $this->assertEquals('3 30 300', $this->smarty->fetch('test_register_block.tpl'));
    }

    /**
     *
     *
     *
     */
    public function testRegisterBlockCaching4()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testblock', 'myblockcache', false);

        $this->smarty->assign('x', 3);
        $this->smarty->assign('y', 30);
        $this->smarty->assign('z', 300);
        $this->assertEquals('3 30 300', $this->smarty->fetch('test_register_block.tpl'));

        $this->smarty->assign('x', 4);
        $this->smarty->assign('y', 40);
        $this->smarty->assign('z', 400);
        $this->assertEquals('3 40 300', $this->smarty->fetch('test_register_block.tpl'));
    }

    /**
     *
     *
     *
     */
    public function testRegisterBlockCaching1Wrapper()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_force_compile(true);
        $this->smarty->assign('x', 1);
        $this->smarty->assign('y', 10);
        $this->smarty->assign('z', 100);
        $this->smarty->register_plugin('block', 'testblock', 'myblockcache');
        $this->assertEquals('1 10 100', $this->smarty->fetch('test_register_block.tpl'));
    }

    /**
     *
     *
     *
     */
    public function testRegisterBlockCaching2Wrapper()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->assign('x', 2);
        $this->smarty->assign('y', 20);
        $this->smarty->assign('z', 200);
        $this->smarty->register_plugin('block', 'testblock', 'myblockcache');
        $this->assertEquals('1 10 100', $this->smarty->fetch('test_register_block.tpl'));
    }

    /**
     *
     *
     *
     */
    public function testRegisterBlockCaching3Wrapper()
    {
        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_force_compile(true);
        $this->smarty->assign('x', 3);
        $this->smarty->assign('y', 30);
        $this->smarty->assign('z', 300);
        $this->smarty->register_plugin('block', 'testblock', 'myblockcache', false);
        $this->assertEquals('3 30 300', $this->smarty->fetch('test_register_block.tpl'));
    }

    public function testRegisterBlockCaching4Wrapper()
    {
        $this->cleanDirs();

        $this->smarty->caching = 1;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->register_plugin('block', 'testblock', 'myblockcache');

        $this->smarty->assign('x', 3);
        $this->smarty->assign('y', 30);
        $this->smarty->assign('z', 300);
        $this->assertEquals('3 30 300', $this->smarty->fetch('test_register_block.tpl'));

        $this->smarty->assign('x', 4);
        $this->smarty->assign('y', 40);
        $this->smarty->assign('z', 400);
        $this->assertEquals('3 30 300', $this->smarty->fetch('test_register_block.tpl'));
    }

    /**
     * test register block with handler that supports positional params
     */
    public function testRegisterBlockWithPositionalParams()
    {
        $this->cleanDirs();
        $this->smarty->register_plugin(Smarty::PLUGIN_COMPILER, 'testblock', blockparamsCompiler::class);
        $this->smarty->register_plugin(Smarty::PLUGIN_COMPILER, 'testblockclose', blockparamsCompiler::class);
        $result = $this->smarty->fetch('string:{testblock "foo" "bar"} block 
		contents
		{/testblock}');
        $this->assertStringContainsString('first', $result);
        $this->assertStringContainsString('second', $result);
    }

    /**
     * test unregister->block method
     */
    public function testUnregisterBlock()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testblock', 'myblock');
        $this->smarty->unregister_plugin(Smarty::PLUGIN_BLOCK, 'testblock');
        $this->assertNull($this->smarty->get_registered_plugin(Smarty::PLUGIN_BLOCK, 'testblock'));
    }

    public function testUnregisterBlockWrapper()
    {
        $this->smarty->register_plugin('block', 'testblock', 'myblock');
        $this->smarty->unregister_plugin('block', 'testblock');
        $this->assertNull($this->smarty->get_registered_plugin(Smarty::PLUGIN_BLOCK, 'testblock'));
    }

    /**
     * test unregister->block method not registered
     */
    public function testUnregisterBlockNotRegistered()
    {
        $this->smarty->unregister_plugin(Smarty::PLUGIN_BLOCK, 'testblock');
        $this->assertNull($this->smarty->get_registered_plugin(Smarty::PLUGIN_BLOCK, 'testblock'));
    }
}

function myblock($params, $content, &$smarty_tpl, &$repeat)
{
    static $loop = 0;

    if ($content == null) {
        $loop = 0;

        return;
    }
    $loop++;
    if ($loop < 3) {
        $repeat = true;
    }

    return "function $content $loop ";
}

function myblockcache($params, $content, &$smarty_tpl, &$repeat)
{
    return $content;
}

class blockparamsCompiler extends \Smarty\Compile\Base
{
    protected $shorttag_order = ['first', 'second'];
    protected $optional_attributes = ['first', 'second'];

    public function compile($args, Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        $_attr = $this->get_attributes($compiler, $args);

        $output = '';
        if (isset($_attr['first'])) {
            $output .= 'first';
        }

        if (isset($_attr['second'])) {
            $output .= 'second';
        }

        return $output;
    }

}

class myblockclass
{
    public static function static_method($params, $content, &$smarty_tpl, &$repeat)
    {
        static $loop = 0;

        if ($content == null) {
            $loop = 0;

            return;
        }
        $loop++;
        if ($loop < 3) {
            $repeat = true;
        }

        return "static $content $loop ";
    }

    public function object_method($params, $content, &$smarty_tpl, &$repeat)
    {
        static $loop = 0;

        if ($content == null) {
            $loop = 0;

            return;
        }
        $loop++;
        if ($loop < 3) {
            $repeat = true;
        }

        return "object $content $loop ";
    }
}
