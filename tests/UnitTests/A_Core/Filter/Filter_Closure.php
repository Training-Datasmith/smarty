<?php

declare(strict_types=1);

/**
* test registered pre filter closure
* @requires PHP 5.3
*/

$this->smarty->register_filter(\Smarty\Smarty::FILTER_PRE, function ($input) {
    return '{$foo}' . $input;
});
$tpl = $this->smarty->create_template('eval:{" hello world"}');
$tpl->assign('foo', 'buh');
$this->assertEquals('buh hello world', $this->smarty->fetch($tpl));
