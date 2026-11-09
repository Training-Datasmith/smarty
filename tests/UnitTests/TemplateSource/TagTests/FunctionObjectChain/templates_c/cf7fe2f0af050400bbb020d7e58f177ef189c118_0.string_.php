<?php
/* Smarty version 5.8.0, created on 2026-11-09 22:11:33
  from 'string:{$x = collect($data)}{$x->filter()->values()->toJson()}' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_0ee2734157abece501658584',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
  ),
  'includes' => 
  array (
  ),
))) {
function content_0ee2734157abece501658584 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '.';
$_smarty_tpl->assign('x', $_smarty_tpl->get_smarty()->get_modifier_callback('collect')($_smarty_tpl->get_value('data')), false, NULL);
echo $_smarty_tpl->get_value('x')->filter()->values()->toJson();
}
}
