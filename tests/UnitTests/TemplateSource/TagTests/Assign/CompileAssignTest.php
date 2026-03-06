<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compilation of assign tags
 *

 * @author  Uwe Tews
 */

/**
 * class for assign tags tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class CompileAssignTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
        $this->smarty->addPluginsDir('../../../__shared/PHPunitplugins/');
        $this->smarty->addTemplateDir('../../../__shared/templates/');
        $this->smarty->addTemplateDir('./templates_tmp');
        $this->smarty->registerPlugin('modifier', 'var_export', 'var_export');
    }

    public function testInit()
    {
        $this->cleanDirs();
    }

    /**
     * Test assign tags
     *
     * @not                 runInSeparateProcess
     *
     * @dataProvider        dataTestAssign
     */
    public function testAssign($code, $result, $testName, $testNumber)
    {
        $file = "Assign_{$testNumber}.tpl";
        $this->makeTemplateFile($file, $code);
        $this->smarty->assignGlobal('file', $file);
        $this->smarty->assign('bar', 'buh');
        $this->assertEquals($result, $this->smarty->fetch($file), "testAssign - {$code} - {$testName}");
    }

    /*
      * Data provider für testAssign
      */
    public function dataTestAssign()
    {
        $i = 1;
        /*
        * Code
        * result
        * test name
        */
        return [// old format
                     ['{assign var=foo value=1}{$foo}', '1', '', $i++],
                     ['{assign var=\'foo\' value=2}{$foo}', '2', '', $i++],
                     ['{assign var="foo" value=3}{$foo}', '3', '', $i++],
                     ['{assign var=foo value=$bar}{$foo}', 'buh', '', $i++],
                     ['{assign var=$bar value=11}{$buh}', '11', '', $i++],
                     ['{assign var=foo value=bar}{$foo}', 'bar', '', $i++],
                     ['{assign var=foo value=1+2}{$foo}', '3', '', $i++],
                     ['{assign var=foo value=strlen(\'barbuh\')}{$foo}', '6', '', $i++],
                     ['{assign var=foo value=\'barr\'|strlen}{$foo}', '4', '', $i++],
                     ['{assign var=foo value=[9,8,7,6]}{$foo|var_export:true}',
                           var_export([9, 8, 7, 6], true), '', $i++],
                     [
                         '{assign var=foo value=[\'a\'=>9,\'b\'=>8,\'c\'=>7,\'d\'=>6]}{$foo|var_export:true}',
                         var_export(['a' => 9, 'b' => 8, 'c' => 7, 'd' => 6,], true), '', $i++,],
                     ['{assign foo  value=1}{$foo}', '1', '', $i++],
                     ['{assign foo 1}{$foo}', '1', '', $i++],
                     // new format
                     ['{$foo=1}{$foo}', '1', '', $i++],
                     ['{$foo =2}{$foo}', '2', '', $i++],
                     ['{$foo=bar}{$foo}', 'bar', '', $i++],
                     ['{$foo=1+2}{$foo}', '3', '', $i++],
                     ['{$foo = 1+3}{$foo}', '4', '', $i++],
                     ['{$foo = 1 + 4}{$foo}', '5', '', $i++],
                     ['{$foo=strlen(\'bar\')}{$foo}', '3', '', $i++],
                     ['{$foo=\'bar\'|strlen}{$foo}', '3', '', $i++],
                     ['{$foo[\'a\'][4]=1}{$foo[\'a\'][4]}', '1', '', $i++],
                     ['{$foo=[9,8,7,6]}{$foo|var_export:true}', var_export([9, 8, 7, 6], true), '', $i++],
                     [
                         '{$foo=[\'a\'=>9,\'b\'=>8,\'c\'=>7,\'d\'=>6]}{$foo|var_export:true}',
                         var_export(['a' => 9, 'b' => 8, 'c' => 7, 'd' => 6,], true), '', $i++],
        ];
    }

    /**
     * Test Assign spacings
     *
     *
     * @dataProvider        dataTestSpacing
     *
     */
    public function testAssignSpacing($code, $result, $testName, $testNumber)
    {
        $name = empty($testName) ? $testNumber : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->makeTemplateFile($file, $code);
        $this->smarty->setTemplateDir('./templates_tmp');
        $this->smarty->assign('foo', 'bar');
        $this->assertEquals(
            $result,
            $this->smarty->fetch($file),
            "testSpacing - {$file}"
        );
    }
    /**
     * Test Output nocache spacings
     *
     *
     * @dataProvider        dataTestSpacing
     *
     */
    public function testAssignSpacingNocache($code, $result, $testName, $testNumber)
    {
        $name = empty($testName) ? $testNumber : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->smarty->setCompileId('1');
        $this->smarty->setCaching(1);
        $this->smarty->setTemplateDir('./templates_tmp');
        $this->smarty->assign('foo', 'bar', true);
        $this->assertEquals(
            $result,
            $this->smarty->fetch($file),
            "testSpacing - {$file}"
        );
    }
    /**
     * Test Output nocache spacings
     *
     *
     * @dataProvider        dataTestSpacing
     *
     */
    public function testAssignSpacingNocache2($code, $result, $testName, $testNumber)
    {
        $name = empty($testName) ? $testNumber : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->smarty->setCompileId('1');
        $this->smarty->setCaching(1);
        $this->smarty->setTemplateDir('./templates_tmp');
        $this->smarty->assign('foo', 'foo', true);
        $this->assertEquals(
            str_replace('bar', 'foo', $result),
            $this->smarty->fetch($file),
            "testSpacing - {$file}"
        );
    }

    /*
      * Data provider für testSpacing
      */
    public function dataTestSpacing()
    {
        $i = 1;
        /*
                    * Code
                    * result
                    * test name
                    * test number
                    */
        return [
                     ['A{assign var=buh value=$foo}B{$buh}', 'ABbar', 'Text1', $i++],
                     ["A\n{assign var=buh value=\$foo}\nB{\$buh}", "A\nBbar", 'Newline1', $i++],
                     ["E{assign var=buh value=\$foo}\nF{\$buh}", 'EFbar', 'Newline2', $i++],
                     ["G\n{assign var=buh value=\$foo}H{\$buh}", "G\nHbar", 'Newline3', $i++],
        ];
    }
}
