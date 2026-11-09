<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests of delimiter
 *

 * @author  Uwe Tews
 */

/**
 * class for delimiter tests
 *
 *
 *
 *
 */
class Delimiter_Test extends PHPUnit_Smarty
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
     * test <{ }> delimiter
     */
    public function testDelimiter1()
    {
        $this->smarty->set_left_delimiter('<{');
        $this->smarty->set_right_delimiter('}>');
        $tpl = $this->smarty->create_template('eval:start <{* comment *}>hello <{if true}><{"world"}><{/if}> end');
        $this->assertEquals('start hello world end', $this->smarty->fetch($tpl));
    }
    /**
     * test <{ }> delimiter
     */
    public function testDelimiter10()
    {
        $this->smarty->set_left_delimiter('<');
        $this->smarty->set_right_delimiter('>');
        $tpl = $this->smarty->create_template('eval:start <* comment *>hello <if 1 < 2><"world"></if> end');
        $this->assertEquals('start hello world end', $this->smarty->fetch($tpl));
    }

    /**
     * test <-{ }-> delimiter
     */
    public function testDelimiter2()
    {
        $this->smarty->set_left_delimiter('<-{');
        $this->smarty->set_right_delimiter('}->');
        $tpl = $this->smarty->create_template('eval:<-<-{* comment *}-><-{if true}-><-{"hello world"}-><-{/if}->->');
        $this->assertEquals('<-hello world->', $this->smarty->fetch($tpl));
    }

    /**
     * test <--{ }--> delimiter
     */
    public function testDelimiter3()
    {
        $this->smarty->set_left_delimiter('<--{');
        $this->smarty->set_right_delimiter('}-->');
        $tpl = $this->smarty->create_template('eval:<--{* comment *}--><--{if true}--><--{"hello world"}--><--{/if}-->');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }

    /**
     * test {{ }} delimiter
     */
    public function testDelimiter4()
    {
        $this->smarty->set_left_delimiter('{{');
        $this->smarty->set_right_delimiter('}}');
        $tpl = $this->smarty->create_template('eval:{{* comment *}}{{if true}}{{"hello world"}}{{/if}}');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }

    /**
     * test {= =} delimiter for conficts with option flags
     */
    public function testDelimiter5()
    {
        $this->smarty->set_left_delimiter('{=');
        $this->smarty->set_right_delimiter('=}');
        $tpl = $this->smarty->create_template('eval:{=assign var=foo value="hello world" nocache=}{=$foo=}');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }
    /**
     * test {= =} delimiter for conficts with option flags
     */
    public function testDelimiterIssue450()
    {
        $this->smarty->set_left_delimiter('{^');
        $this->smarty->set_right_delimiter('^}');
        $tpl = $this->smarty->create_template('eval:{^assign var=foo value="hello world" nocache^}{^$foo^}');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }
}
