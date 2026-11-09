<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compilation of append tags
 *

 * @author  Uwe Tews
 */

/**
 * class for append tags tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Compile_Append_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->load_plugins_dir_for_tests('../../../__shared/PHPunitplugins/');
        $this->smarty->add_template_dir('../../../__shared/templates/');
        $this->smarty->add_template_dir('./templates_tmp');
        $this->smarty->register_plugin('modifier', 'var_export', 'var_export');
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    /**
     * Test {append} tags
     *
     *
     *
     * @dataProvider data_test_append
     */
    public function testAppend($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "Append_{$name}.tpl";
        $this->make_template_file($file, $code);
        $this->smarty->assign('file', $file);
        $this->assertEquals($result, $this->smarty->fetch($file), "testAppend - {$code} - {$testName}");
    }

    /*
      * Data provider für testAppend
      */
    public function data_test_append()
    {
        $i = 0;
        /*
                    * Code
                    * result
                    * test name
                    */
        return [// old format
                     ['{$foo=1}{append var=foo value=2}{$foo|var_export:true}', var_export([0 => 1,1 => 2,], true), '', $i++],
                     ['{$foo[\'i\']=1}{append var=foo value=2 index=\'j\'}{$foo|var_export:true}', var_export(['i' => 1,'j' => 2,], true), '', $i++],
                     ['{$bar=\'j\'}{$foo[\'i\']=1}{append var=foo value=2 index=$bar}{$foo|var_export:true}', var_export(['i' => 1,'j' => 2,], true), '', $i++],
                     ['{append var=foo value=2}{$foo|var_export:true}', var_export([0 => 2,], true), '', $i++],
                     ['{append foo value=3}{$foo|var_export:true}', var_export([0 => 3,], true), '', $i++],
                     ['{append foo 5}{$foo|var_export:true}', var_export([0 => 5,], true), '', $i++], // new format
                     ['{$foo[]=2}{$foo|var_export:true}', var_export([0 => 2,], true), '', $i++],
        ];
    }

    /**
     * Test Assign spacings
     *
     *
     * @dataProvider data_test_spacing
     *
     */
    public function testAppendSpacing($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->make_template_file($file, $code);
        $this->smarty->set_template_dir('./templates_tmp');
        $this->smarty->assign('foo', 'bar');
        $this->assertEquals(
            $result,
            $this->smarty->fetch($file),
            "testSpacing - {$file}"
        );
    }
    /**
     * Test Append nocache spacings
     *
     *
     * @dataProvider data_test_spacing
     *
     */
    public function testAppendSpacingNocache($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->smarty->set_compile_id('1');
        $this->smarty->set_caching(1);
        $this->smarty->set_template_dir('./templates_tmp');
        $this->smarty->assign('foo', 'bar', true);
        $this->assertEquals(
            $result,
            $this->smarty->fetch($file),
            "testSpacing - {$file}"
        );
    }
    /**
     * Test Append nocache spacings
     *
     *
     * @dataProvider data_test_spacing
     *
     */
    public function testAppendSpacingNocache2($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->smarty->set_compile_id('1');
        $this->smarty->set_caching(1);
        $this->smarty->set_template_dir('./templates_tmp');

        $this->smarty->assign('foo', 'bar', true);
        $this->assertEquals(
            $result,
            $this->smarty->fetch($file),
            "testSpacing - {$file}"
        );

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
    public function data_test_spacing()
    {
        $i = 1;
        /*
                    * Code
                    * result
                    * test name
                    * test number
                    */
        return [
            ['A{append var=buh value=$foo}B{$buh[0]}', 'ABbar', 'Text', $i++],
            ["A\n{append var=buh value=\$foo}\nB{\$buh[0]}", "A\nBbar", 'Newline1', $i++],
            ["E{append var=buh value=\$foo}\nF{\$buh[0]}", 'EFbar', 'Newline2', $i++],
            ["G\n{append var=buh value=\$foo}H{\$buh[0]}", "G\nHbar", 'Newline3', $i++],
            ['A{$buh[]=$foo}B{$buh[0]}', 'ABbar', '2_Text', $i++],
            ["A\n{\$buh[]=\$foo}\nB{\$buh[0]}", "A\nBbar", '2_Newline1', $i++],
            ["E{\$buh[]=\$foo}\nF{\$buh[0]}", 'EFbar', '2_Newline2', $i++],
            ["G\n{\$buh[]=\$foo}H{\$buh[0]}", "G\nHbar", '2_Newline3', $i++],
        ];
    }
}
