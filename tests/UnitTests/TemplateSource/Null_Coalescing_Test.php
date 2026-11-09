<?php

declare(strict_types=1);
class Null_Coalescing_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(sys_get_temp_dir());
        $this->clean_dirs();
    }

    public function testUndefined()
    {
        $tpl = $this->smarty->create_template('string:{$myvar ?? "undefined"}');
        $this->assertEquals('undefined', $this->smarty->fetch($tpl));
    }

    /**
     * @dataProvider data_for_other
     */
    public function testOther($value, $expected)
    {
        $tpl = $this->smarty->create_template('string:{$myvar ?? "undefined"}');
        $tpl->assign('myvar', $value);
        $this->assertEquals($expected, $this->smarty->fetch($tpl));
    }

    public function data_for_other()
    {
        return [
            [null, 'undefined'],
            ['blah', 'blah'],
            ['', ''],
            [false, false],
        ];
    }

}
