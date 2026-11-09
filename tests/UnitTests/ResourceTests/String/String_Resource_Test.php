<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for string resources
 *

 * @author  Uwe Tews
 */

/**
 * class for string resource tests
 *
 *
 *
 *
 */
class StringResourceTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
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

    /**
     * test template string exits
     */
    public function testTemplateStringExists1()
    {
        $tpl = $this->smarty->create_template('string:{$foo}');
        $this->assertTrue($tpl->get_source()->exists);
    }

    public function testTemplateStringExists2()
    {
        $this->assertTrue($this->smarty->template_exists('string:{$foo}'));
    }

    /**
     * test getTemplateFilepath
     */
    public function testGetTemplateFilepath()
    {
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertEquals('hello world', $tpl->get_source()->get_resource_name());
    }

    /**
     * test getTemplateTimestamp
     */
    public function testGetTemplateTimestamp()
    {
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertTrue($tpl->get_source()->get_time_stamp());
    }

    /**
     * test getTemplateSource
     */
    public function testGetTemplateSource()
    {
        $tpl = $this->smarty->create_template('string:hello world{$foo}');
        $this->assertEquals('hello world{$foo}', $tpl->get_source()->get_content());
    }

    /**
     * test usesCompiler
     */
    public function testUsesCompiler()
    {
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertTrue($tpl->get_source()->handler->supports_compiled_templates());
        $this->assertNotEmpty($tpl->get_compiled()->filepath);
    }

    /**
     * test isEvaluated
     */
    public function testIsEvaluated()
    {
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertFalse($tpl->get_source()->handler->recompiled);
    }

    /**
     * test must_compile
     */
    public function testMustCompile()
    {
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertTrue($tpl->must_compile());
    }

    /**
     * test getCompiledTimestamp
     */
    public function testGetCompiledTimestamp()
    {
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertFalse($tpl->get_compiled()->get_time_stamp());
    }

    /**
     * test empty templates
     */
    public function testEmptyTemplate()
    {
        $tpl = $this->smarty->create_template('string:');
        $this->assertEquals('', $this->smarty->fetch($tpl));
    }

    /**
     * test getCachedTimestamp
     */
    public function testGetCachedTimestamp()
    {
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertFalse($tpl->get_cached()->timestamp);
    }

    /**
     * test write_cached_content
     */
    public function testWriteCachedContent()
    {
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertFalse($tpl->write_cached_content('dummy'));
    }

    /**
     * test is_cached
     */
    public function testIsCached()
    {
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertFalse($tpl->is_cached());
    }

    /**
     * test getRenderedTemplate
     */
    public function testGetRenderedTemplate()
    {
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertEquals('hello world', $tpl->fetch());
    }

    /**
     * test $smarty->is_cached
     */
    public function testSmartyIsCached()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 20;
        $tpl = $this->smarty->create_template('string:hello world');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
        $this->assertTrue($this->smarty->is_cached($tpl));
    }

    public function testUrlencodeTemplate()
    {
        $tpl = $this->smarty->create_template('string:urlencode:%7B%22foobar%22%7Cescape%7D');
        $this->assertEquals('foobar', $tpl->fetch());
    }

    public function testBase64Template()
    {
        $tpl = $this->smarty->create_template('string:base64:eyJmb29iYXIifGVzY2FwZX0=');
        $this->assertEquals('foobar', $tpl->fetch());
    }
    public function testClearCompiled()
    {
        $this->smarty->fetch('string:string:hello uwe');
        $this->assertEquals(1, $this->smarty->clear_compiled_template('string:'));
    }
}
