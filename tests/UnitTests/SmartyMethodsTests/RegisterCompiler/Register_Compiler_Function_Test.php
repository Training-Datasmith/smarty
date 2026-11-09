<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests register->compilerFunction / unregister->compilerFunction methods
 *

 * @author  Uwe Tews
 */

use Smarty\Smarty;

/**
 * class for register->compilerFunction / unregister->compilerFunction methods tests
 *
 *
 *
 *
 */
class Register_Compiler_Function_Test extends PHPUnit_Smarty
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
     * test register->compilerFunction method for function
     */
    public function testRegisterCompilerFunction()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_COMPILER, 'testcompilerfunction', 'mycompilerfunction');
        $this->assertEquals('mycompilerfunction', $this->smarty->get_registered_plugin('compiler', 'testcompilerfunction')[0]);
        $this->assertEquals('hello world 1', $this->smarty->fetch('eval:{testcompilerfunction var=1}'));
    }

    /**
     * test register->compilerFunction method for blocks
     */
    public function testRegisterCompilerFunctionBlock()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_COMPILER, 'foo', 'mycompilerfunctionopen');
        $this->smarty->register_plugin(Smarty::PLUGIN_COMPILER, 'fooclose', 'mycompilerfunctionclose');
        $result = $this->smarty->fetch('eval:{foo} hallo {/foo}');
        $this->assertEquals('open tag hallo close tag', $result);
    }

    /**
     * test register->compilerFunction method for static class
     */
    public function testRegisterCompilerFunctionClass()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_COMPILER, 'testcompilerfunction', ['mycompilerfunctionclass', 'execute']);
        $this->assertEquals('hello world 2', $this->smarty->fetch('eval:{testcompilerfunction var1=2}'));
    }

    /**
     * test register->compilerFunction method for objects
     */
    public function testRegisterCompilerFunctionObject()
    {
        $obj = new mycompilerfunctionclass();
        $this->smarty->register_plugin(Smarty::PLUGIN_COMPILER, 'testcompilerfunction', [$obj, 'compile']);
        $this->assertEquals('hello world 3', $this->smarty->fetch('eval:{testcompilerfunction var2=3}'));
    }

    /**
     * test unregister->compilerFunction method
     */
    public function testUnregisterCompilerFunction()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_COMPILER, 'testcompilerfunction', 'mycompilerfunction');
        $this->smarty->unregister_plugin(Smarty::PLUGIN_COMPILER, 'testcompilerfunction');
        $this->assertNull($this->smarty->get_registered_plugin(Smarty::PLUGIN_COMPILER, 'testcompilerfunction'));
    }

    /**
     * test unregister->compilerFunction method not registered
     */
    public function testUnregisterCompilerFunctionNotRegistered()
    {
        $this->smarty->unregister_plugin(Smarty::PLUGIN_COMPILER, 'testcompilerfunction');
        $this->assertNull($this->smarty->get_registered_plugin(Smarty::PLUGIN_COMPILER, 'testcompilerfunction'));
    }

    /**
     * test unregister->compilerFunction method other registered
     */
    public function testUnregisterCompilerFunctionOtherRegistered()
    {
        $this->smarty->register_plugin(Smarty::PLUGIN_BLOCK, 'testcompilerfunction', 'mycompilerfunction');
        $this->smarty->unregister_plugin(Smarty::PLUGIN_COMPILER, 'testcompilerfunction');
        $this->assertIsArray($this->smarty->get_registered_plugin(Smarty::PLUGIN_BLOCK, 'testcompilerfunction'));
    }
}

function mycompilerfunction($params, $smarty)
{
    return "<?php echo 'hello world {$params['var']}';?>";
}

function mycompilerfunctionopen($params, $smarty)
{
    return "<?php echo 'open tag';?>";
}

function mycompilerfunctionclose($params, $smarty)
{
    return "<?php echo 'close tag';?>";
}

class mycompilerfunctionclass
{
    public static function execute($params, $smarty)
    {
        return "<?php echo 'hello world {$params['var1']}';?>";
    }

    public function compile($params, $smarty)
    {
        return "<?php echo 'hello world {$params['var2']}';?>";
    }
}
