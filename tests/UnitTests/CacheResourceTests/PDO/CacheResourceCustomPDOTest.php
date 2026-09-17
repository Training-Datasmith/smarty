<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for cache resource Pdo
 *

 * @author  Uwe Tews
 */

include_once __DIR__ . '/../_shared/CacheResourceTestCommon.php';
include_once __DIR__ . '/cacheresource.pdotest.php';

/**
 * class for cache resource file tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class CacheResourceCustomPDOTest extends CacheResourceTestCommon
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
        $this->smarty->setCachingType('pdo');
        $this->smarty->registerCacheResource(
            'pdo',
            new Smarty_CacheResource_Pdotest($this->getPDO(), 'output_cache')
        );
    }

    protected function expectedCachedFilepath(\Smarty\Template $tpl): string
    {
        $cached = $tpl->getCached();
        $_cache_id = isset($cached->cache_id) ? preg_replace('![^\w\|]+!', '_', (string) $cached->cache_id) : null;
        $_compile_id = isset($cached->compile_id) ? preg_replace('![^\w]+!', '_', (string) $cached->compile_id) : null;

        return sha1($cached->getSource()->uid . $_cache_id . $_compile_id);
    }

    public function testGetCachedFilepathSubDirs()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->setUseSubDirs(true);
        $tpl = $this->smarty->createTemplate('helloworld.tpl');
        $this->assertEquals($this->expectedCachedFilepath($tpl), $tpl->getCached()->filepath);
    }

    public function testGetCachedFilepathCacheId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->createTemplate('helloworld.tpl', 'foo|bar');
        $this->assertEquals($this->expectedCachedFilepath($tpl), $tpl->getCached()->filepath);
    }

    public function testGetCachedFilepathCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->createTemplate('helloworld.tpl', null, 'blar');
        $this->assertEquals($this->expectedCachedFilepath($tpl), $tpl->getCached()->filepath);
    }

    public function testGetCachedFilepathCacheIdCompileId()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->createTemplate('helloworld.tpl', 'foo|bar', 'blar');
        $this->assertEquals($this->expectedCachedFilepath($tpl), $tpl->getCached()->filepath);
    }

    public function testInit()
    {
        $this->cleanDirs();
        $this->initMysqlCache();
    }
}
