<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for cache resource registered
 *

 * @author  Uwe Tews
 */
if (MysqlCacheEnable == true) {
    include_once __DIR__ . '/../_shared/Cache_Resource_Test_Common.php';

    /**
     * class for cache resource file tests
     *
     *
     * @preserveGlobalState    disabled
     *
     */
    class Cache_Resource_Custom_Registered_Test extends Cache_Resource_Test_Common
    {
        public function setUp()
        {
            if (MysqlCacheEnable != true) {
                $this->markTestSkipped('mysql tests are disabled');
            }
            if (self::$init) {
                $this->getConnection();
            }
            $this->setUpSmarty(__DIR__);
            parent::setUp();
            if (!class_exists('Smarty_CacheResource_Mysqltest', false)) {
                require_once(__DIR__ . '/../_shared/PHPunitplugins/cacheresource.mysqltest.php');
            }
            $this->smarty->set_caching_type('foobar');
            $this->smarty->register_cache_resource('foobar', new Smarty_CacheResource_Mysqltest());
        }

        public function testInit()
        {
            $this->cleanDirs();
            $this->initMysqlCache();
        }
    }
}
