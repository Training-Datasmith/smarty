<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests object variables
 *

 * @author  Uwe Tews
 */

/**
 * class for object variable tests
 *
 *
 *
 *
 */
class Object_Variable_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->set_force_compile(true);
    }

    public function testInit()
    {
        $this->clean_dirs();
    }
    /**
     * test simple object variable
     */
    public function testObjectVariableOutput()
    {
        $object = new Variable_Object();
        $tpl = $this->smarty->create_template('string:{$object->hello}');
        $tpl->assign('object', $object);
        $this->assertEquals('hello_world', $this->smarty->fetch($tpl));
    }

    /**
     * test simple object variable with variable property
     */
    public function testObjectVariableOutputVariableProperty()
    {
        $object = new Variable_Object();
        $this->smarty->disable_security();
        $tpl = $this->smarty->create_template('string:{$p=\'hello\'}{$object->$p}');
        $tpl->assign('object', $object);
        $this->assertEquals('hello_world', $this->smarty->fetch($tpl));
    }

    /**
     * test simple object variable with method
     */
    public function testObjectVariableOutputMethod()
    {
        $object = new Variable_Object();
        $tpl = $this->smarty->create_template('string:{$object->myhello()}');
        $tpl->assign('object', $object);
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }

    /**
     * test simple object variable with method
     */
    public function testObjectVariableOutputVariableMethod()
    {
        $object = new Variable_Object();
        $this->smarty->disable_security();
        $tpl = $this->smarty->create_template('string:{$p=\'myhello\'}{$object->$p()}');
        $tpl->assign('object', $object);
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }

    /**
     * test  object variable in double quoted string
     */
    public function testObjectVariableOutputDoubleQuotes()
    {
        $object = new Variable_Object();
        $tpl = $this->smarty->create_template('string:{"double quoted `$object->hello` okay"}');
        $tpl->assign('object', $object);
        $this->assertEquals('double quoted hello_world okay', $this->smarty->fetch($tpl));
    }

    /**
     * test  object variable in double quoted string as include name
     */
    public function testObjectVariableOutputDoubleQuotesInclude()
    {
        $object = new Variable_Object();
        $tpl = $this->smarty->create_template('string:{include file="`$object->hello`_test.tpl"}');
        $tpl->assign('object', $object);
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }
}

class Variable_Object
{
    public $hello = 'hello_world';

    public function myhello()
    {
        return 'hello world';
    }
}
