<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compilation of registered object functions
 *

 * @author  Uwe Tews
 */

/**
 * class for registered object function tests
 *
 *
 *
 *
 */
class Compile_Registered_Object_Function_Test extends PHPUnit_Smarty
{
    /**
     * @var Reg_Object
     */
    private $object;

    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);

        $this->smarty->set_force_compile(true);
        $this->smarty->disable_security();
        $this->object = new Reg_Object();
        $this->smarty->register_object('objecttest', $this->object, 'myhello', true, 'myblock');
        $this->smarty->register_object('objectprop', $this->object);
    }

    public function testInit()
    {
        $this->clean_dirs();
    }
    /**
     * test resgistered object as function
     */
    public function testRegisteredObjectFunction()
    {
        $tpl = $this->smarty->create_template('eval:{objecttest->myhello}');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }

    /**
     * test resgistered object as function with modifier
     */
    public function testRegisteredObjectFunctionModifier()
    {
        $tpl = $this->smarty->create_template('eval:{objecttest->myhello|truncate:6}');
        $this->assertEquals('hel...', $this->smarty->fetch($tpl));
    }

    /**
     * test resgistered object as block function
     */
    public function testRegisteredObjectBlockFunction()
    {
        $tpl = $this->smarty->create_template('eval:{objecttest->myblock}hello world{/objecttest->myblock}');
        $this->assertEquals('block test', $this->smarty->fetch($tpl));
    }

    public function testRegisteredObjectBlockFunctionModifier1()
    {
        $tpl = $this->smarty->create_template('eval:{objecttest->myblock}hello world{/objecttest->myblock|upper}');
        $this->assertEquals(strtoupper('block test'), $this->smarty->fetch($tpl));
    }

    public function testRegisteredObjectBlockFunctionModifier2()
    {
        $tpl = $this->smarty->create_template('eval:{objecttest->myblock}hello world{/objecttest->myblock|default:""|upper}');
        $this->assertEquals(strtoupper('block test'), $this->smarty->fetch($tpl));
    }
    // TODO

    /**
    public function testRegisteredObjectProperty()
    {
        $tpl = $this->smarty->create_template('eval:{objectprop->prop}');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }

    public function testRegisteredObjectPropertyAssign()
    {
        $tpl = $this->smarty->create_template('eval:{objectprop->prop assign="foo"}{$foo}');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }
     */
}

class Reg_Object
{
    public $prop = 'hello world';

    public function myhello($params)
    {
        return 'hello world';
    }

    public function myblock($params, $content, &$smarty_tpl, &$repeat)
    {
        if (isset($content)) {
            $output = str_replace('hello world', 'block test', $content);

            return $output;
        }
    }
}
