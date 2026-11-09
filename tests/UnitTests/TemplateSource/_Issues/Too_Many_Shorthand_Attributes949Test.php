<?php

declare(strict_types=1);
class Too_Many_Shorthand_Attributes949Test extends PHPUnit_Smarty
{
    public function testPregMatchAll()
    {
        $smarty = new \Smarty\Smarty();
        $smarty->register_plugin('modifier', 'var_dump', 'var_dump');
        $template_Str = "eval:{\$a = 'blah'}{\$b = array()}{if var_dump('', \$a, \$b, 2)|noprint}blah{else}nah{/if}";
        $this->assertEquals(
            'nah',
            $smarty->fetch($template_Str)
        );
    }

}
