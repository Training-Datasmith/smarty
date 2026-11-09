<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests clearing all assigned variables
 *

 * @author  Uwe Tews
 */

/**
 * class for clearing all assigned variables tests
 */
class Clear_All_Assign_Test extends PHPUnit_Smarty
{
    private $_data = null;
    private $_tpl = null;

    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);

        $this->smarty->assign('foo', 'foo');
        $this->_data = $this->smarty->create_data($this->smarty);
        $this->_data->assign('bar', 'bar');
        $this->_tpl = $this->smarty->create_template('eval:{$foo}{$bar}{$blar}', null, null, $this->_data);
        $this->_tpl->assign('blar', 'blar');
    }

    public function testInit()
    {
        $this->clean_dirs();
    }
    /**
     * test all variables accessable
     */
    public function testAllVariablesAccessable()
    {
        $this->assertEquals('foobarblar', $this->smarty->fetch($this->_tpl));
    }

    /**
     * test clear all assign in template
     */
    public function testClearAllAssignInTemplate()
    {
        error_reporting((error_reporting() & ~(E_NOTICE | E_USER_NOTICE | E_WARNING)));
        $this->_tpl->clear_all_assign();
        $this->assertEquals('foobar', $this->smarty->fetch($this->_tpl));
    }

    /**
     * test clear all assign in data
     */
    public function testClearAllAssignInData()
    {
        error_reporting((error_reporting() & ~(E_NOTICE | E_USER_NOTICE | E_WARNING)));
        $this->_data->clear_all_assign();
        $this->assertEquals('fooblar', $this->smarty->fetch($this->_tpl));
    }

    /**
     * test clear all assign in Smarty object
     */
    public function testClearAllAssignInSmarty()
    {
        error_reporting((error_reporting() & ~(E_NOTICE | E_USER_NOTICE | E_WARNING)));
        $this->smarty->clear_all_assign();
        $this->assertEquals('barblar', $this->smarty->fetch($this->_tpl));
    }
}
