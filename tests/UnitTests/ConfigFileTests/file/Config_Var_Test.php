<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of config  variables
 *

 * @author  Uwe Tews
 */

/**
 * class for config variable tests
 *
 *
 *
 *
 */
class Config_Var_Test extends PHPUnit_Smarty
{
    /**
     * Sets up the fixture
     * This method is called before a test is executed.
     *
     */
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    /**
     * empty templat_c and cache folders
     */
    public function testInit()
    {
        $this->clean_dirs();
    }

    /**
     * test number config variable
     */
    public function testConfigNumber()
    {
        $this->smarty->config_load('test.conf');
        $this->assertEquals('123.4', $this->smarty->fetch('number.tpl'));
    }

    /**
     * test string config variable
     */
    public function testConfigText()
    {
        $this->smarty->config_load('test.conf');
        $this->assertEquals('123bvc', $this->smarty->fetch('text.tpl'));
    }

    /**
     * test line string config variable
     */
    public function testConfigLine()
    {
        $this->smarty->config_load('test.conf');
        $this->assertEquals('123 This is a line', $this->smarty->fetch('eval:{#line#}'));
    }

    /**
     * test config variables in global sections
     */
    public function testConfigVariableGlobalSections()
    {
        $this->smarty->config_load('test.conf');
        $this->assertEquals('Welcome to Smarty! Global Section1 Global Section2', $this->smarty->fetch('sec1sec2.tpl'));
    }

    /**
     * test config variables loading section2
     */
    public function testConfigVariableSection2()
    {
        $this->smarty->config_load('test.conf', 'section2');
        $this->assertEquals('Welcome to Smarty! Global Section1 Hello Section2', $this->smarty->fetch('sec1sec2.tpl'));
    }

    /**
     * test config variables loading section special char
     */
    public function testConfigVariableSectionSpecialChar()
    {
        $this->smarty->config_load('test.conf', '/');
        $this->assertEquals('Welcome to Smarty! special char', $this->smarty->fetch('sec.tpl'));
    }

    /**
     * test config variables loading section foo/bar
     */
    public function testConfigVariableSectionFooBar()
    {
        $this->smarty->config_load('test.conf', 'foo/bar');
        $this->assertEquals('Welcome to Smarty! section foo/bar', $this->smarty->fetch('sec.tpl'));
    }

    /**
     * test config variables loaded in different scopes from different sections (Smarty and template)
     */
    public function testConfigDifferentScope()
    {
        $this->smarty->config_load('test.conf', 'section2');
        $tpl = $this->smarty->create_template('sec1sec2.tpl');
        $tpl->config_load('test.conf', 'section1');
        $this->assertEquals('Welcome to Smarty! Global Section1 Hello Section2', $this->smarty->fetch('sec1sec2.tpl'));
        $this->assertEquals('Welcome to Smarty! Hello Section1 Global Section2', $this->smarty->fetch($tpl));
    }

    /**
     * test config variables of hidden sections
     * shall display variables from hidden section
     */
    public function testConfigVariableHidden()
    {
        $this->smarty->config_read_hidden = true;
        $this->smarty->config_load('test.conf', 'hidden');
        $this->assertEquals('Welcome to Smarty!Hidden Section', $this->smarty->fetch('hidden.tpl'));
    }

    /**
     * test config variables of disabled hidden sections
     * shall display not variables from hidden section
     */
    public function testConfigVariableHiddenDisable()
    {
        $this->smarty->set_error_reporting(error_reporting() & ~(E_NOTICE | E_USER_NOTICE));
        $this->smarty->config_read_hidden = false;
        $this->smarty->config_load('test.conf', 'hidden');
        $this->assertEquals('Welcome to Smarty!', $this->smarty->fetch('hidden.tpl'));
    }

    /**
     * test get_config_vars
     */
    public function testConfigGetSingleConfigVar()
    {
        $this->smarty->config_load('test.conf');
        $this->assertEquals('Welcome to Smarty!', $this->smarty->get_config_vars('title'));
    }

    /**
     * test get_config_vars return all variables
     */
    public function testConfigGetAllConfigVars()
    {
        $this->smarty->config_load('test.conf');
        $vars = $this->smarty->get_config_vars();
        $this->assertTrue(is_array($vars));
        $this->assertEquals('Welcome to Smarty!', $vars['title']);
        $this->assertEquals('Global Section1', $vars['sec1']);
    }

    /**
     * test clear_config for single variable
     */
    public function testConfigClearSingleConfigVar()
    {
        $this->smarty->config_load('test.conf');
        $this->smarty->clear_config('title');
        $this->assertEquals('', $this->smarty->get_config_vars('title'));
    }

    /**
     * test clear_config for all variables
     */
    public function testConfigClearConfigAll()
    {
        $this->smarty->config_load('test.conf');
        $this->smarty->clear_config();
        $vars = $this->smarty->get_config_vars();
        $this->assertTrue(is_array($vars));
        $this->assertTrue(empty($vars));
    }

