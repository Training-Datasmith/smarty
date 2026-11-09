<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for scopes
 *

 * @author  Uwe Tews
 */

/**
 * class scope tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Scope_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->load_plugins_dir_for_tests('../../__shared/PHPunitplugins/');
        $this->smarty->add_template_dir('../../__shared/templates/');
        $this->smarty->add_template_dir('./templates_tmp');
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    /**
     * Test scope
     *
     * @dataProvider data_test_append_scope
     */
    public function testAppendScope($code, $use_Smarty, $result, $testName, $test_number)
    {
        $file = "testAppendScope_{$test_number}.tpl";
        $this->make_template_file($file, $code . '{checkvar var=foo}');
        $this->smarty->assign('file', $file);
        $this->smarty->assign('foo', 'global');
        $data1 = $this->smarty->create_data($use_Smarty ? $this->smarty : null);
        $data = $this->smarty->create_data($data1);
        $data1->assign('foo', 'data1');
        $data->assign('foo', 'data');

        $tpl = $this->smarty->create_template('scope_tag.tpl', $data);

        $this->assertEquals($result, $this->smarty->fetch($tpl), "test - {$code} - {$testName}");
    }

    /**
     * Data provider for testAppendScope
     */
    public function data_test_append_scope()
    {
        $i = 0;
        /*
         * Code
         * use Smarty object
         * result
         * test name
         */
        return [
            [
                '{$foo[] = \'newvar\' scope=tpl_root}',
                true,
                '#testAppendScope_0.tpl:$foo =array(0=>\'data\',1=>\'newvar\',)' .
                '#scope_include.tpl:$foo =array(0=>\'data\',1=>\'newvar\',)' .
                '#scope_tag.tpl:$foo =array(0=>\'data\',1=>\'newvar\',)' .
                '#data:$foo =\'data\'' .
                '#data:$foo =\'data1\'' .
                '#global:$foo =\'global\'',
                '',
                $i++,
            ],
            [
                '{append var=foo value=\'newvar\' scope=tpl_root}', true,
                '#testAppendScope_1.tpl:$foo =array(0=>\'data\',1=>\'newvar\',)' .
                '#scope_include.tpl:$foo =array(0=>\'data\',1=>\'newvar\',)' .
                '#scope_tag.tpl:$foo =array(0=>\'data\',1=>\'newvar\',)' .
                '#data:$foo =\'data\'' .
                '#data:$foo =\'data1\'' .
                '#global:$foo =\'global\'',
                '',
                $i++,
            ],
            [
                '{append var=foo value=\'newvar\' scope=global}', true,
                '#testAppendScope_2.tpl:$foo =\'data\'' .
                '#scope_include.tpl:$foo =\'data\'' .
                '#scope_tag.tpl:$foo =\'data\'' .
                '#data:$foo =\'data\'' .
                '#data:$foo =\'data1\'' .
                '#global:$foo =array(0=>\'data\',1=>\'newvar\',)',
                '',
                $i++,
            ],
            [
                '{append var=foo value=\'newvar\' scope=global}', true,
                '#testAppendScope_3.tpl:$foo =\'data\'' .
                '#scope_include.tpl:$foo =\'data\'' .
                '#scope_tag.tpl:$foo =\'data\'' .
                '#data:$foo =\'data\'' .
                '#data:$foo =\'data1\'' .
                '#global:$foo =array(0=>\'data\',1=>\'newvar\',)',
                '',
                $i++,
            ],
        ];
    }

    /**
     * Test scope
     * @dataProvider data_test_assign_scope
     */
    public function testAssignScope($code, $use_Smarty, $result, $testName, $test_number)
    {
        $file = "testAssignScope_{$test_number}.tpl";
        $this->make_template_file($file, $code . '{checkvar var=foo}');

        $this->smarty->assign('foo', 'global');
        $data = $this->smarty->create_data($use_Smarty ? $this->smarty : null);
        $data->assign('file', $file);
        $data->assign('foo', 'data');

        $tpl = $this->smarty->create_template('scope_tag.tpl', $data);

        $this->assertEquals('#' . $file . $result, $this->smarty->fetch($tpl), "test - {$code} - {$testName}");
    }

    /*
     * Data provider for testAssignScope
     */
    public function data_test_assign_scope()
    {
        $i = 0;
        /*
         * Code
         * use Smarty object
         * result
         * test name
         */
        return [
            ['{$foo = \'newvar\'}', true,
                ':$foo =\'newvar\'#scope_include.tpl:$foo =\'data\'#scope_tag.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'default', $i++,],
            ['{assign var=foo value=\'newvar\'}', true,
                ':$foo =\'newvar\'#scope_include.tpl:$foo =\'data\'#scope_tag.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'assign tag', $i++,],
            ['{$foo = \'newvar\' scope=local}', true,
                ':$foo =\'newvar\'#scope_include.tpl:$foo =\'data\'#scope_tag.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'local', $i++,],
            ['{assign var=foo value=\'newvar\' scope=local}', true,
                ':$foo =\'newvar\'#scope_include.tpl:$foo =\'data\'#scope_tag.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'assign tag local', $i++,],
            ['{$foo = \'newvar\' scope=parent}', true,
                ':$foo =\'newvar\'#scope_include.tpl:$foo =\'newvar\'#scope_tag.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'parent', $i++,],
            ['{assign var=foo value=\'newvar\' scope=parent}', true,
                ':$foo =\'newvar\'#scope_include.tpl:$foo =\'newvar\'#scope_tag.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'assign tag parent', $i++,],
            ['{$foo = \'newvar\' scope=tpl_root}', true,
                ':$foo =\'newvar\'#scope_include.tpl:$foo =\'newvar\'#scope_tag.tpl:$foo =\'newvar\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'tpl_root', $i++,],
            ['{$foo = \'newvar\' scope=global}', true,
                ':$foo =\'data\'#scope_include.tpl:$foo =\'data\'#scope_tag.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'newvar\'',
                'global', $i++,],
            ['{$foo = \'newvar\' scope=root}', true,
                ':$foo =\'newvar\'#scope_include.tpl:$foo =\'newvar\'#scope_tag.tpl:$foo =\'newvar\'#data:$foo =\'newvar\'#global:$foo =\'global\'',
                'root', $i++,],
            ['{$foo = \'newvar\' scope=root}', false,
                ':$foo =\'newvar\'#scope_include.tpl:$foo =\'newvar\'#scope_tag.tpl:$foo =\'newvar\'#data:$foo =\'newvar\'#global:$foo =\'global\'',
                'root, no smarty', $i++,],
            ['{$foo = \'newvar\' scope=global}', false,
                ':$foo =\'data\'#scope_include.tpl:$foo =\'data\'#scope_tag.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'newvar\'',
                'global, no smarty', $i++,],
        ];
    }

    /**
     * Test scope nocache
     *
     *
     *
     * @dataProvider data_test_scope_nocache
     */
    public function testScopeNocache($var, $file, $result)
    {
        $this->smarty->set_caching(true);
        $this->smarty->assign('bar', $var, true);
        $this->smarty->assign('buh', $var);
        $this->smarty->assign('foo', 'smarty');
        $this->smarty->assign('foo', 'global');
        $this->assertEquals($result, $this->smarty->fetch($file), "test - {$file} {$var}");
    }

    /*
  * Data provider für testscopenocache
  */
    public function data_test_scope_nocache()
    {
        /*
         * variable value
         * result
         */
        return [['b1', 'test_scope_assignbar.tpl',
                           '#test_scope_assignbar.tpl:$foo =\'b1\'#global:$foo =\'b1\'',],
                     ['b2', 'test_scope_assignbar.tpl',
                           '#test_scope_assignbar.tpl:$foo =\'b2\'#global:$foo =\'b2\'',],
                     ['b1', 'test_scope_assignnocache.tpl',
                           '#test_scope_assignnocache.tpl:$foo =\'b1\'#global:$foo =\'b1\'',],
                     ['b2', 'test_scope_assignnocache.tpl',
                           '#test_scope_assignnocache.tpl:$foo =\'b2\'#global:$foo =\'b2\'',],];
    }

    /**
     * Test scope
     * @dataProvider data_test_include_scope
     */
    public function testIncludeScope($code, $use_Smarty, $result, $testName, $test_number = null)
    {
        $file = "testIncludeScope_{$test_number}.tpl";
        $this->make_template_file($file, $code);
        $this->smarty->assign('foo', 'global');
        $data = $this->smarty->create_data($use_Smarty ? $this->smarty : null);
        $data->assign('foo', 'data');
        $data->assign('file', $file);
        $tpl = $this->smarty->create_template('test_scope.tpl', $data);
        $this->assertEquals($result, $this->smarty->fetch($tpl), "test - {$code} - {$testName}");
    }

    /*
     * Data provider for testIncludeScope
     */
    public function data_test_include_scope()
    {
        $i = 0;
        return [
            /*
             * Code
             * use Smarty object
             * result
             * test name
             */
            ['{include \'test_scope_assign.tpl\'}', true,
                '#test_scope_assign.tpl:$foo =\'newvar\'#testIncludeScope_' . $i .
                '.tpl:$foo =\'data\'#test_scope.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'basic', $i++],
            ['{include \'test_scope_assign.tpl\' scope=parent}', true,
                '#test_scope_assign.tpl:$foo =\'newvar\'#testIncludeScope_' . $i .
                '.tpl:$foo =\'newvar\'#test_scope.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'parent scope', $i++],
            ['{include \'test_scope_assign.tpl\' scope=tpl_root}', true,
                '#test_scope_assign.tpl:$foo =\'newvar\'#testIncludeScope_' . $i .
                '.tpl:$foo =\'newvar\'#test_scope.tpl:$foo =\'newvar\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'tpl_root scope', $i++],
            ['{include \'test_scope_assign.tpl\' scope=root}', true,
                '#test_scope_assign.tpl:$foo =\'newvar\'#testIncludeScope_' . $i .
                '.tpl:$foo =\'newvar\'#test_scope.tpl:$foo =\'newvar\'#data:$foo =\'newvar\'#global:$foo =\'global\'',
                'root scope', $i++],
            ['{include \'test_scope_assign.tpl\' scope=root}', false,
                '#test_scope_assign.tpl:$foo =\'newvar\'#testIncludeScope_' . $i .
                '.tpl:$foo =\'newvar\'#test_scope.tpl:$foo =\'newvar\'#data:$foo =\'newvar\'#global:$foo =\'global\'',
                'root scope / no smarty', $i++],
            ['{include \'test_scope_assign.tpl\' scope=global}', true,
                '#test_scope_assign.tpl:$foo =\'data\'#testIncludeScope_' . $i .
                '.tpl:$foo =\'data\'#test_scope.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'newvar\'',
                'global scope', $i++],
            ['{include \'test_scope_pluginassign.tpl\' scope=global}', true,
                '#test_scope_pluginassign.tpl:$foo =\'data\'#testIncludeScope_' . $i .
                '.tpl:$foo =\'data\'#test_scope.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'newvar\'',
                'pluginassign global', $i++],
            ['{include \'test_scope_assign_noscope.tpl\' scope=root}', true,
                '#test_scope_assign_noscope.tpl:$foo =\'newvar\'#testIncludeScope_' . $i .
                '.tpl:$foo =\'data\'#test_scope.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'global\'',
                'noscope root', $i++],
        ];
    }

    /**
     * Test scope
     * @dataProvider data_test_config_scope
     */
    public function testConfigScope($code, $use_Smarty, $result, $testName, $test_number)
    {
        $file = "testConfigScope_{$test_number}.tpl";
        $this->make_template_file($file, $code . '{checkconfigvar var=foo}');
        $this->smarty->config_load('smarty.conf');
        $data = $this->smarty->create_data($use_Smarty ? $this->smarty : null);
        $data->assign('file', $file);
        $data->config_load('data.conf');
        $tpl = $this->smarty->create_template('scope_tag.tpl', $data);
        $this->assertEquals(
            '#' . $file . $result,
            $this->smarty->fetch($tpl),
            "test - {$code} - {$testName}
        "
        );
    }

    /*
     * Data provider for testConfigScope
     */
    public function data_test_config_scope()
    {
        $i = 0;
        /*
         * Code
         * use Smarty object
         * result
         * test name
         */
        return [
            ['{config_load \'template.conf\'}', true,
            ':$foo =\'newvar\'#scope_include.tpl:$foo =\'data\'#scope_tag.tpl:$foo =\'data\'#data:$foo =\'data\'#global:$foo =\'smarty\'',
            '', $i++,],
        ];
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFunctionScope()
    {
        $this->smarty->assign('scope', 'none');
        $r = $this->smarty->fetch('test_function_scope.tpl');
    }

    public function testFunctionScopeIsLocalByDefault()
    {
        $this->assertEquals(
            'a',
            $this->smarty->fetch('string:{function name=test}{$var="b"}{/function}{$var="a"}{test}{$var}')
        );
    }

}
