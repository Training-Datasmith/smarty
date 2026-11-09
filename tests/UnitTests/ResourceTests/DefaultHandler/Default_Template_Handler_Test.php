<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests deault template handler
 *

 * @author  Uwe Tews
 */

/**
 * class for block plugin tests
 *
 *
 *
 *
 */
class Default_Template_Handler_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->set_force_compile(true);
        $this->smarty->disable_security();
    }

    public function testInit()
    {
        $this->clean_dirs();
    }
    /**
     * test error on unknow template
     */
    public function testUnknownTemplate()
    {
        try {
            $this->smarty->fetch('foo.tpl');
        } catch (Exception $e) {
            $this->assertStringContainsString('Unable to load', $e->getMessage());

            return;
        }
        $this->fail('Exception for none existing template has not been raised.');
    }

    /**
     * test error on registration on none existent handler function.
     */
    public function testRegisterNoneExistentHandlerFunction()
    {
        try {
            $this->smarty->register_default_template_handler('foo');
        } catch (Exception $e) {
            $this->assertStringContainsString('Default template handler', $e->getMessage());
            $this->assertStringContainsString('not callable', $e->getMessage());

            return;
        }
        $this->fail('Exception for none callable function has not been raised.');
    }
    /**
     * test replacement by default template handler
     */
    /**
     * public function testDefaultTemplateHandlerReplacement()
     * {
     * $this->smarty->register->defaultTemplateHandler('my_template_handler');
     * $this->assertEquals("Recsource foo.tpl of type file not found", $this->smarty->fetch('foo.tpl'));
     * }
     */
    public function testDefaultTemplateHandlerReplacementByTemplateFile()
    {
        $this->smarty->register_default_template_handler('my_template_handler_file');
        $this->assertEquals('hello world', $this->smarty->fetch('foo.tpl'));
    }

    /**
     * test default template handler returning fals
     */
    public function testDefaultTemplateHandlerReturningFalse()
    {
        $this->smarty->register_default_template_handler('my_false');
        try {
            $this->smarty->fetch('foo.tpl');
        } catch (Exception $e) {
            $this->assertStringContainsString('Default handler: No template default content for \'file:foo.tpl\'', $e->getMessage());

            return;
        }
        $this->fail('Exception for none existing template has not been raised.');
    }
}

function my_template_handler($resource_type, $resource_name, &$template_source, &$template_timestamp, \Smarty\Smarty $smarty)
{
    $output = "Recsource $resource_name of type $resource_type not found";
    $template_source = $output;
    $template_timestamp = time();

    return true;
}

function my_template_handler_file($resource_type, $resource_name, &$template_source, &$template_timestamp, \Smarty\Smarty $smarty)
{
    return $smarty->get_template_dir(0) . 'helloworld.tpl';
}

function my_false($resource_type, $resource_name, &$template_source, &$template_timestamp, \Smarty\Smarty $smarty)
{
    return false;
}
