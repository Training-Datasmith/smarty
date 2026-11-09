<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compilation of compiler plugins
 *

 * @author  Uwe Tews
 */

/**
 * class for compiler plugin tests
 *
 *
 *
 *
 */
class CompileCompilerPluginTest extends PHPUnit_Smarty
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
     * test compiler plugin tag in template file
     */
    public function testCompilerPluginFunction()
    {
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_COMPILER, 'compilerplugin', 'mycompilerplugin');
        $this->smarty->set_compile_id('function');
        $this->assertEquals('Hello World', $this->smarty->fetch('compilerplugintest.tpl'));
    }
    /**
     * test compiler plugin tag in template file
     */
    public function testCompilerPluginClassStatic()
    {
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_COMPILER, 'compilerplugin', ['CompilerPluginClass', 'statCompile']);
        $this->smarty->set_compile_id('static');
        $this->assertEquals('Static World', $this->smarty->fetch('compilerplugintest.tpl'));
    }
    /**
     * test compiler plugin tag in template file
     */
    public function testCompilerPluginClassObject()
    {
        $plugin = new CompilerPluginClass();
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_COMPILER, 'compilerplugin', [$plugin, 'compile']);
        $this->smarty->set_compile_id('object');
        $this->assertEquals('Public World', $this->smarty->fetch('compilerplugintest.tpl'));
    }
}

function mycompilerplugin($params, $compiler)
{
    return '<?php echo \'Hello World\';?>';
}

class CompilerPluginClass
{
    public static function statCompile($params, $compiler)
    {
        return '<?php echo \'Static World\';?>';
    }
    public function compile($params, $compiler)
    {
        return '<?php echo \'Public World\';?>';
    }
}
