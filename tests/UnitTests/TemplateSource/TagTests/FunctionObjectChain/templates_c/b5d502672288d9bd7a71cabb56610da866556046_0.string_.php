<?php
/* Smarty version 5.8.0, created on 2026-11-09 23:08:40
  from 'string:{$result = collect($data)->filter()->values()->toJson()}{$result}' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_48fa4e6894a7ab4bb2e4470f',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
  ),
  'includes' => 
  array (
  ),
))) {
function content_48fa4e6894a7ab4bb2e4470f (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '.';
$_smarty_tpl->assign('result', $_smarty_tpl->get_smarty()->get_modifier_callback('collect')($_smarty_tpl->get_value('data'))->filter()->values()->toJson(), false, NULL);
echo $_smarty_tpl->get_value('result');
}
}
