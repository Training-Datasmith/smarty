<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for cache resource mysql
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
    class Cache_Resource_Custom_Mysql_Test extends Cache_Resource_Test_Common
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
            $this->smarty->set_caching_type('mysqltest');
        }

        public function testInit()
        {
            $this->cleanDirs();
            $this->initMysqlCache();
        }
    }
}
