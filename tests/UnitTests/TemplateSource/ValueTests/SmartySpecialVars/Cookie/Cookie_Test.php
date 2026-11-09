<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests {$smarty.cookies.foo}
 *

 * @author  Uwe Tews
 */

/**
 * class for $smarty.cookies.foo} tests
 *
 *
 *
 *
 */
class Cookie_Test extends PHPUnit_Smarty
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
     * test cookies
     *
     *
     *
     * @dataProvider data_provider
     */
    public function testCookie($caching, $value)
    {
        $_COOKIE['fooBar'] = $value;
        $this->smarty->caching = $caching;
        $this->assertEquals($value, $this->smarty->fetch('cookie.tpl'));
    }
    /**
     * test variable cookies
     *
      */
    public function testCookieVariable()
    {
        $_COOKIE['fooBarVar'] = 'fooBarVarValue';
        $this->smarty->assign('foo', 'fooBarVar');
        $this->assertEquals('fooBarVarValue', $this->smarty->fetch('cookie_variable.tpl'));
    }

    /**
     * test cookies with modifier
     *
     *
     *
     * @dataProvider data_provider_modifier
     */
    public function testCookieModifier($caching, $value, $result)
    {
        $_COOKIE['fooBar'] = $value;
        $this->smarty->caching = $caching;
        $this->assertEquals($result, $this->smarty->fetch('cookie_modifier.tpl'));
    }

    /**
     * data provider
     */
    public function data_provider()
    {
        return [
            'compile' => [false, 'buh'],
            'compiled' => [false, 'bar'],
            'create cache' => [true, 'cached buh'],
            'cacheded' => [true, 'cached bar'],
        ];
    }
    public function data_provider_modifier()
    {
        return [
            'compile' => [false, 'buh', 3],
            'compiled' => [false, 'bar1', 4],
            'create cache' => [true, 'cached buh', 10],
            'cacheded' => [true, 'cached bar1', 11],
        ];
    }

}
