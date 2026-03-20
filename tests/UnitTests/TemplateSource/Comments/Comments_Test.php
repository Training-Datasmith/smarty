<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests comments in templates
 *

 * @author  Uwe Tews
 */

/**
 * class for security test
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class CommentsTest extends PHPUnit_Smarty
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
     * Test comments
     *
     *
     * @dataProvider        dataTestComments
     */
    public function testComments($code, $result, $testName, $testNumber)
    {
        $name = empty($testName) ? $testNumber : $testName;
        $file = "testComments_{$name}.tpl";
        $this->makeTemplateFile($file, $code);
        $this->smarty->setTemplateDir('./templates_tmp');
        $this->assertEquals(
            $result,
            $this->smarty->fetch($file),
            $file
        );
    }

    /*
      * Data provider für testComments
      */
    public function dataTestComments()
    {
        $i = 1;
        /*
                    * Code
                    * result
                    * test name
                    * test number
                    */
        return [['{* this is a comment *}', '', 'T1', $i++],
                     ['{* another $foo comment *}', '', 'T2', $i++],
                     ['{* another  comment *}some in between{* another  comment *}', 'some in between',
                           'T3', $i++],
                     ["{* multi line \n comment *}", '', 'T4', $i++],
                     ['{* /* foo * / *}', '', 'T5', $i++],
                     ["A{* comment *}B\nC", "AB\nC", 'T6', $i++],
                     ["D{* comment *}\n{* comment *}E\nF", "DE\nF", 'T7', $i++],
                     ["G{* multi \nline *}H", 'GH', 'T8', $i++],
                     ["I{* multi \nline *}\nJ", 'IJ', 'T9', $i++],
                     ["=\n{* comment *}\n{* comment *}\n    b\n{* comment *}\n{* comment *}\n=", "=\n    b\n=", 'T10', $i++],
                     ["=\na\n{* comment 1 *}\n{* comment 2 *}\n{* comment 3 *}\nb\n=", "=\na\nb\n=", 'T11', $i++],
                     ["=\na\n{* comment 1 *}\n {* comment 2 *}\n{* comment 3 *}\nb\n=", "=\na\n b\n=", 'T12', $i++],
                     ["=\na\n{* comment 1 *}\n{* comment 2 *} \n{* comment 3 *}\nb\n=", "=\na\n \nb\n=", 'T13', $i++],
                     ["=\na\n{* comment 1 *}\n {* comment 2 *} \n{* comment 3 *}\nb\n=", "=\na\n  \nb\n=", 'T14', $i++],
        ];
    }

    public function testTextComment5()
    {
        $this->assertEquals('IJ', $this->smarty->fetch('longcomment.tpl'), 'Comments longcomment.tpl');
    }
}
