<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests resource plugins
 *

 * @author  Uwe Tews
 */

/**
 * class for resource plugins tests
 *
 *
 *
 *
 */
class ResourcePluginTest extends PHPUnit_Smarty
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
     * test resource plugin rendering
     */
    public function testResourcePlugin()
    {
        $this->smarty->add_plugins_dir('./PHPunitplugins/');
        $this->assertEquals('hello world', $this->smarty->fetch('db:test'));
    }

    /**
     * test resource plugin rendering
     */
    public function testResourcePluginObject()
    {
        $this->smarty->add_plugins_dir('./PHPunitplugins/');
        $this->assertEquals('hello world', $this->smarty->fetch('db2:test'));
    }

    /**
     * test resource plugin rendering of a registered object
     */
    public function testResourcePluginRegisteredInstance()
    {
        $this->smarty->add_plugins_dir('./PHPunitplugins/');
        $this->smarty->register_resource('db2a', new Smarty_Resource_Db2('db2a'));
        $this->assertEquals('hello world', $this->smarty->fetch('db2a:test'));
    }

    /**
     * test resource plugin non-existent compiled cache of a recompiling resource
     */
    public function testResourcePluginRecompiledCompiledFilepath()
    {
        $this->smarty->add_plugins_dir('./PHPunitplugins/');
        $tpl = $this->smarty->create_template('db2:test.tpl');
        $expected = realpath('./templates_c/' . sha1('db2:test.tpl') . '.db2.test.tpl.php');
        $this->assertFalse(!!$expected);
        $this->assertNull($tpl->get_compiled()->filepath);
    }

    /**
     * test resource plugin timestamp
     */
    public function testResourcePluginTimestamp()
    {
        $this->smarty->add_plugins_dir('./PHPunitplugins/');
        $tpl = $this->smarty->create_template('db:test');
        $this->assertTrue(is_integer($tpl->get_source()->get_time_stamp()));
        $this->assertEquals(10, strlen((string) $tpl->get_source()->get_time_stamp()));
    }
}
