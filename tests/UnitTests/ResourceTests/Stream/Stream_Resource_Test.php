<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for stream resources
 *

 * @author  Uwe Tews
 */

/**
 * class for stream resource tests
 *
 *
 *
 *
 */
class Stream_Resource_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);

        $this->smarty->assign('foo', 'bar');
        stream_wrapper_register('global', 'Resource_Stream')
        or die('Failed to register protocol');
        $fp = fopen('global://mytest', 'r+');
        fwrite($fp, 'hello world {$foo}');
        fclose($fp);
    }

    public function testInit()
    {
        $this->clean_dirs();
    }
    public function tearDown(): void
    {
        parent::tearDown();
        stream_wrapper_unregister('global');
    }

    /**
     * test getTemplateFilepath
     */
    public function testGetFullResourceName()
    {
        $tpl = $this->smarty->create_template('global:mytest');
        $this->assertEquals('global:mytest', $tpl->get_source()->get_full_resource_name());
    }

    /**
     * test getTemplateTimestamp
     */
    public function testGetTemplateTimestamp()
    {
        $tpl = $this->smarty->create_template('global:mytest');
        $this->assertTrue($tpl->get_source()->get_time_stamp());
    }

    /**
     * test getTemplateSource
     */
    public function testGetTemplateSource()
    {
        $tpl = $this->smarty->create_template('global:mytest', null, null, $this->smarty);
        $this->assertEquals('hello world {$foo}', $tpl->get_source()->get_content());
    }

    /**
     * test usesCompiler
     */
    public function testUsesCompiler()
    {
        $tpl = $this->smarty->create_template('global:mytest');
        $this->assertFalse($tpl->get_source()->handler->supports_compiled_templates());
    }

    /**
     * test isEvaluated
     */
    public function testIsEvaluated()
    {
        $tpl = $this->smarty->create_template('global:mytest');
        $this->assertTrue($tpl->get_source()->handler->recompiled);
    }

    /**
     * test must_compile
     */
    public function testMustCompile()
    {
        $tpl = $this->smarty->create_template('global:mytest');
        $this->assertTrue($tpl->must_compile());
    }

    /**
     * test get_compiled_filepath
     */
    public function testGetCompiledFilepath()
    {
        $tpl = $this->smarty->create_template('global:mytest');
        $this->assertNull($tpl->get_compiled()->filepath);
    }

    /**
     * test getCompiledTimestamp
     */
    public function testGetCompiledTimestamp()
    {
        $tpl = $this->smarty->create_template('global:mytest');
        $this->assertFalse($tpl->get_compiled()->get_time_stamp());
    }

    /**
     * test template file exits
     */
    public function testTemplateStreamExists1()
    {
        $tpl = $this->smarty->create_template('global:mytest');
        $this->assertTrue($tpl->get_source()->exists);
    }

    public function testTemplateStreamExists2()
    {
        $this->assertTrue($this->smarty->template_exists('global:mytest'));
    }

    /**
     * test template is not existing
     */
    public function testTemplateStreamNotExists1()
    {
        $tpl = $this->smarty->create_template('global:notthere');
        $this->assertFalse($tpl->get_source()->exists);
    }

    public function testTemplateStramNotExists2()
    {
        $this->assertFalse($this->smarty->template_exists('global:notthere'));
    }
    /**
     *
     *
     *
     * test not existing template
     */

    public function testTemplateStramNotExists3()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('\'global:notthere\'');
        $this->smarty->fetch('global:notthere');
    }

    /**
     * test write_cached_content
     */
    public function testWriteCachedContent()
    {
        $tpl = $this->smarty->create_template('global:mytest');
        $this->assertFalse($tpl->write_cached_content('dummy'));
    }

    /**
     * test is_cached
     */
    public function testIsCached()
    {
        $tpl = $this->smarty->create_template('global:mytest');
        $this->assertFalse($tpl->is_cached());
    }

    /**
     * test getRenderedTemplate
     */
    public function testGetRenderedTemplate()
    {
        $tpl = $this->smarty->create_template('global:mytest', null, null, $this->smarty);
        $this->assertEquals('hello world bar', $tpl->fetch());
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
        $tpl = $this->smarty->create_template('global:mytest', null, null, $this->smarty);
        $this->assertEquals('hello world bar', $this->smarty->fetch($tpl));
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
        $tpl = $this->smarty->create_template('global:mytest', null, null, $this->smarty);
        $this->assertEquals('hello world bar', $this->smarty->fetch($tpl));
        $this->assertFalse($this->smarty->is_cached($tpl));
    }
}

#[AllowDynamicProperties]
class Resource_Stream
{
    private $position;
    private $varname;

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $this->varname = $url['host'];
        $this->position = 0;

        return true;
    }

    public function stream_read($count)
    {
        $p = &$this->position;
        $ret = substr($GLOBALS[$this->varname], $p, $count);
        $p += strlen($ret);

        return $ret;
    }

    public function stream_write($data)
    {
        $v = &$GLOBALS[$this->varname];
        $l = strlen($data);
        $p = &$this->position;
        $v = substr($v ?? '', 0, $p) . $data . substr($v ?? '', $p += $l);

        return $l;
    }

    public function stream_tell()
    {
        return $this->position;
    }

    public function stream_eof()
    {
        if (!isset($GLOBALS[$this->varname])) {
            return true;
        }

        return $this->position >= strlen($GLOBALS[$this->varname]);
    }

    public function stream_seek($offset, $whence)
    {
        $l = strlen($GLOBALS[$this->varname]);
        $p = &$this->position;
        switch ($whence) {
            case SEEK_SET:
                $new_Pos = $offset;
                break;
            case SEEK_CUR:
                $new_Pos = $p + $offset;
                break;
            case SEEK_END:
                $new_Pos = $l + $offset;
                break;
            default:
                return false;
        }
        $ret = ($new_Pos >= 0 && $new_Pos <= $l);
        if ($ret) {
            $p = $new_Pos;
        }
        return $ret;
    }
}
