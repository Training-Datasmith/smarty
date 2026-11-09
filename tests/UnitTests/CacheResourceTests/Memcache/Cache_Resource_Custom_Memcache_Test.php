<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for cache resource memcache
 *

 * @author  Uwe Tews
 */

include_once __DIR__ . '/../_shared/Cache_Resource_Test_Common.php';

/**
 * class for cache resource memcache tests
 *
 *
 *
 *
 */
class Cache_Resource_Custom_Memcache_Test extends Cache_Resource_Test_Common
{
    /**
     * Sets up the fixture
     * This method is called before a test is executed.
     *
     */
    public function setUp(): void
    {
        if (!class_exists('Memcache')) {
            $this->markTestSkipped('Memcache not available');
        }
        $this->set_up_smarty(__DIR__);
        parent::setUp();
        $this->smarty->set_cache_resource(new Smarty_CacheResource_Memcachetest());
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    protected function do_clear_cache_assertion($a, $b)
    {
        $this->assertEquals(- 1, $b);
    }

    public function testGetCachedFilepathSubDirs()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $sha1 = $tpl->get_source()->uid . '#helloworld_tpl##';
        $this->assertEquals($sha1, $tpl->get_cached()->filepath);
    }

    /**
     * test getCachedFilepath with cache_id
     */
    public function testGetCachedFilepathCacheId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar');
        $sha1 = $tpl->get_source()->uid . '#helloworld_tpl#foo|bar#';
        $this->assertEquals($sha1, $tpl->get_cached()->filepath);
    }

    /**
     * test getCachedFilepath with compile_id
     */
    public function testGetCachedFilepathCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl', null, 'blar');
        $sha1 = $tpl->get_source()->uid . '#helloworld_tpl##blar';
        $this->assertEquals($sha1, $tpl->get_cached()->filepath);
    }

    /**
     * test getCachedFilepath with cache_id and compile_id
     */
    public function testGetCachedFilepathCacheIdCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $sha1 = $tpl->get_source()->uid . '#helloworld_tpl#foo|bar#blar';
        $this->assertEquals($sha1, $tpl->get_cached()->filepath);
    }
}
