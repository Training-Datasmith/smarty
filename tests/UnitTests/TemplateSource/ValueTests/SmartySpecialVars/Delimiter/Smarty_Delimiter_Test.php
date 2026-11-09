<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests {$smarty.ldelim} {$smarty.rdelim}
 *

 * @author  Uwe Tews
 */

/**
 * class for {$smarty.ldelim} {$smarty.rdelim} tests
 *
 *
 *
 *
 */
class Smarty_Delimiter_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testInit()
    {
        $this->clean_dirs();
    }
    /**
     * test {$smarty.ldelim} {$smarty.rdelim}
     *
     */
    public function testSmartyDelimiter()
    {
        $this->assertEquals('left = { right = }', $this->smarty->fetch('delimiter.tpl'));
    }
}
