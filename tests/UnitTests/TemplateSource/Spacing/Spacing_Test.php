<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests spacing in template output
 *

 * @author  Uwe Tews
 */

/**
 * class for spacing test
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Spacing_Test extends PHPUnit_Smarty
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
     * Test spacings
     *
     *
     * @dataProvider data_test_spacing
     *
     */
    public function testSpacing($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->make_template_file($file, $code);
        $this->smarty->set_template_dir('./templates_tmp');
        $this->smarty->assign('file', $file);
        $this->smarty->assign('foo', 'bar');
        $this->assertEquals(
            $result,
            $this->smarty->fetch($file),
            $file
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
        return [['{$foo}', 'bar', 'T1', $i++],
                     ['{$foo}{$foo}', 'barbar', 'T2', $i++],
                     ['A{$foo}{$foo}B', 'AbarbarB', 'T3', $i++],
                     ['{$foo} {$foo}', 'bar bar', 'T4', $i++],
                     ['A{$foo}B', 'AbarB', 'T5', $i++],
                     ['A{counter}B', 'A1B', 'T6', $i++],
                     ['A {$foo}B', 'A barB', 'T7', $i++],
                     ['A{$foo} B', 'Abar B', 'T8', $i++],
                     ["A{\$foo}\nB", "Abar\nB", 'T9', $i++],
                     ["A{counter start=1}\nB", "A1\nB", 'T10', $i++],
                     ["A{\$foo}B\nC", "AbarB\nC", 'T11', $i++],
                     ["A{assign var=zoo value='blah'}B", 'AB', 'T12', $i++],
                     ["A\n{assign var=zoo value='blah'}\nB", "A\nB", 'T13', $i++],
                     ["E{assign var=zoo value='blah'}\nF", 'EF', 'T14', $i++],
                     ["G\n{assign var=zoo value='blah'}H", "G\nH", 'T15', $i++],
        ];
    }
}
