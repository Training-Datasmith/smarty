<?php

declare(strict_types=1);

/**
 * Tests the ::has_variable method
 */
class Has_Variable_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    public function testSimpleTrue()
    {
        $this->smarty->assign('foo', 'bar');
        $this->assertTrue($this->smarty->has_variable('foo'));
    }

    public function testSimpleFalse()
    {
        $this->smarty->assign('foo', 'bar');
        $this->assertFalse($this->smarty->has_variable('foox'));
    }

}
