<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests resource plugins
 *

 * @author  Uwe Tews
 */
if (MysqlResourceEnable == true) {
    /**
     * class for resource plugins tests
     *
     *
     *
     *
     */
class Resource_Mysql_Plugin_Test extends PHPUnit_Smarty
    {
        public function setUp(): void
        {
            if (MysqlResourceEnable != true) {
                $this->markTestSkipped('Msqlresource tests are disabled');
            }
            if (self::$init) {
                $this->get_connection();
            }
            $this->set_up_smarty(__DIR__);
            $this->load_plugins_dir_for_tests('./PHPunitplugins/');
        }

        /**
        *
        */
        public function testInit()
        {
            $this->clean_dirs();
            $this->init_mysql_resource();
            PHPUnit_Smarty::$pdo->exec("REPLACE INTO templates (name, source) VALUES ('test.tpl', '{\$x = \'hello world\'}{\$x}')");
        }

        /**
        * test resource plugin rendering of a custom resource
        */
        public function testResourcePluginMysql()
        {
            $this->assertEquals('hello world', $this->smarty->fetch('mysqltest:test.tpl'));
        }

        /**
         * test must compile
         */
        public function testMustCompile()
        {
            $tpl = $this->smarty->create_template('mysqltest:test.tpl');
            $this->assertFalse($tpl->must_compile());
        }

        /**
        * test must compile
        * @group slow
        */
        public function testMustCompile2()
        {
            sleep(2);
            PHPUnit_Smarty::$pdo->exec("REPLACE INTO templates (name, source) VALUES ('test.tpl',  '{\$x = \'hello smarty\'}{\$x}' )");
            $tpl = $this->smarty->create_template('mysqltest:test.tpl');
            $this->assertTrue($tpl->must_compile());
        }

        /**
        * test resource plugin rendering of a custom resource
        */
        public function testResourcePluginMysql2()
        {
            $this->assertEquals('hello smarty', $this->smarty->fetch('mysqltest:test.tpl'));
        }

        /**
        * test clear compiled
        */
        public function testClearCompiled()
        {
            $this->assertEquals(1, $this->smarty->clear_compiled_template('mysqltest:test.tpl'));
        }

        /**
         * test must compile
         */
        public function testMustCompile3()
        {
            $tpl = $this->smarty->create_template('mysqltest:test.tpl');
            $this->assertTrue($tpl->must_compile());
        }

        /**
        * test resource plugin compiledFilepath of a custom resource
        */
        public function testResourcePluginMysqlCompiledFilepath()
        {
            //$this->load_plugins_dir_for_tests("./PHPunitplugins/");
            $tpl = $this->smarty->create_template('mysqltest:test.tpl');
            $this->assertEquals($this->build_compiled_path($tpl, false, false, null, 'test.tpl', 'mysqltest', $this->smarty->get_template_dir(0)), $tpl->get_compiled()->filepath);
        }

        public function testResourcePluginMysqlCompiledFilepathCache()
        {
            $this->smarty->caching = true;
            $this->smarty->cache_lifetime = 1000;
            $this->smarty->set_force_compile(true);
            $this->smarty->fetch('mysqltest:test.tpl');
            $tpl = $this->smarty->create_template('mysqltest:test.tpl');
            $this->assertEquals($this->build_compiled_path($tpl, false, true, null, 'test.tpl', 'mysqltest', $this->smarty->get_template_dir(0)), $tpl->get_compiled()->filepath);
            $this->smarty->caching = false;
        }

        /**
        * test unknown template
        */
        public function testUnknownTemplate()
        {
            $this->expectException(\Smarty\Exception::class);
            $this->expectExceptionMessage('Unable to load \'mysqlstest:foo.tpl\'');
            $this->assertEquals('foo', $this->smarty->fetch('mysqlstest:foo.tpl'));
        }
    }
}
