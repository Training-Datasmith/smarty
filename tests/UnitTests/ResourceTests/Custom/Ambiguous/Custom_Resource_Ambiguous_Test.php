<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for File resources
 *

 * @author  Uwe Tews
 */

/**
 * class for file resource tests
 */
class CustomResourceAmbiguousTest extends PHPUnit_Smarty
{
    public $_resource = null;

    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
        require_once __DIR__ . '/PHPunitplugins/resource.ambiguous.php';

        // empty the template dir
        $this->smarty->set_template_dir([]);

        // kill cache for unit test
        //        Smarty::$_resource_cache = array();
    }

    public function testInit()
    {
        $this->cleanDirs();
    }

    protected function relative($path)
    {
        $path = str_replace(__DIR__, '.', $path);
        if (DIRECTORY_SEPARATOR == '\\') {
            $path = str_replace('\\', '/', $path);
        }

        return $path;
    }

    public function testNone()
    {
        $resource_handler = new Smarty_Resource_AmbiguousPlugin(__DIR__ . '/templates/ambiguous/');
        $this->smarty->register_resource('ambiguous', $resource_handler);
        $this->smarty->set_default_resource_type('ambiguous');
        //        $this->smarty->setAllowAmbiguousResources(true);

        $tpl = $this->smarty->create_template('foobar.tpl');
        $this->assertFalse($tpl->get_source()->exists);
    }

    public function testCase1()
    {
        $resource_handler = new Smarty_Resource_AmbiguousPlugin(__DIR__ . '/templates/ambiguous/');
        $this->smarty->register_resource('ambiguous', $resource_handler);
        $this->smarty->set_default_resource_type('ambiguous');
        //        $this->smarty->setAllowAmbiguousResources(true);

        $resource_handler->setSegment('case1');

        $tpl = $this->smarty->create_template('foobar.tpl');
        $this->assertTrue($tpl->get_source()->exists);
        $this->assertEquals('case1', $tpl->get_source()->get_content());
    }

    public function testCase2()
    {
        $resource_handler = new Smarty_Resource_AmbiguousPlugin(__DIR__ . '/templates/ambiguous/');
        $this->smarty->register_resource('ambiguous', $resource_handler);
        $this->smarty->set_default_resource_type('ambiguous');
        //        $this->smarty->setAllowAmbiguousResources(true);

        $resource_handler->setSegment('case2');

        $tpl = $this->smarty->create_template('foobar.tpl');
        $this->assertTrue($tpl->get_source()->exists);
        $this->assertEquals('case2', $tpl->get_source()->get_content());
    }

    public function testCaseSwitching()
    {
        $resource_handler = new Smarty_Resource_AmbiguousPlugin(__DIR__ . '/templates/ambiguous/');
        $this->smarty->register_resource('ambiguous', $resource_handler);
        $this->smarty->set_default_resource_type('ambiguous');
        //        $this->smarty->setAllowAmbiguousResources(true);

        $resource_handler->setSegment('case1');
        $tpl = $this->smarty->create_template('foobar.tpl');
        $this->assertTrue($tpl->get_source()->exists);
        $this->assertEquals('case1', $tpl->get_source()->get_content());

        $resource_handler->setSegment('case2');
        $tpl = $this->smarty->create_template('foobar.tpl');
        $this->assertTrue($tpl->get_source()->exists);
        $this->assertEquals('case2', $tpl->get_source()->get_content());
    }
}
