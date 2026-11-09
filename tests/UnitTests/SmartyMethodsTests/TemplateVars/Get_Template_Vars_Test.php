<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests get_template_vars method
 */
class GetTemplateVarsTest extends PHPUnit_Smarty
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
     * test root get_template_vars single value
     */
    public function testGetSingleTemplateVarScopeRoot()
    {
        $this->smarty->assign('foo', 'bar');
        $this->smarty->assign('blar', 'buh');
        $this->assertEquals('bar', $this->smarty->get_template_vars('foo'));
    }

    /**
     * test root get_template_vars all values
     */
    public function testGetAllTemplateVarsScopeRoot()
    {
        $this->smarty->assign('foo', 'bar');
        $this->smarty->assign('blar', 'buh');
        $vars = $this->smarty->get_template_vars();
        $this->assertTrue(is_array($vars));
        $this->assertEquals('bar', $vars['foo']);
        $this->assertEquals('buh', $vars['blar']);
    }

    /**
     * test single variable with data object chain
     */
    public function testGetSingleTemplateVarScopeAll()
    {
        $data1 = $this->smarty->create_data($this->smarty);
        $data2 = $this->smarty->create_data($data1);
        $this->smarty->assign('foo', 'bar');
        $this->smarty->assign('blar', 'buh');
        $this->assertEquals('bar', $data2->get_template_vars('foo'));
    }

    /**
     * test get all variables with data object chain
     */
    public function testGetAllTemplateVarsScopeAll()
    {
        $data1 = $this->smarty->create_data($this->smarty);
        $data2 = $this->smarty->create_data($data1);
        $this->smarty->assign('foo', 'bar');
        $data1->assign('blar', 'buh');
        $data2->assign('foo2', 'bar2');
        $vars = $data2->get_template_vars(null);
        $this->assertTrue(is_array($vars));
        $this->assertEquals('bar', $vars['foo']);
        $this->assertEquals('bar2', $vars['foo2']);
        $this->assertEquals('buh', $vars['blar']);
    }

    /**
     * test get all variables with data object chain search parents disabled
     */
    public function testGetAllTemplateVarsScopeAllNoParents()
    {
        $data1 = $this->smarty->create_data($this->smarty);
        $data2 = $this->smarty->create_data($data1);
        $this->smarty->assign('foo', 'bar');
        $data1->assign('blar', 'buh');
        $data2->assign('foo2', 'bar2');
        $vars = $data2->get_template_vars(null, false);
        $this->assertTrue(is_array($vars));
        $this->assertFalse(isset($vars['foo']));
        $this->assertEquals('bar2', $vars['foo2']);
        $this->assertFalse(isset($vars['blar']));
    }

    /**
     * test get single variables with data object chain search parents disabled
     */
    public function testGetSingleTemplateVarsScopeAllNoParents()
    {
        error_reporting(error_reporting() & ~(E_NOTICE | E_USER_NOTICE));
        $data1 = $this->smarty->create_data($this->smarty);
        $data2 = $this->smarty->create_data($data1);
        $this->smarty->assign('foo', 'bar');
        $data1->assign('blar', 'buh');
        $data2->assign('foo2', 'bar2');
        $this->assertEquals('', $data2->get_template_vars('foo', false));
        $this->assertEquals('bar2', $data2->get_template_vars('foo2', false));
        $this->assertEquals('', $data2->get_template_vars('blar', false));
    }

    /**
     * test that variable assigned by global assign in template is included in get_template_vars
     */
    public function testAssignedInTemplate()
    {
        $this->smarty->fetch('string:{assign var="b" value="x" scope="global"}');
        $this->assertEquals('x', $this->smarty->get_template_vars('b'));
    }

    /**
     * test that get_template_vars returns simple array of values
     */
    public function testSimpleCallReturnsArrayWithAllValues()
    {
        $this->smarty->assign('foo', 'bar');
        $this->smarty->assign('i', 3);

        $vars = $this->smarty->get_template_vars();

        $this->assertArrayHasKey('foo', $vars);
        $this->assertArrayHasKey('i', $vars);
        $this->assertEquals('bar', $vars['foo']);
        $this->assertEquals(3, $vars['i']);
    }

}
