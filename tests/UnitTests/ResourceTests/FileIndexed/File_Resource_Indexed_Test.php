<?php

declare(strict_types=1);

/**
 * Smarty PHPunit tests for File resources
 *

 * @author                 Rodney Rehm
 *
 *
 *
 */
class File_Resource_Indexed_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->add_template_dir(__DIR__ . '/templates_2');
        // note that 10 is a string!
        $this->smarty->add_template_dir(__DIR__ . '/templates_3', '10');
        $this->smarty->add_template_dir(__DIR__ . '/templates_4', 'foo');
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    public function testFetch()
    {
        $tpl = $this->smarty->create_template('dirname.tpl');
        $this->assertEquals('templates', $this->smarty->fetch($tpl));
    }

    public function testFetchNumber()
    {
        $tpl = $this->smarty->create_template('[1]dirname.tpl');
        $this->assertEquals('templates_2', $this->smarty->fetch($tpl));
    }

    public function testFetchNumeric()
    {
        $tpl = $this->smarty->create_template('[10]dirname.tpl');
        $this->assertEquals('templates_3', $this->smarty->fetch($tpl));
    }
    public function testFetchNumeric2()
    {
        $tpl = $this->smarty->create_template('[10, 1]dirname10.tpl');
        $this->assertEquals('templates_3', $this->smarty->fetch($tpl));
    }
    public function testFetchNumeric3()
    {
        $tpl = $this->smarty->create_template('[10, 1]dirname1.tpl');
        $this->assertEquals('templates_2', $this->smarty->fetch($tpl));
    }

    public function testFetchName()
    {
        $tpl = $this->smarty->create_template('[foo]dirname.tpl');
        $this->assertEquals('templates_4', $this->smarty->fetch($tpl));
    }
    public function testFetchName1()
    {
        $tpl = $this->smarty->create_template('[10,0,1,foo]dirname_foo.tpl');
        $this->assertEquals('dirname_foo', $this->smarty->fetch($tpl));
    }
    public function testFetchName2()
    {
        $tpl = $this->smarty->create_template('[0,1,foo,10]dirname_x.tpl');
        $this->assertEquals('templates_2', $this->smarty->fetch($tpl));
    }
    public function testFetchName3()
    {
        $tpl = $this->smarty->create_template('[0,10,foo,1]dirname_x.tpl');
        $this->assertEquals('templates_4', $this->smarty->fetch($tpl));
    }

    public function testGetCompiledFilepath()
    {
        $tpl = $this->smarty->create_template('[foo]dirname.tpl');
        $tpl2 = $this->smarty->create_template('dirname.tpl');

        $this->assertNotEquals($tpl->get_compiled()->filepath, $tpl2->get_compiled()->filepath);
    }

    public function testGetCachedFilepath()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('[foo]dirname.tpl');
        $tpl2 = $this->smarty->create_template('dirname.tpl');

        $this->assertNotEquals($tpl->get_cached()->filepath, $tpl2->get_cached()->filepath);
    }

    public function testPrependTemplatePath()
    {
        $this->smarty->set_template_dir(__DIR__ . '/templates');
        $this->smarty->prepend_template_dir(__DIR__ . '/templates_4');
        $tpl = $this->smarty->create_template('dirname.tpl');
        $this->assertEquals('templates_4', $this->smarty->fetch($tpl));
    }

}
