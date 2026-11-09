<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for cache resource file
 *

 * @author  Uwe Tews
 */

use Smarty\Template;

/**
 * class for cache resource file tests
 *
 *
 */
abstract class Cache_Resource_Test_Common extends PHPUnit_Smarty
{
    public static $touch_resource = null;

    public function setUp(): void
    {
        $this->smarty->set_template_dir(__DIR__ . '/templates');
        $this->load_plugins_dir_for_tests(__DIR__ . '/PHPunitplugins');
        $this->smarty->register_filter('pre', [$this, 'compiled_prefilter']);
    }

    protected function do_clear_cache_assertion($a, $b)
    {
        $this->assertEquals($a, $b);
    }

    public function compiled_prefilter($text, Template $tpl)
    {
        $replace = $tpl->get_template_vars('test');
        return str_replace('#', (string) ($replace ?? ''), $text);
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
        $this->assertEquals($this->build_cached_path($tpl), $tpl->get_cached()->filepath);
    }

    /**
     * test getCachedFilepath with cache_id
     */
    public function testGetCachedFilepathCacheId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar');
        $this->assertEquals($this->build_cached_path($tpl, true, 'foo|bar'), $tpl->get_cached()->filepath);
    }

    /**
     * test getCachedFilepath with compile_id
     */
    public function testGetCachedFilepathCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl', null, 'blar');
        $this->assertEquals($this->build_cached_path($tpl, true, null, 'blar'), $tpl->get_cached()->filepath);
    }

    /**
     * test getCachedFilepath with cache_id and compile_id
     */
    public function testGetCachedFilepathCacheIdCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->assertEquals($this->build_cached_path($tpl, true, 'foo|bar', 'blar'), $tpl->get_cached()->filepath);
    }

    /**
     * test cache->clear_all with cache_id and compile_id
     */
    public function testClearCacheAllCacheIdCompileId()
    {
        $this->smarty->clear_all_cache();
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $tpl->write_cached_content('hello world');
        $this->assertEquals('hello world', $tpl->get_cached_content());
        // Custom CacheResources may return -1 if they can't tell the number of deleted elements
        //$this->assertEquals(-1, $this->smarty->clear_all_cache());
    }

    /**
     * test cache->clear with cache_id and compile_id
     */
    public function testClearCacheCacheIdCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $tpl->write_cached_content('hello world 1');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar2', 'blar');
        $tpl2->write_cached_content('hello world 2');
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $tpl3->write_cached_content('hello world 3');
        // test cached content
        $this->assertEquals('hello world 1', $tpl->get_cached_content());
        $this->assertEquals('hello world 2', $tpl2->get_cached_content());
        $this->assertEquals('hello world 3', $tpl3->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(2, $this->smarty->clear_cache(null, 'foo|bar'));
        // test that caches are deleted properly
        $this->assertNull($tpl->get_cached_content());
        $this->assertEquals('hello world 2', $tpl2->get_cached_content());
        $this->assertNull($tpl3->get_cached_content());
    }

    public function testClearCacheCacheIdCompileId2()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $tpl->write_cached_content('hello world');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar2', 'blar');
        $tpl2->write_cached_content('hello world');
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $tpl3->write_cached_content('hello world');
        // test cached content
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(2, $this->smarty->clear_cache('helloworld.tpl'));
        // test that caches are deleted properly
        $this->assertNull($tpl->get_cached_content());
        $this->assertNull($tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
    }

    public function testClearCacheCacheIdCompileId2Sub()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $tpl->write_cached_content('hello world');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar2', 'blar');
        $tpl2->write_cached_content('hello world');
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $tpl3->write_cached_content('hello world');
        // test cached content
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(2, $this->smarty->clear_cache('helloworld.tpl'));
        // test that caches are deleted properly
        $this->assertNull($tpl->get_cached_content());
        $this->assertNull($tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
    }

    public function testClearCacheCacheIdCompileId3()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $tpl->write_cached_content('hello world');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $tpl2->write_cached_content('hello world');
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $tpl3->write_cached_content('hello world');
        // test cached content
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(1, $this->smarty->clear_cache('helloworld.tpl', null, 'blar2'));
        // test that caches are deleted properly
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertNull($tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
    }

    public function testClearCacheCacheIdCompileId3Sub()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $tpl->write_cached_content('hello world');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $tpl2->write_cached_content('hello world');
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $tpl3->write_cached_content('hello world');
        // test cached content
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(1, $this->smarty->clear_cache('helloworld.tpl', null, 'blar2'));
        // test that caches are deleted properly
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertNull($tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
    }

    public function testClearCacheCacheIdCompileId4()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $tpl->write_cached_content('hello world');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $tpl2->write_cached_content('hello world');
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $tpl3->write_cached_content('hello world');
        // test cached content
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(1, $this->smarty->clear_cache('helloworld.tpl', null, 'blar2'));
        // test that caches are deleted properly
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertNull($tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
    }

    public function testClearCacheCacheIdCompileId4Sub()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $tpl->write_cached_content('hello world');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $tpl2->write_cached_content('hello world');
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $tpl3->write_cached_content('hello world');
        // test cached content
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(1, $this->smarty->clear_cache('helloworld.tpl', null, 'blar2'));
        // test that caches are deleted properly
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertNull($tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
    }

    public function testClearCacheCacheIdCompileId5()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $tpl->write_cached_content('hello world');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $tpl2->write_cached_content('hello world');
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $tpl3->write_cached_content('hello world');
        // test cached content
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(2, $this->smarty->clear_cache(null, null, 'blar'));
        // test that caches are deleted properly
        $this->assertNull($tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertNull($tpl3->get_cached_content());
    }

    public function testClearCacheCacheIdCompileId5Sub()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $tpl->write_cached_content('hello world');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar2');
        $tpl2->write_cached_content('hello world');
        $tpl3 = $this->smarty->create_template('helloworld2.tpl', 'foo|bar', 'blar');
        $tpl3->write_cached_content('hello world');
        // test cached content
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(2, $this->smarty->clear_cache(null, null, 'blar'));
        // test that caches are deleted properly
        $this->assertNull($tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertNull($tpl3->get_cached_content());
    }

    public function testClearCacheCacheFile()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $tpl->write_cached_content('hello world');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', null, 'bar');
        $tpl2->write_cached_content('hello world');
        $tpl3 = $this->smarty->create_template('helloworld.tpl', 'buh|blar');
        $tpl3->write_cached_content('hello world');
        $tpl4 = $this->smarty->create_template('helloworld2.tpl');
        $tpl4->write_cached_content('hello world');
        // test cached content
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        $this->assertEquals('hello world', $tpl4->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(3, $this->smarty->clear_cache('helloworld.tpl'));
        // test that caches are deleted properly
        $this->assertNull($tpl->get_cached_content());
        $this->assertNull($tpl2->get_cached_content());
        $this->assertNull($tpl3->get_cached_content());
        $this->assertEquals('hello world', $tpl4->get_cached_content());
    }

    /**
     * @group slow
     */
    public function testClearCacheExpired()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $tpl->write_cached_content('something else 1');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', null, 'bar');
        $tpl2->write_cached_content('something else 2');
        $tpl3 = $this->smarty->create_template('helloworld.tpl', 'buh|blar');
        $tpl3->write_cached_content('something else 3');
        // test cached content
        $this->assertEquals('something else 1', $tpl->get_cached_content());
        $this->assertEquals('something else 2', $tpl2->get_cached_content());
        $this->assertEquals('something else 3', $tpl3->get_cached_content());
        sleep(4);
        $tpl4 = $this->smarty->create_template('helloworld2.tpl');
        $tpl4->write_cached_content('something else 4');

        // test number of deleted caches
        $this->do_clear_cache_assertion(3, $this->smarty->clear_all_cache(2));

        $tpl = $this->smarty->create_template('helloworld.tpl');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', null, 'bar');
        $tpl3 = $this->smarty->create_template('helloworld.tpl', 'buh|blar');
        $tpl4 = $this->smarty->create_template('helloworld2.tpl');

        // test that caches are deleted properly
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        $this->assertEquals('something else 4', $tpl4->get_cached_content());
    }

    public function testClearCacheCacheFileSub()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->clear_all_cache();
        // create and cache templates
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $tpl->write_cached_content('hello world');
        $tpl2 = $this->smarty->create_template('helloworld.tpl', null, 'bar');
        $tpl2->write_cached_content('hello world');
        $tpl3 = $this->smarty->create_template('helloworld.tpl', 'buh|blar');
        $tpl3->write_cached_content('hello world');
        $tpl4 = $this->smarty->create_template('helloworld2.tpl');
        $tpl4->write_cached_content('hello world');
        // test cached content
        $this->assertEquals('hello world', $tpl->get_cached_content());
        $this->assertEquals('hello world', $tpl2->get_cached_content());
        $this->assertEquals('hello world', $tpl3->get_cached_content());
        $this->assertEquals('hello world', $tpl4->get_cached_content());
        // test number of deleted caches
        $this->do_clear_cache_assertion(3, $this->smarty->clear_cache('helloworld.tpl'));
        // test that caches are deleted properly
        $this->assertNull($tpl->get_cached_content());
        $this->assertNull($tpl2->get_cached_content());
        $this->assertNull($tpl3->get_cached_content());
        $this->assertEquals('hello world', $tpl4->get_cached_content());
    }
    /**
     * Test caching
     * @dataProvider data
     * @group slow
     */
    public function testCache($lock_time, $lock_timeout, $compile_id, $cache_id, $is_cached, $tmin, $tmax, $force_Compile, $force_Cache, $update, $test_number, $compile_Test_Number, $render_Test_Number, $testName)
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->assign('test', $test_number);
        if ($lock_timeout) {
            $this->smarty->cache_locking = true;
            $this->smarty->locking_timeout = $lock_timeout;
        }
        $this->smarty->set_compile_id($compile_id);
        $this->smarty->cache_id = $cache_id;
        $this->smarty->force_compile = $force_Compile;
        $this->smarty->force_cache = $force_Cache;
        if ($update) {
            sleep(1);
            $filepath = $this->build_source_path(null, 'cacheresource.tpl', 'file');
            touch($filepath, $t = time());
            clearstatcache();
        }
        $tpl = $this->smarty->create_template('cacheresource.tpl', $this->smarty);
        if ($update) {
            $this->assertEquals($t, $tpl->get_source()->get_time_stamp(), $testName . ' - source touch');
        }
        if ($lock_time) {
            $tpl->get_cached()->handler->acquire_lock($this->smarty, $tpl->get_cached());
            $tpl->get_cached()->handler->lock_time = $lock_time;
            $tpl->get_cached()->is_locked = false;
        }
        $start = time();
        if (isset($is_cached)) {
            $this->assertEquals($is_cached, $tpl->is_cached(), $testName . ' - is_cached()');
            if ($lock_timeout) {
                $time = time() - $start;
                $this->assertTrue($time >= $tmin && $time <= $tmax, $testName . ' - is_cached() - lock time');
                $this->assertEquals(!$is_cached, $tpl->get_cached()->handler->has_lock($this->smarty, $tpl->get_cached()), $testName . ' - is_cached() - lock status');
            } else {
                $this->assertFalse($tpl->get_cached()->handler->has_lock($this->smarty, $tpl->get_cached()), $testName . ' - is_cached() - unexpected lock');
            }
        }
        $this->assertEquals("cache resource test:{$test_number} compiled:{$compile_Test_Number} rendered:{$render_Test_Number}", $this->smarty->fetch($tpl), $testName . ' - fetch() failure');
        if ($lock_time) {
            $time = time() - $start;
            $this->assertTrue($time >= $tmin && $time <= $tmax, $testName . ' - fetch() - lock time');
        }
        $this->assertFalse($tpl->get_cached()->handler->has_lock($this->smarty, $tpl->get_cached(), $testName . ' - lock not removed'));
    }

    public function data()
    {
        return [
            /*
             * lock time
             * locking_timeout 0 = no cache_locking
             * compile_id
             * cache_id
             * is_cached()  expected result (null = no is_cached test)
             * min elapsed time
             * max elapsed time
             * force compile
             * force cache
             * source update
             * test nr
             * result compile nr
             * result render nr
             * text
             */
            [0, 0, null, null, false, 0, 0, false, false, false, 1, 1, 1, 'locking off - create cache'],
            [0, 0, 1, null, false, 0, 0, false, false, false, 2, 2, 2, 'locking off - create cache compile_id'],
            [0, 0, null, 2, false, 0, 0, false, false, false, 3, 1, 3, 'locking off - create cache cache_id'],
            [0, 0, 3, 4, false, 0, 0, false, false, false, 4, 4, 4, 'locking off - create cache cache_id & compile_id'],
            [0, 0, null, null, null, 0, 0, false, false, false, 5, 1, 1, 'locking off - fetch'],
            [0, 0, 1, null, null, 0, 0, false, false, false, 6, 2, 2, 'locking off - fetch compile_id'],
            [0, 0, null, 2, null, 0, 0, false, false, false, 7, 1, 3, 'locking off - fetch cache_id'],
            [0, 0, 3, 4, null, 0, 0, false, false, false, 8, 4, 4, 'locking off - fetch cache_id & compile_id'],
            [0, 0, null, null, true,0, 0, false, false, false, 9, 1, 1, 'locking off - is_cached & fetch'],
            [0, 0, 1, null, true, 0, 0, false, false, false, 10, 2, 2, 'locking off - is_cached & fetch compile_id'],
            [0, 0, null, 2, true, 0, 0, false, false, false, 11, 1, 3, 'locking off - is_cached & fetch cache_id'],
            [0, 0, 3, 4, true, 0, 0, false, false, false, 12, 4, 4, 'locking off - is_cached & fetch cache_id & compile_id'],
            [0, 0, null, null, false, 0, 0, true, false, false, 13, 13, 13, 'locking off - force compile'],
            [0, 0, null, null, true, 0, 0, false, false, false, 14, 13, 13, 'locking off - after force compile'],
            [0, 0, null, null, false, 0, 0, false, true, false, 15, 13, 15, 'locking off - force cache'],
            [0, 0, null, null, true, 0, 0, false, false, false, 16, 13, 15, 'locking off - after force cache'],
            [0, 0, null, null, false, 0, 0, false, false, true, 17, 17, 17, 'locking off - new source'],
            [0, 0, null, null, true, 0, 0, false, false, false, 18, 17, 17, 'locking off - after new source'],
            [0, 5, null, null, null, 0, 1, false, false, false, 19, 17, 17, 'not locked - fetch'],
            [0, 5, null, null, true, 0, 1, false, false, false, 20, 17, 17, 'not locked - is_cached & fetch'],
            [0, 5, null, null, false, 0, 1, false, false, true, 21, 21, 21, 'not locked - new source'],
            [0, 5, null, null, true, 0, 1, false, false, false, 22, 21, 21, 'not locked - after new source'],
            [4, 10, null, null, null, 2, 10, false, false, false, 23, 21, 21, 'locked - fetch'],
            [4, 10, null, null, true, 2, 10, false, false, false, 24, 21, 21, 'locked - is_cached & fetch'],
            [4, 10, null, null, false, 2, 10, false, false, true, 25, 25, 25, 'locked - new source'],
            [4, 10, null, null, true, 2, 10, false, false, false, 26, 25, 25, 'locked - after new source'],
            [10, 4, null, null, null, 2, 10, false, false, false, 27, 25, 25, 'lock timeout - fetch'],
            [10, 4, null, null, true, 2, 10, false, false, false, 28, 25, 25, 'lock timeout - is_cached & fetch'],
            [10, 4, null, null, false, 2, 10, false, false, true, 29, 29, 29, 'lock timeout - new source'],
            [10, 4, null, null, true, 2, 10, false, false, false, 30, 29, 29, 'lock timeout - after new source'],
            [4, 10, 5, null, false, 2, 10, false, false, false, 31, 31, 31, 'locked - new compiled'],
            [10, 4, 6, null, false, 2, 10, false, false, false, 32, 32, 32, 'lock timeout - new compiled'],
        ];
    }

    public function testCachingDisabled1()
    {
        $this->smarty->assign('test', 50);
        $tpl = $this->smarty->create_template('cacheresource.tpl', $this->smarty);
        $this->assertFalse($tpl->is_cached(), 'is_cached() status');
        $this->assertEquals('cache resource test:50 compiled:50 rendered:50', $this->smarty->fetch($tpl), 'fetch()');
    }
    public function testCachingDisabled2()
    {
        $this->smarty->assign('test', 51);
        $tpl = $this->smarty->create_template('cacheresource.tpl', $this->smarty);
        $this->assertFalse($tpl->is_cached(), 'is_cached() status');
        $this->assertEquals('cache resource test:51 compiled:50 rendered:51', $this->smarty->fetch($tpl), 'fetch()');
    }

    /**
     * Test caching
     * @dataProvider data_dir
     *
     */
    public function testCachingTemplateDir($folder, $iscached, $merge, $result)
    {
        $this->smarty->set_caching(true);
        if ($folder == 0) {
            $this->smarty->set_template_dir([__DIR__ . '/../_shared/templates', __DIR__ . '/../_shared/templates/a']);
        } else {
            $this->smarty->set_template_dir([__DIR__ . '/../_shared/templates', __DIR__ . '/../_shared/templates/b']);
        }
        if ($merge) {
            $this->smarty->set_compile_id(1);
            $this->smarty->set_merge_compiled_includes(true);
        }
        $tpl = $this->smarty->create_template('templatedir.tpl', $this->smarty);
        $this->assertEquals($iscached, $tpl->is_cached());
        $this->assertStringContainsString($result, $tpl->fetch());
    }

    public function data_dir()
    {
        return [
            [0,false,0, 'include a'],
            [0,true,0, 'include a'],
            [1,false,0, 'include b'],
            [1,true,0, 'include b'],
            [0,false,1, 'include a'],
            [0,true,1, 'include a'],
            [1,false,1, 'include b'],
            [1,true,1, 'include b'],
            ];
    }
}
