<?php
/* Smarty version 5.8.0, created on 2026-11-09 23:08:40
  from 'string:{collect($data)->filter()->values()->toJson()}' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_78584bd705a328fdc9fb5ecf',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
  ),
  'includes' => 
  array (
  ),
))) {
function content_78584bd705a328fdc9fb5ecf (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '.';
echo $_smarty_tpl->get_smarty()->get_modifier_callback('collect')($_smarty_tpl->get_value('data'))->filter()->values()->toJson();
}
}
