<?php
/* Smarty version 5.8.0, created on 2026-11-09 22:11:33
  from 'string:{$filtered = collect($data)->filter()->values()->toArray()}{$filtered|@json_encode}' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_2f97ab413f46114f00135e82',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
  ),
  'includes' => 
  array (
  ),
))) {
function content_2f97ab413f46114f00135e82 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '.';
$_smarty_tpl->assign('filtered', $_smarty_tpl->get_smarty()->get_modifier_callback('collect')($_smarty_tpl->get_value('data'))->filter()->values()->toArray(), false, NULL);
echo json_encode($_smarty_tpl->get_value('filtered'));
}
}
