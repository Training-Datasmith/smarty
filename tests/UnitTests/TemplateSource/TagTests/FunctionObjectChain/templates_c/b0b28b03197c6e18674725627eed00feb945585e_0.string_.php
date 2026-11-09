<?php
/* Smarty version 5.8.0, created on 2026-11-09 22:11:33
  from 'string:{collect($data)->filter()->values()->toJson()}' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_c774f620f1cd5c7e116694a8',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
  ),
  'includes' => 
  array (
  ),
))) {
function content_c774f620f1cd5c7e116694a8 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '.';
echo $_smarty_tpl->get_smarty()->get_modifier_callback('collect')($_smarty_tpl->get_value('data'))->filter()->values()->toJson();
}
}
