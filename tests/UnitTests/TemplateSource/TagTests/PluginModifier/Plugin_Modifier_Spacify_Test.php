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
class Plugin_Modifier_Spacify_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testDefault()
    {
        $result = 'h e l l o   w ö r l d';
        $tpl = $this->smarty->create_template('string:{"hello wörld"|spacify}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }

    public function testCharacter()
    {
        $result = 'h##e##l##l##o## ##w##ö##r##l##d';
        $tpl = $this->smarty->create_template('string:{"hello wörld"|spacify:"##"}');
        $this->assertEquals(str_replace("\r", '', $result), $this->smarty->fetch($tpl));
    }
}
