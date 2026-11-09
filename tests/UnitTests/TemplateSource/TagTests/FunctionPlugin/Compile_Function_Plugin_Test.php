<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compilation of function plugins
 *

 * @author  Uwe Tews
 */

/**
 * class for function plugin tests
 *
 *
 *
 *
 */
class Compile_Function_Plugin_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->load_plugins_dir_for_tests('../../../__shared/PHPunitplugins/');
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    /**
     * test function plugin tag in compiled template file
     */
    public function testFunctionPluginFromCompiledTemplateFile()
    {
        $this->smarty->set_force_compile(false);
        $tpl = $this->smarty->create_template('functionplugintest.tpl', $this->smarty);
        $this->assertEquals('10', $this->smarty->fetch($tpl));
    }

    /**
     * test function plugin function definition in script
     */
    public function testFunctionPluginRegisteredFunction()
    {
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_FUNCTION, 'plugintest', 'myplugintest');
        $tpl = $this->smarty->create_template('string:{plugintest foo=bar}', $this->smarty);
        $this->assertEquals('plugin test called bar', $this->smarty->fetch($tpl));
    }

    /**
     * test muiltiline tags
     */
    public function testMultiLineTags()
    {
        $this->assertEquals('10', $this->smarty->fetch("string:{counter\n\tstart=10}"));
    }
}

function myplugintest($params, $smarty)
{
    return "plugin test called $params[foo]";
}
