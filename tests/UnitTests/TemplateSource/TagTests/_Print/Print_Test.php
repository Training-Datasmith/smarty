<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests variable output with nocache attribute
 *

 * @author  Uwe Tews
 */

/**
 * class for variable output with nocache attribute tag tests
 *
 *
 *
 *
 */
class Print_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testInit()
    {
        $this->clean_dirs();
    }
    /**
     * Test Output spacings
     *
     *
     * @dataProvider data_test_output_spacing
     *
     */
    public function testOutputSpacing($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "testSpacing_{$name}.tpl";
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
     * Test Output nocache spacings
     *
     *
     * @dataProvider data_test_output_spacing
     *
     */
    public function testOutputSpacingNocache($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "testSpacing_{$name}.tpl";
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
     * Test Output nocache spacings
     *
     *
     * @dataProvider data_test_output_spacing
     *
     */
    public function testOutputSpacingNocache2($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "testSpacing_{$name}.tpl";
        $this->smarty->set_compile_id('1');
        $this->smarty->set_caching(1);
        $this->smarty->set_template_dir('./templates_tmp');
        $this->smarty->assign('foo', 'foo', true);
        $this->assertEquals(
            str_replace('bar', 'foo', $result),
            $this->smarty->fetch($file),
            "testSpacing - {$file}"
        );
    }

    /*
      * Data provider für testOutputSpacing
      */
    public function data_test_output_spacing()
    {
        $i = 1;
        /*
                    * Code
                    * result
                    * test name
                    * test number
                    */
        return [['{$foo}', 'bar', 'Variable', $i++],
                     ['{$foo}{$foo}', 'barbar', 'twoVariables', $i++],
                     ['A{$foo}{$foo}B', 'AbarbarB', 'twoVariablesInText', $i++],
                     ['{$foo} {$foo}', 'bar bar', 'twoVariablesWithSpace', $i++],
                     ['A{$foo}B', 'AbarB', 'VariableInText1', $i++],
                     ['A {$foo}B', 'A barB', 'VariableInText2', $i++],
                     ['A{$foo} B', 'Abar B', 'VariableInText3', $i++],
                     ["A{\$foo}\nB", "Abar\nB", 'VariableInTextNewline1', $i++],
                     ["A{\$foo}B\nC", "AbarB\nC", 'VariableInTextNewline2', $i++],
        ];
    }
}