    /**
     * test config vars on data object
     */
    public function testConfigTextData()
    {
        $data = $this->smarty->create_data();
        $data->config_load('test.conf');
        $tpl = $this->smarty->create_template('text.tpl', $data);
        $this->assertEquals('123bvc', $this->smarty->fetch($tpl));
    }

    /**
     * test get_config_vars on data object
     */
    public function testConfigGetSingleConfigVarData()
    {
        $data = $this->smarty->create_data();
        $data->config_load('test.conf');
        $this->assertEquals('Welcome to Smarty!', $data->get_config_vars('title'));
    }

    /**
     * test get_config_vars return all variables on data object
     */
    public function testConfigGetAllConfigVarsData()
    {
        $data = $this->smarty->create_data();
        $data->config_load('test.conf');
        $vars = $data->get_config_vars();
        $this->assertTrue(is_array($vars));
        $this->assertEquals('Welcome to Smarty!', $vars['title']);
        $this->assertEquals('Global Section1', $vars['sec1']);
    }

    /**
     * test clear_config for single variable on data object
     */
    public function testConfigClearSingleConfigVarData()
    {
        $data = $this->smarty->create_data();
        $data->config_load('test.conf');
        $data->clear_config('title');
        $this->assertEquals('', $data->get_config_vars('title'));
        $this->assertEquals('Global Section1', $data->get_config_vars('sec1'));
    }

    /**
     * test clear_config for all variables on data object
     */
    public function testConfigClearConfigAllData()
    {
        $data = $this->smarty->create_data();
        $data->config_load('test.conf');
        $data->clear_config();
        $vars = $data->get_config_vars();
        $this->assertTrue(is_array($vars));
        $this->assertTrue(empty($vars));
    }

    /**
     * test config vars on template object
     */
    public function testConfigTextTemplate()
    {
        $tpl = $this->smarty->create_template('text.tpl');
        $tpl->config_load('test.conf');
        $this->assertEquals('123bvc', $this->smarty->fetch($tpl));
    }

    /**
     * test get_config_vars on template object
     */
    public function testConfigGetSingleConfigVarTemplate()
    {
        $tpl = $this->smarty->create_template('text.tpl');
        $tpl->config_load('test.conf');
        $this->assertEquals('Welcome to Smarty!', $tpl->get_config_vars('title'));
    }

    /**
     * test get_config_variable on template object
     */
    public function testConfigGetSingleConfigVarTemplate2()
    {
        $tpl = $this->smarty->create_template('text.tpl');
        $tpl->config_load('test.conf');
        $this->assertEquals('Welcome to Smarty!', $tpl->get_config_variable('title'));
    }

    /**
     * test get_config_vars return all variables on template object
     */
    public function testConfigGetAllConfigVarsTemplate()
    {
        $tpl = $this->smarty->create_template('text.tpl');
        $tpl->config_load('test.conf');
        $vars = $tpl->get_config_vars();
        $this->assertTrue(is_array($vars));
        $this->assertEquals('Welcome to Smarty!', $vars['title']);
        $this->assertEquals('Global Section1', $vars['sec1']);
    }

    /**
     * test clear_config for single variable on template object
     */
    public function testConfigClearSingleConfigVarTemplate()
    {
        $tpl = $this->smarty->create_template('text.tpl');
        $tpl->config_load('test.conf');
        $tpl->clear_config('title');
        $this->assertEquals('', $tpl->get_config_vars('title'));
        $this->assertEquals('Global Section1', $tpl->get_config_vars('sec1'));
    }

    /**
     * test clear_config for all variables on template object
     */
    public function testConfigClearConfigAllTemplate()
    {
        $tpl = $this->smarty->create_template('text.tpl');
        $tpl->config_load('test.conf');
        $tpl->clear_config();
        $vars = $tpl->get_config_vars();
        $this->assertTrue(is_array($vars));
        $this->assertTrue(empty($vars));
    }

    /**
     * test config variables loading from absolute file path
     */
    public function testConfigAbsolutePath()
    {
        $file = realpath($this->smarty->get_config_dir(0) . 'test.conf');
        $this->smarty->config_load($file);
        $this->assertEquals('123.4', $this->smarty->fetch('number.tpl'));
    }

    public function testConfigResourceDb4()
    {
        $this->load_plugins_dir_for_tests(__DIR__ . '/../../ResourceTests/ResourcePlugins/PHPunitplugins/');
        $this->smarty->config_load('db4:foo.conf');
        $this->assertEquals('bar', $this->smarty->fetch('foo.tpl'));
    }
    public function testConfigUndefinedSilent()
    {
        $this->assertEquals('', $this->smarty->fetch('foo.tpl'));
    }

    public function testConfigUndefinedNotice()
    {
        $this->smarty->error_unassigned = true;
        try {
            $this->assertEquals('', $this->smarty->fetch('foo.tpl'));
        } catch (Exception $e) {
            $this->assertStringStartsWith('Undefined variable', $e->getMessage());
        }
    }
}
