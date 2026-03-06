<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests {$smarty.post.foo}
 *

 * @author  Uwe Tews
 */

/**
 * class for {$smarty.post.foo} tests
 *
 *
 *
 *
 */
class PostTest extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
    }

    public function testInit()
    {
        $this->cleanDirs();
    }
    /**
     * test $_POST
     *
     *
     *
     * @dataProvider dataProvider
     */
    public function testPost($caching, $value)
    {
        $_POST['fooBar'] = $value;
        $this->smarty->caching = $caching;
        $this->assertEquals($value, $this->smarty->fetch('post.tpl'));
    }

    /**
     * test $_POST with modifier
     *
     *
     *
     * @dataProvider dataProviderModifier
     */
    public function testPostModifier($caching, $value, $result)
    {
        $_POST['fooBar'] = $value;
        $this->smarty->caching = $caching;
        $this->assertEquals($result, $this->smarty->fetch('post_modifier.tpl'));
    }

    /**
     * test variable post
     *
     */
    public function testPostVariable()
    {
        $_POST['fooBarVar'] = 'fooBarVarValue';
        $this->smarty->assign('foo', 'fooBarVar');
        $this->assertEquals('fooBarVarValue', $this->smarty->fetch('post_variable.tpl'));
    }
    /**
     * data provider
     */
    public function dataProvider()
    {
        return [
            'compile' => [false, 'buh'],
            'compiled' => [false, 'bar'],
            'create cache' => [true, 'cached buh'],
            'cacheded' => [true, 'cached bar'],
        ];
    }
    public function dataProviderModifier()
    {
        return [
            'compile' => [false, 'buh', 3],
            'compiled' => [false, 'bar1', 4],
            'create cache' => [true, 'cached buh', 10],
            'cacheded' => [true, 'cached bar1', 11],
        ];
    }

}
