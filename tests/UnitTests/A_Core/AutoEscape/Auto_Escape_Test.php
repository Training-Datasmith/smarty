<?php

declare(strict_types=1);
/*
 * This file is part of the Smarty PHPUnit tests.
 */

/**
 * class for 'escapeHtml' property tests
 *
 *
 *
 *
 */
class AutoEscapeTest extends PHPUnit_Smarty
{
    /*
     * Setup test fixture
     */
    public function setUp(): void
    {
        $this->setUpSmarty(__DIR__);
        $this->smarty->set_escape_html(true);
    }

    /**
     * test 'escapeHtml' property
     */
    public function testAutoEscape()
    {
        $tpl = $this->smarty->create_template('eval:{$foo}');
        $tpl->assign('foo', '<a@b.c>');
        $this->assertEquals('&lt;a@b.c&gt;', $this->smarty->fetch($tpl));
    }

    /**
     * test 'escapeHtml' property
     * @group issue906
     */
    public function testAutoEscapeDoesNotEscapeFunctionPlugins()
    {
        $this->smarty->register_plugin(
            \Smarty\Smarty::PLUGIN_FUNCTION,
            'horizontal_rule',
            function ($params, $smarty) {
                return '<hr>';
            }
        );
        $tpl = $this->smarty->create_template('eval:{horizontal_rule}');
        $this->assertEquals('<hr>', $this->smarty->fetch($tpl));
    }

    /**
     * test 'escapeHtml' property
     * @group issue906
     */
    public function testAutoEscapeDoesNotEscapeBlockPlugins()
    {
        $this->smarty->register_plugin(
            \Smarty\Smarty::PLUGIN_BLOCK,
            'paragraphify',
            function ($params, $content) {
                return $content == null ? null : '<p>'.$content.'</p>';
            }
        );
        $tpl = $this->smarty->create_template('eval:{paragraphify}hi{/paragraphify}');
        $this->assertEquals('<p>hi</p>', $this->smarty->fetch($tpl));
    }

    /**
     * test autoescape + raw modifier
     */
    public function testAutoEscapeRaw()
    {
        $tpl = $this->smarty->create_template('eval:{$foo|raw}');
        $tpl->assign('foo', '<a@b.c>');
        $this->assertEquals('<a@b.c>', $this->smarty->fetch($tpl));
    }

    /**
     * test autoescape + escape modifier = no double-escaping
     */
    public function testAutoEscapeNoDoubleEscape()
    {
        $tpl = $this->smarty->create_template('eval:{$foo|escape}');
        $tpl->assign('foo', '<a@b.c>');
        $this->assertEquals('&lt;a@b.c&gt;', $this->smarty->fetch($tpl));
    }

    /**
     * test autoescape + escape modifier = force double-escaping
     */
    public function testAutoEscapeForceDoubleEscape()
    {
        $tpl = $this->smarty->create_template('eval:{$foo|escape:\'force\'}');
        $tpl->assign('foo', '<a@b.c>');
        $this->assertEquals('&amp;lt;a@b.c&amp;gt;', $this->smarty->fetch($tpl));
    }

    /**
     * test autoescape + escape modifier = special escape
     */
    public function testAutoEscapeSpecialEscape()
    {
        $tpl = $this->smarty->create_template('eval:{$foo|escape:\'url\'}');
        $tpl->assign('foo', 'aa bb');
        $this->assertEquals('aa%20bb', $this->smarty->fetch($tpl));
    }

    /**
     * test autoescape + escape modifier = special escape
     */
    public function testAutoEscapeSpecialEscape2()
    {
        $tpl = $this->smarty->create_template('eval:{$foo|escape:\'url\'}');
        $tpl->assign('foo', '<BR>');
        $this->assertEquals('%3CBR%3E', $this->smarty->fetch($tpl));
    }

    /**
     * test autoescape + escape modifier = special escape
     */
    public function testAutoEscapeSpecialEscape3()
    {
        $tpl = $this->smarty->create_template('eval:{$foo|escape:\'htmlall\'}');
        $tpl->assign('foo', '<BR>');
        $this->assertEquals('&lt;BR&gt;', $this->smarty->fetch($tpl));
    }

    /**
     * test autoescape + escape modifier = special escape
     */
    public function testAutoEscapeSpecialEscape4()
    {
        $tpl = $this->smarty->create_template('eval:{$foo|escape:\'javascript\'}');
        $tpl->assign('foo', '<\'');
        $this->assertEquals("<\\'", $this->smarty->fetch($tpl));
    }

}
