<?php

declare(strict_types=1);

/**
 * Tests the ::hasVariable method
 */
class HasVariableTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
    }

    public function testInit()
    {
        $this->cleanDirs();
    }

    public function testSimpleTrue()
    {
        $this->smarty->assign('foo', 'bar');
        $this->assertTrue($this->smarty->hasVariable('foo'));
    }

    public function testSimpleFalse()
    {
        $this->smarty->assign('foo', 'bar');
        $this->assertFalse($this->smarty->hasVariable('foox'));
    }

}
