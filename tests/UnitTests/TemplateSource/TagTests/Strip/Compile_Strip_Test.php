<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compilation of strip tags
 *

 * @author  Uwe Tews
 */

/**
 * class for strip tags tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Compile_Strip_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->load_plugins_dir_for_tests('../../../__shared/PHPunitplugins/');
        $this->smarty->add_template_dir('./templates_tmp');
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    /**
     * Test {strip} tags
     *
     * @not                 runInSeparateProcess
     *
     * @dataProvider data_test_strip
     */
    public function testStrip($code, $result, $testName, $test_number)
    {
        $file = "testStrip_{$test_number}.tpl";
        $this->make_template_file($file, "{strip}\n" . $code);
        $this->smarty->assign('file', $file);
        $this->assertEquals($result, $this->smarty->fetch($file), "testStrip - {$code} - {$testName}");
    }

    /*
      * Data provider für testStrip
      */
    public function data_test_strip()
    {
        $i = 0;
        /*
                    * Code
                    * result
                    * test name
                    */
        return [["    foo\n    bar  buh\n\n", 'foobar  buh', '', $i++],
                     ["\n   <div style=\"float: right; cursor: url;\">[<a\n    onmouseover=\"this.style.cursor='pointer'\"\n    onmouseup=\"document.getElementById('screenEdit_(\$screen.id)').style.display='none'\">X</a>]</div>\n\n\n   foo\n    bar\n",
                           '<div style="float: right; cursor: url;">[<a onmouseover="this.style.cursor=\'pointer\'" onmouseup="document.getElementById(\'screenEdit_($screen.id)\').style.display=\'none\'">X</a>]</div>foobar',
                           '', $i++],
                     ["\n    <ul>\n        <li>\n            <a href=\"#\">BlaBla</a>\n        </li>\n        <li>\n            <a href=\"#\">BlaBla</a>\n        </li>\n    </ul>\n",
                           '<ul><li><a href="#">BlaBla</a></li><li><a href="#">BlaBla</a></li></ul>', '', $i++],
                     ["\n            <textarea>\n\n                some text\n\n            </textarea>   foo\n    bar\n",
                           "<textarea>\n\n                some text\n\n            </textarea>   foobar", '', $i++],
                     // variable in html tag
                     ["\n    <b><c>c</c></b>\n", '<b><c>c</c></b>', '', $i++],
                     ["\n    <b> <c>c</c></b>\n", '<b> <c>c</c></b>', '', $i++],
                     ["\n    <b>\n<c>c</c></b>\n", '<b><c>c</c></b>', '', $i++],
                     ["{\$foo=1}\n    <b>{\$foo}<c>c</c></b>\n", '<b>1<c>c</c></b>', '', $i++],
                     ["{\$foo=1}\n    <b>{\$foo} <c>c</c></b>\n", '<b>1 <c>c</c></b>', '', $i++],
                     ["{\$foo=1}\n    <b>\n{\$foo} <c>c</c></b>\n", '<b>1 <c>c</c></b>', '', $i++],
                     ["\n<span>#</span>{'Text'}<span>#</span>\n", '<span>#</span>Text<span>#</span>', '', $i++],
                     ["\n<span>#</span> {'Text'}\n", '<span>#</span> Text', '', $i++],
                     ["\n<span>#</span> {'Text'}\n", '<span>#</span> Text', '', $i++],
                     ["\n<b></b><c></c>", '<b></b><c></c>', '', $i++],
                     ["{'Var'}\n<b></b> <c></c>", 'Var<b></b> <c></c>', '', $i++],
                     ["{'Var'}\n <b></b> <c></c>", 'Var<b></b> <c></c>', '', $i++],
                     ["\n<b></b>  <c></c>", '<b></b> <c></c>', '', $i++],
                     ["\n<b></b>\n  <c></c>", '<b></b><c></c>', '', $i++],
                     ["\n<b>\n  {* a comment *}\n   <c>", '<b><c>', '', $i++],
        ];
    }

}
