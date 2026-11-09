<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of modifier
 *

 * @author  Rodney Rehm
 */

/**
 * class for modifier tests
 *
 *
 *
 *
 *
 *
 */
class Php_Function_Test extends PHPUnit_Smarty
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
     * test PHP empty() on variables true
     */
    public function testEmpty1()
    {
        $this->smarty->disable_security();
        $this->smarty->assign('var', [null,
                                           false,
                                           (int) 0,
                                           (float) 0.0,
                                           '',
                                           []]);
        $expected = ' true , true , true , true , true , true , true , true ';
        $this->assertEquals($expected, $this->smarty->fetch('string:{strip}{if empty($var[0])} true {else} false {/IF}
        ,{if empty($var[1])} true {else} false {/IF}
        ,{if empty($var[2])} true {else} false {/IF}
        ,{if empty($var[3])} true {else} false {/IF}
        ,{if empty($var[4])} true {else} false {/IF}
        ,{if empty($var[5])} true {else} false {/IF}
        ,{if empty($var[6])} true {else} false {/IF}
        ,{if empty($varr)} true {else} false {/IF}
        '));
    }

    /**
     * test PHP empty() on function result true
     */
    public function testEmpty2()
    {
        $this->smarty->disable_security();
        $this->get_smarty()->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'pass', function ($v) {
            return $v;
        });
        $this->smarty->assign('var', [null,
                                           false,
                                           (int) 0,
                                           (float) 0.0,
                                           '',
                                           []]);
        $expected = ' true , true , true , true , true , true ';
        $this->assertEquals($expected, $this->smarty->fetch('string:{strip}{if empty(pass($var[0]))} true {else} false {/IF}
        ,{if empty(pass($var[1]))} true {else} false {/IF}
        ,{if empty(pass($var[2]))} true {else} false {/IF}
        ,{if empty(pass($var[3]))} true {else} false {/IF}
        ,{if empty(pass($var[4]))} true {else} false {/IF}
        ,{if empty(pass($var[5]))} true {else} false {/IF}
        '));
    }
    /**
     * test PHP empty() on function result false
     */
    public function testEmpty3()
    {
        $this->smarty->disable_security();
        $this->get_smarty()->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'pass', function ($v) {
            return $v;
        });
        $this->smarty->assign('var', [true,
                                           (int) 1,
                                           (float) 0.1,
                                           ' ',
                                           [1]]);
        $expected = ' false , false , false , false , false ';
        $this->assertEquals($expected, $this->smarty->fetch('string:{strip}{if empty(pass($var[0]))} true {else} false {/IF}
        ,{if empty(pass($var[1]))} true {else} false {/IF}
        ,{if empty(pass($var[2]))} true {else} false {/IF}
        ,{if empty(pass($var[3]))} true {else} false {/IF}
        ,{if empty(pass($var[4]))} true {else} false {/IF}
        '));
    }
    /**
     * test PHP empty() on object
     */
    public function testEmpty4()
    {
        $this->smarty->disable_security();
        $this->get_smarty()->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'pass', function ($v) {
            return $v;
        });
        $this->smarty->assign('var', new Test_Isset());
        $expected = ' true , false , false , true , true , true , false ';
        $this->assertEquals($expected, $this->smarty->fetch('string:{strip}{if empty($var->is_null)} true {else} false {/IF}
        ,{if empty($var->is_set)} true {else} false {/IF}
        ,{if empty($var->arr[\'isSet\'])} true {else} false {/IF}
        ,{if empty($var->arr[\'isNull\'])} true {else} false {/IF}
        ,{if empty($var->arr[\'foo\'])} true {else} false {/IF}
        ,{if empty($var->pass(null))} true {else} false {/IF}
        ,{if empty($var->pass(1))} true {else} false {/IF}
        '));
    }
    /**
     * test PHP isset() on variables and functions
     */
    public function testIsset1()
    {
        $this->smarty->disable_security();
        $this->get_smarty()->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'pass', function ($v) {
            return $v;
        });
        $this->smarty->assign('isNull', null);
        $this->smarty->assign('isSet', 1);
        $this->smarty->assign('arr', ['isNull' => null, 'isSet' => 1]);
        $expected = ' false , true , false , true , false , false , false , true ';
        $this->assertEquals($expected, $this->smarty->fetch('string:{strip}{if isset($isNull)} true {else} false {/IF}
        ,{if isset($isSet)} true {else} false {/IF}
        ,{if isset($foo)} true {else} false {/IF}
        ,{if isset($arr[\'isSet\'])} true {else} false {/IF}
        ,{if isset($arr[\'isNull\'])} true {else} false {/IF}
        ,{if isset($arr[\'foo\'])} true {else} false {/IF}
        ,{if isset(pass(null))} true {else} false {/IF}
        ,{if isset(pass(1))} true {else} false {/IF}
        '));
    }
    /**
     * test PHP isset() on object
     */
    public function testIsset2()
    {
        $this->smarty->disable_security();
        $this->smarty->assign('var', new Test_Isset());
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'pass', function ($v) {
            return $v;
        });
        $expected = ' false , true , true , false , false , false , true ';
        $this->assertEquals($expected, $this->smarty->fetch('string:{strip}{if isset($var->is_null)} true {else} false {/IF}
        ,{if isset($var->is_set)} true {else} false {/IF}
        ,{if isset($var->arr[\'isSet\'])} true {else} false {/IF}
        ,{if isset($var->arr[\'isNull\'])} true {else} false {/IF}
        ,{if isset($var->arr[\'foo\'])} true {else} false {/IF}
        ,{if isset($var->pass(null))} true {else} false {/IF}
        ,{if isset($var->pass(1))} true {else} false {/IF}
        '));
    }

    /**
     * test PHP isset() on (non-)variables
     * @dataProvider data_test_isset3
     * @param string $str_Template template to test
     * @param string $result expected result
     */
    public function testIsset3($str_Template, $result)
    {
        $this->smarty->disable_security();
        $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'intval', 'intval');

        $this->smarty->assign('varobject', new Test_Isset());
        $this->smarty->assign('vararray', $vararray = [
            'keythatexists' => false,
            'keywitharray' => [1 => 1],
            'keywithobject' => new Test_Isset(),
        ]);

        $this->smarty->assign('key', 'a');
        $this->smarty->assign('_varsimple_a', 1);
        $this->smarty->assign('varsimple_b', 0);
        $this->smarty->assign('varsimple_c', null);

        $this->assertEquals($result, $this->smarty->fetch('string:' . $str_Template));
    }

    /**
     * Data provider for testIsset3
     */
    public function data_test_isset3()
    {
        return [
            ['{if isset($varobject->arr)}true{else}false{/if}', 'true'],
            ['{if isset($vararray["keywitharray"])}true{else}false{/if}', 'true'],
            ['{if isset($vararray["keythatexists"])}true{else}false{/if}', 'true'],
            ['{if isset($vararray["nonexistingkey"])}true{else}false{/if}', 'false'],
            ['{if isset($_GET["sscr6hr6cz34j6"])}true{else}false{/if}', 'false'],
            ['{if isset(count([\'hi\']))}true{else}false{/if}', 'true'],
            ['{if isset($vararray[\'keywitharray\'][intval(\'1\')])}true{else}false{/if}', 'true'],
            ['{if isset($vararray[\'keywithobject\']->arr[\'isSet\'])}true{else}false{/if}', 'true'],
            ['{if isset($vararray[\'keywithobject\']->arr[\'isNull\'])}true{else}false{/if}', 'false'],
            ['{if isset($varobject->arr[\'isSet\'])}true{else}false{/if}', 'true'],
            ['{if isset($varobject->arr[\'isNull\'])}true{else}false{/if}', 'false'],
            ['{if isset($_varsimple_a)}true{else}false{/if}', 'true'],
            ['{if isset($varsimple_b)}true{else}false{/if}', 'true'],
            ['{if isset($varsimple_c)}true{else}false{/if}', 'false'],
            ['{if isset($_varsimple_a && varsimple_b)}true{else}false{/if}', 'true'],
            ['{if isset($_varsimple_a && varsimple_c)}true{else}false{/if}', 'true'],
            ['{if isset($_varsimple_{$key})}true{else}false{/if}', 'true'],
        ];
    }
}

/**
 * Class Test_Isset
 */
class Test_Isset
{
    public $is_null = null;
    public $is_set = 1;
    public $arr = ['isNull' => null, 'isSet' => 1];

    /**
     * @param mixed $v
     *
     * @return mixed
     */
    public function pass($v)
    {
        return $v;
    }
}
