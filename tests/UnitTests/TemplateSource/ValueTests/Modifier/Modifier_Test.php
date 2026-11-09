<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of modifier
 *

 * @author  Uwe Tews
 */

/**
 * class for modifier tests
 *
 *
 *
 *
 */
class Modifier_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->add_template_dir('./templates_tmp');
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    /**
     * Test modifier
     *
     * @not                 runInSeparateProcess
     *
     * @dataProvider data_test_modifier
     */
    public function testModifier($code, $result, $testName, $test_number)
    {
        $name = empty($testName) ? $test_number : $testName;
        $file = "testModifier_{$name}.tpl";
        $this->make_template_file($file, $code);
        $this->smarty->assign('file', $file);
        $this->smarty->assign('bar', 'buh');
        $this->assertEquals(
            $result,
            $this->smarty->fetch($file),
            "testModifier - {$code} - {$name}"
        );
    }

    /*
      * Data provider für testModifier
      */
    public function data_test_modifier()
    {
        $i = 1;
        /*
                    * Code
                    * result
                    * test name
                    */
        return [['{"hello world"|strlen}', '11', 'OnString', $i++],
                     ['{$foo ="hello world"}{$foo|strlen}', '11', 'OnVar', $i++],
                     ['{"hello world"|truncate:6}', 'hel...', 'TruncatePlugin', $i++],
                     ['{$foo=7}{"hello world"|truncate:$foo}', 'hell...', 'TruncatePluginLengthVar', $i++],
                     ['{$foo=10}{$bar=\'<>\'}{"hello world"|truncate:$foo:$bar}', 'hello<>', 'TruncatePluginAllVar', $i++],
                     ['{"hello world"|truncate:6|strlen}', '6', 'Chain', $i++],
                     ['{"hello world"|truncate:6:"xx"|cat:"Smarty"}', 'hellxxSmarty', 'ChainVar', $i++],
                     ['{"hello world"|truncate:6|strlen}', '6', 'Chain', $i++],
                     ['{if "hello world"|truncate:6|strlen == 6}okay{/if}', 'okay', 'InIF', $i++],
                     ['{"hello world"|truncate:6|strlen + ("hello world"|truncate:8|strlen)}', '14', 'Expression', $i++],
                     ['{1.1*7.1|round}', '7.7', 'InExpression', $i++],
                     ['{counter|truncate:5 start=100000}', '10...', 'PluginOutput', $i++],
                     ['{1 + [1,2,3]|count}', '4', 'SumExpression', $i++],
       ];
    }

    /**
     * test registered modifier static class
     */
    public function testModifierRegisteredStaticClass()
    {
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier', ['testmodifierclass', 'staticcall']);
        $this->smarty->assign('foo', 1);
        $this->assertEquals('mymodifier static 1', $this->smarty->fetch('testModifier_RegisteredStatic.tpl'));
    }

    /**
     * test registered modifier method call
     */
    public function testModifierRegisteredMethodCall()
    {
        $obj = new testmodifierclass();
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier', [$obj, 'method']);
        $this->smarty->assign('foo', 3);
        $this->assertEquals('mymodifier method 3', $this->smarty->fetch('testModifier_RegisteredMethod.tpl'));
    }

    /**
     * test unknown modifier error
     */
    public function testUnknownModifier()
    {
        $this->expectException(\Smarty\Compiler_Exception::class);
        $this->expectExceptionMessage('unknown modifier \'unknown\'');
        $this->smarty->fetch('eval:{"hello world"|unknown}');
    }

    /**
     * test default modifier
     */
    public function testDefaultModifier()
    {
        $this->smarty->set_default_modifiers(['escape']);
        $this->smarty->assign('foo', '<bar>');
        $this->assertEquals('&lt;bar&gt;<bar>', $this->smarty->fetch('testModifier_Default.tpl'));
    }
}

function testmodifier($value)
{
    return "mymodifier function $value";
}

class testmodifierclass
{
    public static function staticcall($value)
    {
        return "mymodifier static $value";
    }

    public function method($value)
    {
        return "mymodifier method $value";
    }
}
