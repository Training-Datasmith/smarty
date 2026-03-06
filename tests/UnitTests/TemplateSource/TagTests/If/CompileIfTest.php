<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compilation of {if} tag
 *

 * @author  Uwe Tews
 */

/**
 * class for {if} tag tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class CompileIfTest extends PHPUnit_Smarty
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
     * Test if tags
     *
     * @not                 runInSeparateProcess
     *
     * @dataProvider        dataTestIf
     */
    public function testIf($code, $result, $testName, $testNumber)
    {
        $name = empty($testName) ? $testNumber : $testName;
        $file = "testIf_{$name}.tpl";
        $this->makeTemplateFile($file, $code);
        $this->smarty->assign('file', $file);
        $this->smarty->assign('bar', 'buh');
        $this->assertEquals($result, $this->smarty->fetch($file), "testIf - {$code} - {$name}");
    }

    /*
      * Data provider für testIf
      */
    public function dataTestIf()
    {
        $i = 1;
        /*
                    * Code
                    * result
                    * test name
                    */
        return [['{if 0<1}yes{/if}', 'yes', '', $i++],
                     ['{if false}false{elseif 0<1}yes{/if}', 'yes', '', $i++],
                     ['{if 2<1}yes{else}no{/if}', 'no', '', $i++],
                     ['{if 2<1}yes{elseif 4<5}yes1{else}no{/if}', 'yes1', '', $i++],
                     ['{if 2<1}yes{elseif 6<5}yes1{else}no{/if}', 'no', '', $i++],
                     ['{if false}false{elseif true}yes{else}no{/if}', 'yes', '', $i++],
                     ['{if false}yes{else}no{/if}', 'no', '', $i++],
                     ['{if !(1<2)}yes{else}no{/if}', 'no', '', $i++],
                     ['{if not (true)}yes{else}no{/if}', 'no', '', $i++],
                     ['{if 1 == 1}yes{else}no{/if}', 'yes', '', $i++],
                     ['{if 1 EQ 1}yes{else}no{/if}', 'yes', '', $i++],
                     ['{if 1 eq 1}yes{else}no{/if}', 'yes', '', $i++],
                     ['{$foo=true}{if $foo===true}yes{else}no{/if}', 'yes', '', $i++],
                     ['{$foo=true}{if $foo!==true}yes{else}no{/if}', 'no', '', $i++],
                     ['{if 1 > 0}yes{else}no{/if}', 'yes', '', $i++],
                     ['{if $x=1}yes{else}no{/if}{$x}', 'yes1', '', $i++],
                     ['{$x=0}{if $x++}yes{else}no{/if} {$x}', 'no 1', '', $i++],
                     ['{$x=[1,2]}{if $x[] = 7}{$x|var_export:true}{else}no{/if}', var_export([0 => 1,1 => 2,2 => 7,], true), '',
                           $i++],
                     ['{$x=[1,2]}{if $x[][\'a\'] = 7}{$x|var_export:true}{else}no{/if}',
                                         var_export([0 => 1,1 => 2,2 => ['a' => 7,],], true), '', $i++],
                     ['{$foo=\'foo\'}{$bar=\'bar\'}{if $bar = "new_{$foo|default:\'\'}"}yes-{else}no{/if}{$bar}',
                           'yes-new_foo', '', $i++],
                     ['{$foo=\'foo\'}{$bar=\'bar\'}{if false}false{elseif $bar = "new_{$foo|default:\'\'}"}yes-{else}no{/if}{$bar}',
                           'yes-new_foo', '', $i++],
                     ['{$foo=\'foo\'}{$bar=\'bar\'}{if false}false{elseif $bar[3] = "new_{$foo|default:\'\'}"}yes-{else}no{/if}{$bar[0]}-{$bar[3]}',
                           'yes-bar-new_foo', '', $i++],

                     ['{$x=0}{if $x}yes{else}no{/if}', 'no', 'AssignVar', $i++],
                     ['{$x=0}{if $x++}yes{else}no{/if} {$x}', 'no 1', 'IncVar', $i++],
                     ['{$x=1}{if $x}yes{else}no{/if}', 'yes', 'SimpleVar', $i++],
                     ['{if $x=true}yes{else}no{/if}', 'yes', 'AssignTrue', $i++],
                     ['{if $x=false}yes{else}no{/if}', 'no', 'AssignFalse', $i++],
                     ['{if 3 ge strlen("foo")}yes{else}no{/if}', 'yes', 'CmpWithFunc', $i++],
                     ['{if isset($foo)}yes{else}no{/if}', 'no', 'NotIsset', $i++],
                     ['{$foo=1}{if isset($foo)}yes{else}no{/if}', 'yes', 'Isset', $i++],
                     ['{$foo=1}{if !isset($foo)}yes{else}no{/if}', 'no', 'IssetNegate', $i++],
                     ['{$foo=\'\'}{if empty($foo)}yes{else}no{/if}', 'yes', 'Empty', $i++],
                     ['{$foo=\'foo\'}{if empty($foo)}yes{else}no{/if}', 'no', 'NotEmpty', $i++],
                     ['{if 6 is div by 3}yes{else}no{/if}', 'yes', 'IsDivBy', $i++],
                     ['{if 6 is not div by 3}yes{else}no{/if}', 'no', 'IsNotDivBye', $i++],
                     ['{if 6 is even}yes{else}no{/if}', 'yes', 'IsEven', $i++],
                     ['{if 6 is not even}yes{else}no{/if}', 'no', 'IsNotEven', $i++],
                     ['{if 3 is odd}yes{else}no{/if}', 'yes', 'IsOdd', $i++],
                     ['{if 3 is not odd}yes{else}no{/if}', 'no', 'IsNotOdd', $i++],

                     ['{if 0 is even by 3}yes{else}no{/if}', 'yes', 'IsEvenByTest0', $i++],
                     ['{if 1 is even by 3}yes{else}no{/if}', 'yes', 'IsEvenByTest1', $i++],
                     ['{if 2 is even by 3}yes{else}no{/if}', 'yes', 'IsEvenByTest2', $i++],
                     ['{if 3 is even by 3}yes{else}no{/if}', 'no', 'IsEvenByTest3', $i++],
                     ['{if 4 is even by 3}yes{else}no{/if}', 'no', 'IsEvenByTest4', $i++],
                     ['{if 5 is even by 3}yes{else}no{/if}', 'no', 'IsEvenByTest5', $i++],
                     ['{if 6 is even by 3}yes{else}no{/if}', 'yes', 'IsEvenByTest6', $i++],
                     ['{if 7 is even by 3}yes{else}no{/if}', 'yes', 'IsEvenByTest7', $i++],

                     ['{if 0 is odd by 3}yes{else}no{/if}', 'no', 'IsOddByTest0', $i++],
                     ['{if 1 is odd by 3}yes{else}no{/if}', 'no', 'IsOddByTest1', $i++],
                     ['{if 2 is odd by 3}yes{else}no{/if}', 'no', 'IsOddByTest2', $i++],
                     ['{if 3 is odd by 3}yes{else}no{/if}', 'yes', 'IsOddByTest3', $i++],
                     ['{if 4 is odd by 3}yes{else}no{/if}', 'yes', 'IsOddByTest4', $i++],
                     ['{if 5 is odd by 3}yes{else}no{/if}', 'yes', 'IsOddByTest5', $i++],
                     ['{if 6 is odd by 3}yes{else}no{/if}', 'no', 'IsOddByTest6', $i++],
                     ['{if 7 is odd by 3}yes{else}no{/if}', 'no', 'IsOddByTest7', $i++],

                     ['{if 2 is even by 3}yes{else}no{/if}', 'yes', 'IsEvenByVal1', $i++],
                     ['{if 3 is even by 2}yes{else}no{/if}', 'no', 'IsEvenByVal2', $i++],
                     ['{if 4 is even by 3}yes{else}no{/if}', 'no', 'IsEvenByVal3', $i++],
                     ['{$foo=3}{if 2 is even by $foo}yes{else}no{/if}', 'yes', 'IsEvenByVar1', $i++],
                     ['{$foo=2}{if 3 is even by $foo}yes{else}no{/if}', 'no', 'IsEvenByVar2', $i++],
                     ['{$foo=3}{if 4 is even by $foo}yes{else}no{/if}', 'no', 'IsEvenByVar3', $i++],
                     ['{if 2 is odd by 3}yes{else}no{/if}', 'no', 'IsOddByVal1', $i++],
                     ['{if 3 is odd by 2}yes{else}no{/if}', 'yes', 'IsOddByVal2', $i++],
                     ['{if 4 is odd by 3}yes{else}no{/if}', 'yes', 'IsOddByVal3', $i++],
                     ['{$foo=3}{if 2 is odd by $foo}yes{else}no{/if}', 'no', 'IsOddByVar1', $i++],
                     ['{$foo=2}{if 3 is odd by $foo}yes{else}no{/if}', 'yes', 'IsOddByVar2', $i++],
                     ['{$foo=3}{if 4 is odd by $foo}yes{else}no{/if}', 'yes', 'IsOddByVar3', $i++],
                     ['{$foo=3}{$bar=6}{if $bar is not odd by $foo}yes{else}no{/if}', 'yes', 'IsNotOddByVar', $i++],
                     ['{$foo=3}{$bar=3}{if 3+$bar is not odd by $foo}yes{else}no{/if}', 'yes', 'ExprIsNotOddByVar', $i++],
                     ['{$foo=2}{$bar=6}{if (3+$bar) is not odd by ($foo+1)}yes{else}no{/if}', 'no', 'ExprIsNotOddByExpr', $i++],
                     ['{if strlen("hello world") ===  11}yes{else}no{/if}', 'yes', 'FuncCmp', $i++],
                     ['{if 0>1}yes{else}no{/if}', 'no', 'GT2', $i++],
                     ['{if 1 GT 0}yes{else}no{/if}', 'yes', 'GT3', $i++],
                     ['{if 0 gt 1}yes{else}no{/if}', 'no', 'GT4', $i++],
                     ['{if 1 >= 0}yes{else}no{/if}', 'yes', 'GE1', $i++],
                     ['{if 1>=1}yes{else}no{/if}', 'yes', 'GE2', $i++],
                     ['{if 1 GE 1}yes{else}no{/if}', 'yes', 'GE3', $i++],
                     ['{if 0 ge 1}yes{else}no{/if}', 'no', 'GE4', $i++],
                     ['{if 0 < 0}yes{else}no{/if}', 'no', 'LT1', $i++],
                     ['{if 0<1}yes{else}no{/if}', 'yes', 'LT2', $i++],
                     ['{if 0 <= 0}yes{else}no{/if}', 'yes', 'LE1', $i++],
                     ['{if 0<=1}yes{else}no{/if}', 'yes', 'LE2', $i++],
                     ['{if 1 LE 0}yes{else}no{/if}', 'no', 'LE3', $i++],
                     ['{if 0 le 1}yes{else}no{/if}', 'yes', 'LE4', $i++],
                     ['{if 1 != 1}yes{else}no{/if}', 'no', 'NE1', $i++],
                     ['{if 1!=2}yes{else}no{/if}', 'yes', 'NE2', $i++],
                     ['{if 1 NE 1}yes{else}no{/if}', 'no', 'NE3', $i++],
                     ['{if 1 ne 2}yes{else}no{/if}', 'yes', 'NE4', $i++],
                     ['{if 1 === "1"}yes{else}no{/if}', 'no', 'Ident1', $i++],
                     ['{if "1" === "1"}yes{else}no{/if}', 'yes', 'Ident2', $i++],
                     ['{if 1 > 0 && 5 < 6}yes{else}no{/if}', 'yes', 'And1', $i++],
                     ['{if 1 > 0&&5 < 6}yes{else}no{/if}', 'yes', 'And2', $i++],
                     ['{if 1 > 0 AND 5 > 6}yes{else}no{/if}', 'no', 'And3', $i++],
                     ['{if (1 > 0) and (5 < 6)}yes{else}no{/if}', 'yes', 'And4', $i++],
                     ['{if 1 > 0 || 7 < 6}yes{else}no{/if}', 'yes', 'Or1', $i++],
                     ['{if 1 > 0||5 < 6}yes{else}no{/if}', 'yes', 'Or2', $i++],
                     ['{if 1 > 0 OR 5 > 6}yes{else}no{/if}', 'yes', 'Or3', $i++],
                     ['{if (0 > 0) or (9 < 6)}yes{else}no{/if}', 'no', 'Or4', $i++],
                     ['{if ((7>8)||(1 > 0)) and (5 < 6)}yes{else}no{/if}', 'yes', 'AndOr1', $i++],
                     ['{if {counter start=1} == 1}yes{else}no{/if}', 'yes', 'Tag1', $i++],
                     ['{if false}false{elseif {counter start=1} == 1}yes{else}no{/if}', 'yes', 'Tag2', $i++],
                     ['{if {counter start=1} == 0}false{elseif {counter} == 2}yes{else}no{/if}', 'yes', 'Tag3', $i++],

                     ['{if 2 is in ["foo", 2]}yes{else}no{/if}', 'yes', 'IsIn', $i++],
                     ['{if 2 is in ["foo", "bar"]}yes{else}no{/if}', 'no', 'IsIn2', $i++],
                     ['{if 2 is not in ["foo", "bar"]}yes{else}no{/if}', 'yes', 'IsNotIn', $i++],
                     ['{if 2 is not in ["foo", 2]}yes{else}no{/if}', 'no', 'IsNotIn2', $i++],

         ];
    }

    /**
     * Test if nocache tags
     *
     *
     *
     * @dataProvider        dataTestIfNocache
     */
    public function testIfNocache($var, $value, $code, $result, $testName, $testNumber, $file = null)
    {
        if (!isset($file)) {
            $file = "testIfNoCache_{$testNumber}.tpl";
        }
        if ($code) {
            $this->makeTemplateFile($file, $code);
        }
        $this->smarty->setCaching(true);
        $this->smarty->assign('file', $file, true);
        $this->smarty->assign($var, $value, true);
        $this->smarty->assign($var . '2', $value);
        $this->assertEquals(
            $result,
            $this->strip($this->smarty->fetch('run_code_caching.tpl')),
            "testIfNocache - {$code} - {$testName}"
        );
    }

    /*
      * Data provider für testIfNocache
      */
    public function dataTestIfNocache()
    {
        $i = 1;
        /*
         * var
         * value
                    * Code
                    * result
                    * test name
                    */
        return [['foo', true, '{if $foo}yes{else}no{/if}', 'yes', '', $i++, 'testIfNoCache_Var1.tpl'],
                     ['foo', true, false, 'yes', '', $i++, 'testIfNoCache_Var1.tpl'],
                     ['foo', false, false, 'no', '', $i++, 'testIfNoCache_Var1.tpl'],
                     ['foo', false, false, 'no', '', $i++, 'testIfNoCache_Var1.tpl'],
                     ['foo', true, '{$bar=$foo}{if $bar}yes{else}no{/if}', 'yes', '', $i++,
                           'testIfNoCache_Var2.tpl'],
                     ['foo', true, false, 'yes', '', $i++, 'testIfNoCache_Var2.tpl'],
                     ['foo', false, false, 'no', '', $i++, 'testIfNoCache_Var2.tpl'],
                     ['foo', false, false, 'no', '', $i++, 'testIfNoCache_Var2.tpl'],
                     ['foo', 1, '{if $bar=$foo}yes{else}no{/if}{$bar}', 'yes1', '', $i++,
                           'testIfNoCache_Var3.tpl'],
                     ['foo', 1, false, 'yes1', '', $i++, 'testIfNoCache_Var3.tpl'],
                     ['foo', 0, false, 'no0', '', $i++, 'testIfNoCache_Var3.tpl'],
                     ['foo', 0, false, 'no0', '', $i++, 'testIfNoCache_Var3.tpl'],
                     ['bar', 4, '{if $bar2=$bar+3}yes{else}no{/if}{$bar2}', 'yes7', '', $i++,
                           'testIfNoCache_Var4.tpl'],
                     ['bar', 4, false, 'yes7', '', $i++, 'testIfNoCache_Var4.tpl'],
                     ['bar', 0, false, 'yes3', '', $i++, 'testIfNoCache_Var4.tpl'],
                     ['bar', 0, false, 'yes3', '', $i++, 'testIfNoCache_Var4.tpl'],];
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
        $this->smarty->assign('bar', 'bar');
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
        return [['A{if false}false{elseif true}{$bar}{/if}C', 'AbarC', 'T1', $i++],
                     ["A{if false}false{elseif true}\n{\$bar}{/if}C", 'AbarC', 'T2', $i++],
                     ["A{if false}false{elseif true}{\$bar}\n{/if}C", "Abar\nC", 'T3', $i++],
                     ["A{if false}false{elseif true}\n{\$bar}\n{/if}C", "Abar\nC", 'T4', $i++],
                     ["A\n{if false}false{elseif true}{\$bar}{/if}C", "A\nbarC", 'T5', $i++],
                     ["A{if false}false{elseif true}{\$bar}{/if}\nC", 'AbarC', 'T6', $i++],
                     ['A{if false}false{elseif true}{$bar}{else}D{/if}C', 'AbarC', 'T7', $i++],
                     ["A{if false}false{elseif true}{\$bar}\n{else}D{/if}C", "Abar\nC", 'T8', $i++],
                     ['{if false}false{else}A{$bar}B{/if}', 'AbarB', 'T9', $i++],
                     ["{if false}false{else}\nA{\$bar}B{/if}", 'AbarB', 'T10', $i++],
                     ["{if false}false{else}A{\$bar}\nB{/if}", "Abar\nB", 'T11', $i++],
                     ["{if false}false{else}\nA{\$bar}\nB{/if}", "Abar\nB", 'T12', $i++],
                     ["{if false}false{else}{\$bar}\nB{/if}", "bar\nB", 'T13', $i++],
                     ['{if false}false{else}{$bar}{/if}', 'bar', 'T14', $i++],
                     ['A{if false}false{elseif true}{$bar}{/if}C', 'AbarC', 'T15', $i++],
                     ["A{if false}false{elseif true}\n{\$bar}{/if}C", 'AbarC', 'T16', $i++],
                     ["A{if false}false{elseif true}{\$bar}\n{/if}C", "Abar\nC", 'T17', $i++],
                     ["A{if false}false{elseif true}\n{\$bar}\n{/if}C", "Abar\nC", 'T18', $i++],
                     ["A\n{if false}false{elseif true}{\$bar}{/if}C", "A\nbarC", 'T19', $i++],
                     ["A{if false}false{elseif true}{\$bar}{/if}\nC", 'AbarC', 'T20', $i++],
                     ['A{if false}false{elseif true}{$bar}{else}D{/if}C', 'AbarC', 'T21', $i++],
                     ["A{if false}false{elseif true}{\$bar}\n{else}D{/if}C", "Abar\nC", 'T22', $i++],
        ];
    }

}
