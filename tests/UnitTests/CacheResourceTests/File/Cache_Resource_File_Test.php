<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for cache resource file
 *

 * @author  Uwe Tews
 */

include_once __DIR__ . '/../_shared/Cache_Resource_Test_Common.php';

/**
 * class for cache resource file tests
 */
class Cache_Resource_File_Test extends Cache_Resource_Test_Common
{
    private $directory_separator;

    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        parent::setUp();
        $this->directory_separator = preg_quote(DIRECTORY_SEPARATOR, '/');
        $this->smarty->set_cache_resource(new Smarty_CacheResource_Filetest());
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    /**
     * test getCachedFilepath with configuration['useSubDirs'] enabled
     */
    public function testGetCachedFilepathSubDirs()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(true);
        $tpl = $this->smarty->create_template('helloworld.tpl');

        $pattern = '/.*' . $this->directory_separator . '([a-f0-9]{2}' . $this->directory_separator . '){3}.*\.php/';
        $this->assertRegExp($pattern, $tpl->get_cached()->filepath);
    }

    /**
     * test getCachedFilepath with cache_id
     */
    public function testGetCachedFilepathCacheId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(true);
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar');

        $pattern = '/.*' . $this->directory_separator . 'foo' . $this->directory_separator . 'bar' . $this->directory_separator . '([a-f0-9]{2}' . $this->directory_separator . '){3}.*\.php/';
        $this->assertRegExp($pattern, $tpl->get_cached()->filepath);
    }

    /**
     * test getCachedFilepath with compile_id
     */
    public function testGetCachedFilepathCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(true);
        $tpl = $this->smarty->create_template('helloworld.tpl', null, 'blar');

        $pattern = '/.*' . $this->directory_separator . 'blar' . $this->directory_separator . '([a-f0-9]{2}' . $this->directory_separator . '){3}.*\.php/';
        $this->assertRegExp($pattern, $tpl->get_cached()->filepath);
    }

    /**
     * test getCachedFilepath with cache_id and compile_id
     */
    public function testGetCachedFilepathCacheIdCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(true);
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');

        $pattern = '/.*' . $this->directory_separator . 'foo' . $this->directory_separator . 'bar' . $this->directory_separator . 'blar' . $this->directory_separator . '([a-f0-9]{2}' . $this->directory_separator . '){3}.*\.php/';
        $this->assertRegExp($pattern, $tpl->get_cached()->filepath);
    }

    /**
     * test cache->clear_all with cache_id and compile_id
     */
    public function testClearCacheAllCacheIdCompileId()
    {
        $this->clean_cache_dir();
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(true);
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertEquals(1, $this->smarty->clear_all_cache());
    }

    /**
     * test cache->clear with cache_id and compile_id
     */
    public function testClearCacheCacheIdCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->clean_cache_dir();
        $this->smarty->set_use_sub_dirs(true);
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar2', 'blar');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl3);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertEquals(2, $this->smarty->clear_cache(null, 'foo|bar'));
        $this->assertFalse(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl3->get_cached()->filepath));
    }

    public function testClearCacheCacheIdCompileId2()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(false);
        $this->clean_cache_dir();
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar2', 'blar');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl3);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertEquals(2, $this->smarty->clear_cache('helloworld.tpl'));
        $this->assertFalse(file_exists($tpl->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
    }

    public function testClearCacheCacheIdCompileId2Sub()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(true);
        $this->clean_cache_dir();
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar2', 'blar');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl3);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertEquals(2, $this->smarty->clear_cache('helloworld.tpl'));
        $this->assertFalse(file_exists($tpl->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
    }

    public function testClearCacheCacheIdCompileId3()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->clean_cache_dir();
        $this->smarty->set_use_sub_dirs(false);
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl3);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertEquals(1, $this->smarty->clear_cache('helloworld.tpl', null, 'blar2'));
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
    }

    public function testClearCacheCacheIdCompileId3Sub()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->clean_cache_dir();
        $this->smarty->set_use_sub_dirs(true);
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl3);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertEquals(1, $this->smarty->clear_cache('helloworld.tpl', null, 'blar2'));
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
    }

    public function testClearCacheCacheIdCompileId4()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(false);
        $this->clean_cache_dir();
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl3);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertEquals(1, $this->smarty->clear_cache('helloworld.tpl', null, 'blar2'));
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
    }

    public function testClearCacheCacheIdCompileId4Sub()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(true);
        $this->clean_cache_dir();
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl3);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertEquals(1, $this->smarty->clear_cache('helloworld.tpl', null, 'blar2'));
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
    }

    public function testClearCacheCacheIdCompileId5()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(false);
        $this->clean_cache_dir();
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl3);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertEquals(2, $this->smarty->clear_cache(null, null, 'blar'));
        $this->assertFalse(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl3->get_cached()->filepath));
    }

    public function testClearCacheCacheIdCompileId5Sub()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(true);
        $this->clean_cache_dir();
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $this->write_cached_content($tpl3);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertEquals(2, $this->smarty->clear_cache(null, null, 'blar'));
        $this->assertFalse(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl3->get_cached()->filepath));
    }

    public function testClearCacheCacheFile()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(false);
        $this->clean_cache_dir();
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', null, 'bar');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld.tpl', 'buh|blar');
        $this->write_cached_content($tpl3);
        $tpl4 = $this->smarty->create_template('helloworld2.tpl');
        $this->write_cached_content($tpl4);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl4->get_cached()->filepath));
        $this->assertEquals(3, $this->smarty->clear_cache('helloworld.tpl'));
        $this->assertFalse(file_exists($tpl->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl2->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl3->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl4->get_cached()->filepath));
    }

    public function testClearCacheCacheFileSub()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(true);
        $this->clean_cache_dir();
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->write_cached_content($tpl);
        $tpl2 = $this->smarty->create_template('helloworld.tpl', null, 'bar');
        $this->write_cached_content($tpl2);
        $tpl3 = $this->smarty->create_template('helloworld.tpl', 'buh|blar');
        $this->write_cached_content($tpl3);
        $tpl4 = $this->smarty->create_template('helloworld2.tpl');
        $this->write_cached_content($tpl4);
        $this->assertTrue(file_exists($tpl->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl2->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl3->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl4->get_cached()->filepath));
        $this->assertEquals(3, $this->smarty->clear_cache('helloworld.tpl'));
        $this->assertFalse(file_exists($tpl->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl2->get_cached()->filepath));
        $this->assertFalse(file_exists($tpl3->get_cached()->filepath));
        $this->assertTrue(file_exists($tpl4->get_cached()->filepath));
    }

    /**
     * test cache->clear_all method
     */
    public function testClearCacheAll()
    {
        $this->clean_cache_dir();
        file_put_contents($this->smarty->get_cache_dir() . 'dummy.php', 'test');
        $this->assertEquals(1, $this->smarty->clear_all_cache());
    }

    /**
     * test cache->clear_all method not expired
     */
    public function testClearCacheAllNotExpired()
    {
        $this->clean_cache_dir();
        file_put_contents($this->smarty->get_cache_dir() . 'dummy.php', 'test');
        touch($this->smarty->get_cache_dir() . 'dummy.php', time() - 1000);
        clearstatcache();
        $this->assertEquals(0, $this->smarty->clear_all_cache(2000));
    }

    /**
     * test cache->clear_all method expired
     */
    public function testClearCacheAllExpired()
    {
        $this->clean_cache_dir();
        file_put_contents($this->smarty->get_cache_dir() . 'dummy.php', 'test');
        touch($this->smarty->get_cache_dir() . 'dummy.php', time() - 1000);
        clearstatcache();
        $this->assertEquals(1, $this->smarty->clear_all_cache(500));
    }

    private function write_cached_content($tpl)
    {
        $tpl->write_cached_content("echo 'hello world';\n");
    }
}
