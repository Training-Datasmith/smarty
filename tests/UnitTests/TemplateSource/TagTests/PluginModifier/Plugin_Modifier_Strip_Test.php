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
class Plugin_Modifier_Strip_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $tpl = $this->smarty->create_template('string:{" hello     spaced words  "|strip}');
        $this->assertEquals(' hello spaced words ', $this->smarty->fetch($tpl));
    }

    public function testUnicodeSpaces()
    {
        // Some Unicode Spaces
        $string = '&#8199;hello      spaced&#8196; &#8239;  &#8197;&#8199;  words  ';
        $string = html_entity_decode($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');
        $tpl = $this->smarty->create_template('string:{"' . $string . '"|strip}');
        $this->assertEquals(' hello spaced words ', $this->smarty->fetch($tpl));
    }

    public function testLinebreak()
    {
        $tpl = $this->smarty->create_template('string:{" hello
            spaced words  "|strip}');
        $this->assertEquals(' hello spaced words ', $this->smarty->fetch($tpl));
    }
}
