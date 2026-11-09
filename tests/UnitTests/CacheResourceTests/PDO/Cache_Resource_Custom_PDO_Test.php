<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for cache resource Pdo
 *

 * @author  Uwe Tews
 */

include_once __DIR__ . '/../_shared/Cache_Resource_Test_Common.php';
include_once __DIR__ . '/cacheresource.pdotest.php';

/**
 * class for cache resource file tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Cache_Resource_Custom_PDO_Test extends Cache_Resource_Test_Common
{
    public function setUp(): void
    {
        if (PdoCacheEnable != true) {
            $this->markTestSkipped('mysql Pdo tests are disabled');
        }
        if (self::$init) {
            $this->getConnection();
        }
        $this->initMysqlCache(false);
        $this->setUpSmarty(__DIR__);
        parent::setUp();
        $this->smarty->set_caching_type('pdo');
        $this->smarty->register_cache_resource(
            'pdo',
            new Smarty_CacheResource_Pdotest($this->getPDO(), 'output_cache')
        );
    }

    protected function expectedCachedFilepath(\Smarty\Template $tpl): string
    {
        $cached = $tpl->get_cached();
        $_cache_id = isset($cached->cache_id) ? preg_replace('![^\w\|]+!', '_', (string) $cached->cache_id) : null;
        $_compile_id = isset($cached->compile_id) ? preg_replace('![^\w]+!', '_', (string) $cached->compile_id) : null;

        return sha1($cached->get_source()->uid . $_cache_id . $_compile_id);
    }

    public function testGetCachedFilepathSubDirs()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->set_use_sub_dirs(true);
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertEquals($this->expectedCachedFilepath($tpl), $tpl->get_cached()->filepath);
    }

    public function testGetCachedFilepathCacheId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar');
        $this->assertEquals($this->expectedCachedFilepath($tpl), $tpl->get_cached()->filepath);
    }

    public function testGetCachedFilepathCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl', null, 'blar');
        $this->assertEquals($this->expectedCachedFilepath($tpl), $tpl->get_cached()->filepath);
    }

    public function testGetCachedFilepathCacheIdCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl', 'foo|bar', 'blar');
        $this->assertEquals($this->expectedCachedFilepath($tpl), $tpl->get_cached()->filepath);
    }

    public function testInit()
    {
        $this->cleanDirs();
        $this->initMysqlCache();
    }
}
