<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for compile check
 */
class Compile_Check_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->add_template_dir('./templates_tmp');
        $this->clean_dirs();
    }

    /**
     * generate templates
     */
    protected function make_files()
    {
        file_put_contents('./templates_tmp/t1.tpl', 'TPL1');
        file_put_contents('./templates_tmp/t2.tpl', 'TPL2');
        file_put_contents('./templates_tmp/base.tpl', '{include file="t1.tpl"}{include file="t2.tpl"}');
    }
    /**
     * remove generated templates
     */
    protected function remove_files()
    {
        unlink('./templates_tmp/t1.tpl');
        unlink('./templates_tmp/t2.tpl');
        unlink('./templates_tmp/base.tpl');
    }

    /**
     * reset, but leave the files alone
     * @return void
     */
    private function soft_reset_smarty()
    {
        $this->smarty = new \Smarty\Smarty();
        $this->smarty->add_template_dir('./templates_tmp');
    }

    /**
     * @group slow
     */
    public function testCompileCheckOn0()
    {
        $this->make_files();
        $this->assertEquals('TPL1TPL2', $this->smarty->fetch('base.tpl'));

        $this->soft_reset_smarty();
        $this->smarty->set_compile_check(\Smarty\Smarty::COMPILECHECK_ON);

        $this->assertEquals('TPL1TPL2', $this->smarty->fetch('base.tpl'));
    }

    /**
     * @group slow
     */
    public function testCompileCheckOn1()
    {
        $this->make_files();
        $this->assertEquals('TPL1TPL2', $this->smarty->fetch('base.tpl'));

        $this->soft_reset_smarty();
        $this->smarty->set_compile_check(\Smarty\Smarty::COMPILECHECK_ON);

        unlink('./templates_tmp/base.tpl');
        sleep(1);

        $this->expectException(\Smarty\Exception::class);
        $this->smarty->fetch('base.tpl');
    }

    /**
     * @group slow
     */
    public function testCompileCheckOn2()
    {
        $this->make_files();
        $this->assertEquals('TPL1TPL2', $this->smarty->fetch('base.tpl'));

        $this->soft_reset_smarty();
        $this->smarty->set_compile_check(\Smarty\Smarty::COMPILECHECK_ON);

        sleep(1);
        file_put_contents('./templates_tmp/base.tpl', 'hello');

        $this->assertEquals('hello', $this->smarty->fetch('base.tpl'));
    }

    /**
     * @group slow
     */
    public function testCompileCheckOff0()
    {
        $this->make_files();
        $this->assertEquals('TPL1TPL2', $this->smarty->fetch('base.tpl'));

        $this->soft_reset_smarty();
        $this->smarty->set_compile_check(\Smarty\Smarty::COMPILECHECK_OFF);

        $this->assertEquals('TPL1TPL2', $this->smarty->fetch('base.tpl'));
    }

    /**
     * @group slow
     */
    public function testCompileCheckOff1()
    {
        $this->make_files();
        $this->assertEquals('TPL1TPL2', $this->smarty->fetch('base.tpl'));

        $this->soft_reset_smarty();
        $this->smarty->set_compile_check(\Smarty\Smarty::COMPILECHECK_OFF);

        unlink('./templates_tmp/base.tpl');
        sleep(1);

        $this->assertEquals('TPL1TPL2', $this->smarty->fetch('base.tpl'));
    }

    /**
     * @group slow
     */
    public function testCompileCheckOff2()
    {
        $this->make_files();
        $this->assertEquals('TPL1TPL2', $this->smarty->fetch('base.tpl'));

        $this->soft_reset_smarty();
        $this->smarty->set_compile_check(\Smarty\Smarty::COMPILECHECK_OFF);

        sleep(1);
        file_put_contents('./templates_tmp/base.tpl', 'hello');

        $this->assertEquals('TPL1TPL2', $this->smarty->fetch('base.tpl'));
    }

}
