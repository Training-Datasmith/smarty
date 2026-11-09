<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for eval resources
 *

 * @author  Uwe Tews
 */

/**
 * class for eval resource tests
 *
 *
 *
 *
 */
class Eval_Resource_Test extends PHPUnit_Smarty
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
     * test template eval exits
     */
    public function testTemplateEvalExists1()
    {
        $tpl = $this->smarty->create_template('eval:{$foo}');
        $this->assertTrue($tpl->get_source()->exists);
    }

    public function testTemplateEvalExists2()
    {
        $this->assertTrue($this->smarty->template_exists('eval:{$foo}'));
    }

    public function testGetTemplateResourceName()
    {
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertEquals('hello world', $tpl->get_source()->get_resource_name());
    }

    public function testGetTemplateTimestamp()
    {
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertTrue($tpl->get_source()->get_time_stamp());
    }

    /**
     * test getTemplateSource
     */
    public function testGetTemplateSource()
    {
        $tpl = $this->smarty->create_template('eval:hello world{$foo}');
        $this->assertEquals('hello world{$foo}', $tpl->get_source()->get_content());
    }

    /**
     * test empty templates
     */
    public function testEmptyTemplate()
    {
        $tpl = $this->smarty->create_template('eval:');
        $this->assertEquals('', $this->smarty->fetch($tpl));
    }

    /**
     * test usesCompiler
     */
    public function testUsesCompiler()
    {
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertFalse($tpl->get_source()->handler->supports_compiled_templates());
    }

    /**
     * test isEvaluated
     */
    public function testIsEvaluated()
    {
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertTrue($tpl->get_source()->handler->recompiled);
    }

    /**
     * test must_compile
     */
    public function testMustCompile()
    {
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertTrue($tpl->must_compile());
    }

    /**
     * test get_compiled_filepath
     */
    public function testGetCompiledFilepath()
    {
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertNull($tpl->get_compiled()->filepath);
    }

    /**
     * test getCompiledTimestamp
     */
    public function testGetCompiledTimestamp()
    {
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertFalse($tpl->get_compiled()->get_time_stamp());
    }

    /**
     * test is_cached
     */
    public function testIsCached()
    {
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertFalse($tpl->is_cached());
    }

    /**
     * test getRenderedTemplate
     */
    public function testGetRenderedTemplate()
    {
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertEquals('hello world', $tpl->fetch());
    }

    /**
     * test that no complied template and cache file was produced
     */
    public function testNoFiles()
    {
        $this->clean_dir($this->smarty->get_cache_dir());
        $this->clean_dir($this->smarty->get_compile_dir());
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 20;
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
        $this->assertEquals(0, $this->smarty->clear_all_cache());
        $this->assertEquals(0, $this->smarty->clear_compiled_template());
    }

    /**
     * test $smarty->is_cached
     */
    public function testSmartyIsCached()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 20;
        $tpl = $this->smarty->create_template('eval:hello world');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
        $this->assertFalse($this->smarty->is_cached($tpl));
    }

    public function testUrlencodeTemplate()
    {
        $tpl = $this->smarty->create_template('eval:urlencode:%7B%22foobar%22%7Cescape%7D');
        $this->assertEquals('foobar', $tpl->fetch());
    }

    public function testBase64Template()
    {
        $tpl = $this->smarty->create_template('eval:base64:eyJmb29iYXIifGVzY2FwZX0=');
        $this->assertEquals('foobar', $tpl->fetch());
    }

    /**
     * test clear_compiled_template()
     */
    public function testClearCompiled()
    {
        $this->assertEquals(0, $this->smarty->clear_compiled_template('eval:hello world'));
    }

}
