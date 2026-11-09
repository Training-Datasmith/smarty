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
 * @preserveGlobalState    disabled
 *
 */
class Plugin_Modifier_Capitalize_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $tpl = $this->smarty->create_template('string:{"next x-men fiLm, x3, delayed. ümlauts äre cööl."|capitalize}');
        $this->assertEquals('Next X-Men FiLm, x3, Delayed. Ümlauts Äre Cööl.', $this->smarty->fetch($tpl));
    }

    public function testDigits()
    {
        $tpl =
            $this->smarty->create_template('string:{"next x-men fiLm, x3, delayed. ümlauts äre cööl."|capitalize:true}');
        $this->assertEquals('Next X-Men FiLm, X3, Delayed. Ümlauts Äre Cööl.', $this->smarty->fetch($tpl));
    }

    public function testTrueCaptials()
    {
        $tpl =
            $this->smarty->create_template('string:{"next x-men fiLm, x3, delayed. ümlauts äre cööl."|capitalize:true:true}');
        $this->assertEquals('Next X-Men Film, X3, Delayed. Ümlauts Äre Cööl.', $this->smarty->fetch($tpl));
    }

    public function testQuotes()
    {
        $tpl = $this->smarty->create_template('string:{"next x-men fiLm, x3, \"delayed. umlauts\" foo."|capitalize}');
        $this->assertEquals('Next X-Men FiLm, x3, "Delayed. Umlauts" Foo.', $this->smarty->fetch($tpl));
        $tpl = $this->smarty->create_template('string:{"next x-men fiLm, x3, \'delayed. umlauts\' foo."|capitalize}');
        $this->assertEquals("Next X-Men FiLm, x3, 'Delayed. Umlauts' Foo.", $this->smarty->fetch($tpl));
    }

    public function testQuotesDigits()
    {
        $tpl =
            $this->smarty->create_template('string:{"next x-men fiLm, x3, \"delayed. umlauts\" foo."|capitalize:true}');
        $this->assertEquals('Next X-Men FiLm, X3, "Delayed. Umlauts" Foo.', $this->smarty->fetch($tpl));
        $tpl =
            $this->smarty->create_template('string:{"next x-men fiLm, x3, \'delayed. umlauts\' foo."|capitalize:true}');
        $this->assertEquals("Next X-Men FiLm, X3, 'Delayed. Umlauts' Foo.", $this->smarty->fetch($tpl));
    }

    public function testQuotesTrueCapitals()
    {
        $tpl =
            $this->smarty->create_template('string:{"next x-men fiLm, x3, \"delayed. umlauts\" foo."|capitalize:true:true}');
        $this->assertEquals('Next X-Men Film, X3, "Delayed. Umlauts" Foo.', $this->smarty->fetch($tpl));
        $tpl =
            $this->smarty->create_template('string:{"next x-men fiLm, x3, \'delayed. umlauts\' foo."|capitalize:true:true}');
        $this->assertEquals("Next X-Men Film, X3, 'Delayed. Umlauts' Foo.", $this->smarty->fetch($tpl));
    }

    public function testQuotesBeginning()
    {
        $tpl = $this->smarty->create_template('string:{"\"delayed. umlauts\" foo."|capitalize}');
        $this->assertEquals('"Delayed. Umlauts" Foo.', $this->smarty->fetch($tpl));
        $tpl = $this->smarty->create_template('string:{"\'delayed. umlauts\' foo."|capitalize}');
        $this->assertEquals("'Delayed. Umlauts' Foo.", $this->smarty->fetch($tpl));
    }

}
