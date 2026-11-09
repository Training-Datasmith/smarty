<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests register->resource
 *

 * @author  Uwe Tews
 */

use Smarty\Resource\Custom_Plugin;

/**
 * class for register->resource tests
 *
 *
 *
 *
 */
class Registered_Resource_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);

        $this->smarty->register_resource('rr', new Registered_Resource_Test__Resource1_Plugin());
    }

    public function testInit()
    {
        $this->clean_dirs();
    }
    /**
     * test resource plugin rendering
     */
    public function testResourcePlugin()
    {
        $this->assertEquals('hello world', $this->smarty->fetch('rr:test'));
    }

    public function testClearCompiledResourcePlugin()
    {
        $this->assertEquals(1, $this->smarty->clear_compiled_template('rr:test'));
    }

    /**
     * test resource plugin timesatmp
     */
    public function testResourcePluginTimestamp()
    {
        $tpl = $this->smarty->create_template('rr:test');
        $this->assertTrue(is_integer($tpl->get_source()->get_time_stamp()));
        $this->assertEquals(10, strlen((string) $tpl->get_source()->get_time_stamp()));
    }

    /**
     * test compile_id change
     */
    public function testResourceCompileIdChange()
    {
        $this->smarty->register_resource('myresource', new Registered_Resource_Test__Resource2_Plugin());
        $this->smarty->set_compile_id('a');
        $this->assertEquals('this is template 1', $this->smarty->fetch('myresource:some'));
        $this->assertEquals('this is template 1', $this->smarty->fetch('myresource:some'));
        $this->smarty->set_compile_id('b');
        $this->assertEquals('this is template 2', $this->smarty->fetch('myresource:some'));
        $this->assertEquals('this is template 2', $this->smarty->fetch('myresource:some'));
    }
    /**
     * test {$smarty.template}
     *
     */
    public function testSmartyTemplate()
    {
        $this->smarty->register_resource('mytpl', new Registered_Resource_Test__Resource3_Plugin());
        $this->assertEquals('template = mytpl:foo', $this->smarty->fetch('mytpl:foo'));
    }
    /**
     * test {$smarty.current_dir}
     *
     */
    public function testSmartyCurrentDir()
    {
        $this->smarty->register_resource('mytpl', new Registered_Resource_Test__Resource4_Plugin());
        $this->assertEquals('current_dir = .', $this->smarty->fetch('mytpl:bar'));
    }
}

class Registered_Resource_Test__Resource1_Plugin extends Custom_Plugin
{
    protected function fetch($name, &$source, &$mtime)
    {
        $source = '{$x="hello world"}{$x}';
        $mtime = 1000000000;
    }

}

class Registered_Resource_Test__Resource2_Plugin extends Custom_Plugin
{
    protected function fetch($name, &$source, &$mtime)
    {

        // we update a counter, so that we return a new source for every call
        static $counter = 0;
        $counter++;

        // construct a new source
        $source = "this is template $counter";

        $mtime = 1000000000;
    }

    protected function fetch_timestamp($name)
    {
        return 1000000000;
    }

}

class Registered_Resource_Test__Resource3_Plugin extends Custom_Plugin
{
    protected function fetch($name, &$source, &$mtime)
    {
        $source = 'template = {$smarty.template}';
        $mtime = 1000000000;
    }

}

class Registered_Resource_Test__Resource4_Plugin extends Custom_Plugin
{
    protected function fetch($name, &$source, &$mtime)
    {
        $source = 'current_dir = {$smarty.current_dir}';
        $mtime = 1000000000;
    }

}
