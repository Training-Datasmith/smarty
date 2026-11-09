<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compiler errors
 *

 * @author  Uwe Tews
 */

/**
 * class for compiler tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Newline_Spacing extends PHPUnit_Smarty
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
        return [
            ["=====================\n{if true}\n{foreach from=array(1) item='i'}\n    <htmltag />\n{/foreach}\n{/if}\n=====================", "=====================\n    <htmltag />\n=====================", 'T1', $i++],
            ["=====================\n{if true}\n{if true}\n    <htmltag />\n{/if}\n{/if}\n=====================", "=====================\n    <htmltag />\n=====================", 'T2', $i++],
            ["=====================\n{* comment *}\n{* comment *}\n    <htmltag />\n{* comment *}\n{* comment *}\n=====================", "=====================\n    <htmltag />\n=====================", 'T3', $i++],
            ["=====================\na\n{* comment 1 *}\n{* comment 2 *}\n{* comment 3 *}\nb\n=====================", "=====================\na\nb\n=====================", 'T4', $i++],
            ["=====================\na\nb{if true}\nd<span></span>\nf{/if}\nh\n=====================", "=====================\na\nbd<span></span>\nfh\n=====================", 'T5', $i++],
        ];
    }

}
