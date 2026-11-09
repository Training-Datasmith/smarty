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
class Plugin_Modifier_Count_Words_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $result = '7';
        $tpl = $this->smarty->create_template('string:{"Dealers Will Hear Car Talk at Noon."|count_words}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

    public function testDashes()
    {
        $result = '7';
        $tpl = $this->smarty->create_template('string:{"Smalltime-Dealers Will Hear Car Talk at Noon."|count_words}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

    public function testUmlauts()
    {
        $result = '7';
        $tpl = $this->smarty->create_template('string:{"Dealers Will Hear Cär Talk at Nöön."|count_words}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

}
