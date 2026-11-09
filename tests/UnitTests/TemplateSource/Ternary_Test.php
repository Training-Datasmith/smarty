<?php

declare(strict_types=1);
class Ternary_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(sys_get_temp_dir());
        $this->clean_dirs();
    }

    public function testTernaryTrue()
    {
        $tpl = $this->smarty->create_template('string:{$a ? $b : $c}');
        $tpl->assign('a', true);
        $tpl->assign('b', 'B');
        $tpl->assign('c', 'C');
        $this->assertEquals('B', $this->smarty->fetch($tpl));
    }

    public function testTernaryFalse()
    {
        $tpl = $this->smarty->create_template('string:{$a ? $b : $c}');
        $tpl->assign('a', false);
        $tpl->assign('b', 'B');
        $tpl->assign('c', 'C');
        $this->assertEquals('C', $this->smarty->fetch($tpl));
    }

    public function testShorthandTernaryTrue()
    {
        $tpl = $this->smarty->create_template('string:{$a ?: $c}');
        $tpl->assign('a', true);
        $tpl->assign('c', 'C');
        $this->assertEquals(true, $this->smarty->fetch($tpl));
    }

    public function testShorthandTernaryFalse()
    {
        $tpl = $this->smarty->create_template('string:{$a ? $b : $c}');
        $tpl->assign('a', false);
        $tpl->assign('c', 'C');
        $this->assertEquals('C', $this->smarty->fetch($tpl));
    }

}
