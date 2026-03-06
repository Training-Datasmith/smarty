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
class SpacingTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
    }

    public function testInit()
    {
        $this->cleanDirs();
    }

    /**
     * Test spacings
     *
     *
     * @dataProvider        dataTestSpacing
     *
     */
    public function testSpacing($code, $result, $testName, $testNumber)
    {
        $name = empty($testName) ? $testNumber : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->makeTemplateFile($file, $code);
        $this->smarty->setTemplateDir('./templates_tmp');
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
    public function dataTestSpacing()
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
