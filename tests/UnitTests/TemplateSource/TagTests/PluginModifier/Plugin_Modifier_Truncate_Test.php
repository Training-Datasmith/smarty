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
 */
class Plugin_Modifier_Truncate_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $result = 'Two Sisters Reunite after Eighteen Years at Checkout Counter.';
        $tpl = $this->smarty->create_template('string:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

    public function testLength()
    {
        $result = 'Two Sisters Reunite after...';
        $tpl = $this->smarty->create_template('string:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

    public function testEtc()
    {
        $result = 'Two Sisters Reunite after';
        $tpl = $this->smarty->create_template('string:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30:""}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

    public function testEtc2()
    {
        $result = 'Two Sisters Reunite after---';
        $tpl = $this->smarty->create_template('string:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30:"---"}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

    public function testBreak()
    {
        $result = 'Two Sisters Reunite after Eigh';
        $tpl = $this->smarty->create_template('string:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30:"":true}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

    public function testBreak2()
    {
        $result = 'Two Sisters Reunite after E...';
        $tpl = $this->smarty->create_template('string:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30:"...":true}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

    public function testMiddle()
    {
        $result = 'Two Sisters Re..ckout Counter.';
        $tpl = $this->smarty->create_template('string:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30:"..":true:true}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

}
