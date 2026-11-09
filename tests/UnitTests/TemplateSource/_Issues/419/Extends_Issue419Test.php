<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests compiler errors
 *

 * @author  Uwe Tews
 */

/**
 * class for compiler tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Extends_Issue419Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    public function testextends419()
    {
        $this->smarty->set_left_delimiter('{{');
        $this->smarty->set_right_delimiter('}}');
        $this->assertEquals('child', $this->smarty->fetch('extends:001_parent.tpl|001_child.tpl'));
    }

    public function testextendsSecurity()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessageMatches('/Unable to load.*/');
        $this->assertEquals('child', $this->smarty->fetch('string:{include "001_parent.tpl\', var_dump(shell_exec(\'ls\')), 1, 2, 3);}}?>"}'));
    }

}
