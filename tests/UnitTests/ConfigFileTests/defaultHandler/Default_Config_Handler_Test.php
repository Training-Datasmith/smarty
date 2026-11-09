<?php

declare(strict_types=1);
/**
 * Smarty PHPUnit tests default config handler
 *

 * @author  Uwe Tews
 */

use Smarty\Smarty;

/**
 * class for default config handler test
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Default_Config_Handler_Test extends PHPUnit_Smarty
{
    /**
     * Sets up the fixture
     * This method is called before a test is executed.
     *
     */
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->set_force_compile(true);
    }

    /**
     * test unknown config file
     */
    public function testUnknownConfigFile()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('Unable to load \'file:foo.conf\'');
        $this->smarty->config_load('foo.conf');
    }

    /**
     * test register unknown default config handler
     */
    public function testRegisterUnknownDefaultConfigHandler()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('Default config handler');
        $this->expectExceptionMessage('not callable');
        $this->smarty->register_default_config_handler('foo');
    }

    /**
     * test default config handler replacement (config data)
     *
     * @throws \Exception
     * @throws \Smarty\Exception
     */
    public function testDefaultConfigHandlerReplacement()
    {
        $this->smarty->register_default_config_handler('config_handler_data');
        $this->smarty->config_load('foo.conf');
        $this->assertEquals('bar', $this->smarty->fetch('foo.tpl'));
    }

    /**
     * test default config handler replacement (other config file)
     *
     * @throws \Exception
     * @throws \Smarty\Exception
     */
    public function testDefaultConfigHandlerReplacementByConfigFile()
    {
        $this->smarty->register_default_config_handler('config_handler_file');
        $this->smarty->config_load('foo.conf');
        $this->assertEquals('123.4', $this->smarty->fetch('number.tpl'));
    }

    public function testDefaultConfigHandlerReplacementByConfigFileFail()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage("Unable to load default file 'no.conf' for 'file:fo.conf'");
        $this->smarty->register_default_config_handler('config_handler_file');
        $this->smarty->config_load('fo.conf');
        $this->assertEquals('123.4', $this->smarty->fetch('number.tpl'));
    }

    /**
     * test default config handler replacement (return false)
     */
    public function testDefaultConfigHandlerReplacementReturningFalse()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('Unable to load \'file:foo.conf\'');
        $this->smarty->config_load('foo.conf');
    }

    /**
     * test default config handler replacement (return false)
     */
    public function testDefaultConfigHandlerReplacementReturningFalse1()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('No config default content for \'file:bla.conf\'');
        $this->smarty->register_default_config_handler('config_handler_data');
        $this->smarty->config_load('bla.conf');
    }

}

/**
 * config handler returning config data
 *
 * @param         $resource_type
 * @param         $resource_name
 * @param         $config_source
 * @param         $config_timestamp
 * @param \Smarty $smarty
 *
 * @return bool
 */
function config_handler_data($resource_type, $resource_name, &$config_source, &$config_timestamp, \Smarty\Smarty $smarty)
{
    if ($resource_name !== 'foo.conf') {
        return false;
    }
    $output = "foo = 'bar'\n";
    $config_source = $output;
    $config_timestamp = time();

    return true;
}

/**
 * config handler returning config file
 *
 * @param         $resource_type
 * @param         $resource_name
 * @param         $config_source
 * @param         $config_timestamp
 * @param \Smarty $smarty
 *
 * @return string
 */
function config_handler_file($resource_type, $resource_name, &$config_source, &$config_timestamp, \Smarty\Smarty $smarty)
{
    if ($resource_name !== 'foo.conf') {
        return 'no.conf';
    }

    return $smarty->get_config_dir(0) . 'test.conf';
}

/**
 * config handler returning false
 *
 * @param         $resource_type
 * @param         $resource_name
 * @param         $config_source
 * @param         $config_timestamp
 * @param \Smarty $smarty
 *
 * @return bool
 */
function config_handler_false($resource_type, $resource_name, &$config_source, &$config_timestamp, \Smarty\Smarty $smarty)
{
    return false;
}
