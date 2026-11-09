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
class Compile_Compiler_Plugin_Test extends PHPUnit_Smarty
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
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_COMPILER, 'compilerplugin', ['Compiler_Plugin_Class', 'stat_compile']);
        $this->smarty->set_compile_id('static');
        $this->assertEquals('Static World', $this->smarty->fetch('compilerplugintest.tpl'));
    }
    /**
     * test compiler plugin tag in template file
     */
    public function testCompilerPluginClassObject()
    {
        $plugin = new Compiler_Plugin_Class();
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_COMPILER, 'compilerplugin', [$plugin, 'compile']);
        $this->smarty->set_compile_id('object');
        $this->assertEquals('Public World', $this->smarty->fetch('compilerplugintest.tpl'));
    }
}

function mycompilerplugin($params, $compiler)
{
    return '<?php echo \'Hello World\';?>';
}

class Compiler_Plugin_Class
{
    public static function stat_compile($params, $compiler)
    {
        return '<?php echo \'Static World\';?>';
    }
    public function compile($params, $compiler)
    {
        return '<?php echo \'Public World\';?>';
    }
}
