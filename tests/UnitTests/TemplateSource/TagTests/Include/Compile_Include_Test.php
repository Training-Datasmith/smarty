<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compilation of the {include} tag
 *

 * @author  Uwe Tews
 */

/**
 * class for {include} tests
 *
 *
 *
 *
 */
class Compile_Include_Test extends PHPUnit_Smarty
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
     * test spacing
     *
     *
     *
     * @dataProvider include_provider_caching
     */
    public function testSpacing_001($merge, $caching, $text)
    {
        $this->smarty->set_caching($caching);
        if ($merge) {
            $this->smarty->set_cache_id('1');
        }
        $this->smarty->set_merge_compiled_includes($merge);
        $content = $this->smarty->fetch('test_include_001.tpl');
        $this->assertEquals('I1I2I3', $content, $text);
    }

    /**
     * test spacing
     *
     *
     *
     * @dataProvider include_provider_caching
     */
    public function testSpacing_001V2($merge, $caching, $text)
    {
        $this->smarty->set_caching($caching);
        if ($merge) {
            $this->smarty->set_cache_id('1');
        }
        $this->smarty->set_merge_compiled_includes($merge);
        $content = $this->smarty->fetch('test_include_001V2.tpl');
        $this->assertEquals('I1I2I3', $content, $text);
    }

    /**
     * test spacing
     *
     *
     *
     * @dataProvider include_provider_caching
     */
    public function testSpacing_001V3($merge, $caching, $text)
    {
        $this->smarty->set_caching($caching);
        if ($merge) {
            $this->smarty->set_cache_id('1');
        }
        $this->smarty->set_merge_compiled_includes($merge);
        $content = $this->smarty->fetch('test_include_001V3.tpl');
        $this->assertEquals('I1I2I3', $content, $text);
    }

    /**
     * test template name escaping
     */
    public function testIncludeFilenameEscaping()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessageMatches('/Unable to load.*/');
        $tpl = $this->smarty->create_template('test_include_security.tpl');
        $content = $this->smarty->fetch($tpl);
        $this->assertEquals('hello world', $content);
    }

    /**
     * test standard output
     *
     *
     *
     * @dataProvider include_provider
     */
    public function testIncludeStandard_001($merge, $text)
    {
        $this->smarty->set_merge_compiled_includes($merge);
        $tpl = $this->smarty->create_template('test_include_standard.tpl');
        $content = $this->smarty->fetch($tpl);
        $this->assertEquals('hello world', $content, $text);
    }

    /**
     * test standard output var
     *
     *
     *
     * @dataProvider include_provider
     */
    public function testIncludeStandardNocacheVar($merge, $text)
    {
        $this->smarty->set_merge_compiled_includes(false);
        $this->smarty->caching = true;
        $this->smarty->assign('foo', 'foo', true);
        $tpl = $this->smarty->create_template('test_include_standard_nocache_var.tpl', $this->smarty);
        $content = $this->smarty->fetch($tpl);
        $this->assertEquals("foo\n\nhello world", $content, $text);
    }

    /**
     * Test that assign attribute does not create standard output
     *
     *
     *
     * @dataProvider include_provider
     */
    public function testIncludeAssign1($merge, $text)
    {
        $this->smarty->set_merge_compiled_includes($merge);
        $tpl = $this->smarty->create_template('test_include_assign1.tpl');
        $this->assertEquals('', $this->smarty->fetch($tpl), $text);
    }

    /**
     * Test that assign attribute does load variable
     *
     *
     *
     * @dataProvider include_provider
     */
    public function testIncludeAssign2($merge, $text)
    {
        $this->smarty->set_merge_compiled_includes($merge);
        $tpl = $this->smarty->create_template('test_include_assign2.tpl');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl), $text);
    }

    /**
     * Test passing local vars eval
     *
     *
     *
     * @dataProvider include_provider
     */
    public function testIncludePassVars($merge, $text)
    {
        //$this->smarty->caching = true;
        $this->smarty->set_merge_compiled_includes($merge);
        $tpl = $this->smarty->create_template('test_include_pass_vars.tpl');
        $this->assertEquals('12', $this->smarty->fetch($tpl), $text);
    }

    /**
     * Test passing local vars include
     *
     *
     *
     * @dataProvider include_provider
     */
    public function testIncludePassVars2($merge, $text)
    {
        $this->smarty->set_merge_compiled_includes($merge);
        $tpl = $this->smarty->create_template('test_include_pass_vars2.tpl');
        $this->assertEquals('12', $this->smarty->fetch($tpl), $text);
    }

    /**
     * Test  recursive includes
     *
     *
     *
     * @dataProvider include_provider
     */
    public function testRecursiveIncludes1($merge, $text)
    {
        $this->smarty->set_merge_compiled_includes($merge);
        $this->smarty->assign('foo', 1);
        $this->smarty->assign('bar', 'bar');
        $content = $this->smarty->fetch('test_recursive_includes.tpl');
        $this->assertStringContainsString("before 1 bar<br>\nbefore 2 bar<br>\nbefore 3 bar<br>\nafter 3 bar<br>\nafter 2 bar<br>\nafter 1 bar<br>", $content, $text);
    }

    /**
     * Test  recursive includes 2
     *
     *
     *
     * @dataProvider include_provider
     */
    public function testRecursiveIncludes2($merge, $text)
    {
        $this->smarty->set_merge_compiled_includes($merge);
        $this->smarty->assign('foo', 1);
        $this->smarty->assign('bar', 'bar');
        $content = $this->smarty->fetch('test_recursive_includes2.tpl');
        $this->assertStringContainsString("before 1 bar<br>\nbefore 3 bar<br>\nbefore 5 bar<br>\nafter 5 bar<br>\nafter 3 bar<br>\nafter 1 bar<br>", $content, $text);
    }

    /**
     * Include data provider
     */
    public function include_provider()
    {
        return [
            [false, 'normal'],
            [true, 'merged'],
        ];
    }

    /**
     * Include data provider caching
     *
     * @dataProvider file_provider
     */
    public function include_provider_caching($file)
    {
        return [
            [false, false, 'normal'],
            [true, false, 'merged'],
            [false, true, 'normal cached 1'],
            [false, true, 'normal cached 2'],
            [true, true, 'merged cached 1'],
            [true, true, 'merged cached 2'],
        ];
    }

    public function file_provider()
    {
        return [
            ['normal'],
            ['merged'],
        ];
    }
    /**
     * Test Include spacings
     *
     *
     * @dataProvider data_test_spacing
     *
     */
    public function testIncludeSpacing($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->make_template_file($file, $code);
        $this->smarty->add_template_dir('./templates_tmp');
        $this->smarty->assign('foo', 'bar');
        $this->assertEquals(
            $result,
            $this->smarty->fetch($file),
            "testSpacing - {$file}"
        );
    }
    /**
     * Test Output spacings
     *
     *
     * @dataProvider data_test_spacing
     *
     */
    public function testIncludeSpacingNocache($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->smarty->set_compile_id('1');
        $this->smarty->set_caching(1);
        $this->smarty->add_template_dir('./templates_tmp');
        $this->smarty->assign('foo', 'bar', true);
        $this->assertEquals(
            $result,
            $this->smarty->fetch($file),
            "testSpacing - {$file}"
        );
    }
    /**
     * Test Output spacings
     *
     *
     * @dataProvider data_test_spacing
     *
     */
    public function testIncludeSpacingNocache2($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "Spacing_{$name}.tpl";
        $this->smarty->set_compile_id('1');
        $this->smarty->set_caching(1);
        $this->smarty->add_template_dir('./templates_tmp');
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
        return [['A{include file=\'include_spacing2.tpl\'}B', 'A barB', '2_Text1', $i++],
                     ['A {include file=\'include_spacing2.tpl\'}B', 'A  barB', '2_Text2', $i++],
                     ['A{include file=\'include_spacing2.tpl\'}B', 'A barB', '2_Text3', $i++],
                     ["A{include file='include_spacing2.tpl'}\nB", 'A barB', '2_Newline1', $i++],
                     ["A\n{include file='include_spacing2.tpl'}\nB", "A\n barB", '2_Newline2', $i++],
                     ["A{include file='include_spacing2.tpl'}B\nC", "A barB\nC", '2_Newline3', $i++],
                     ['A{include file=\'include_spacing3.tpl\'}B', 'AbarB', '3_Text1', $i++],
                     ['A {include file=\'include_spacing3.tpl\'}B', 'A barB', '3_Text2', $i++],
                     ['A{include file=\'include_spacing3.tpl\'}B', 'AbarB', '3_Text3', $i++],
                     ["A{include file='include_spacing3.tpl'}\nB", 'AbarB', '3_Newline1', $i++],
                     ["A\n{include file='include_spacing3.tpl'}\nB", "A\nbarB", '3_Newline2', $i++],
                     ["A{include file='include_spacing3.tpl'}B\nC", "AbarB\nC", '3_Newline3', $i++],
        ];
    }

    public function testModifierWrongPlace()
    {
        $this->smarty->debugging = true;
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('No modifiers allowed');
        $this->smarty->fetch('include_with_modifier.tpl');
    }
}
