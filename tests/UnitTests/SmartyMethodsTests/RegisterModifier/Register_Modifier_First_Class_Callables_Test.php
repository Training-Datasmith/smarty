<?php

declare(strict_types=1);
// first class callables where introduced in PHP 8.1
if (PHP_VERSION_ID >= 80100) {

    /**
     * class for register modifier with (first class) callables tests
     *
     * @runTestsInSeparateProcess
     * @preserveGlobalState disabled
     * @backupStaticAttributes enabled
     */
class Register_Modifier_First_Class_Callables_Test extends PHPUnit_Smarty
    {
        public function setUp(): void
        {
            $this->set_up_smarty(__DIR__);
        }

        public function testInit()
        {
            $this->clean_dirs();
        }

        public function testRegisterFirstClassCallable()
        {
            $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'testmodifier', eval('return strrev(...);'));
            $this->assertEquals('mosredna', $this->smarty->fetch('string:{"andersom"|testmodifier}'));
        }

        public function testRegisterFirstClassCallableSameName()
        {
            $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'mymodifier', eval('return strrev(...);'));
            $this->assertEquals('mosredna', $this->smarty->fetch('string:{"andersom"|mymodifier}'));
        }

        public function testRegisterFirstClassCallableAsFunc()
        {
            $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'kprint_r_out', eval('return strrev(...);'));
            $this->smarty->assign('my_var', 'andersom');
            $this->assertEquals('mosredna', $this->smarty->fetch('string:{kprint_r_out($my_var)}'));
        }

        public function testRegisterFirstClassCallableSameNameAsPhpFunc()
        {
            $this->smarty->register_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, 'mymodifierfcc', eval('return strrev(...);'));
            $this->assertEquals('mosredna', $this->smarty->fetch('string:{mymodifierfcc("andersom")}'));
        }

    }
}
function mymodifierfcc($a, $b, $c)
{
    return "$a function $b $c";
}
