<?php
/* Smarty version 5.8.0, created on 2026-11-09 23:08:40
  from 'string:{$temp = collect($data)}{$temp = $temp->filter()}{$temp->count()}' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_ee9eeef75f01eeaa005ec74b',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
  ),
  'includes' => 
  array (
  ),
))) {
function content_ee9eeef75f01eeaa005ec74b (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '.';
$_smarty_tpl->assign('temp', $_smarty_tpl->get_smarty()->get_modifier_callback('collect')($_smarty_tpl->get_value('data')), false, NULL);
$_smarty_tpl->assign('temp', $_smarty_tpl->get_value('temp')->filter(), false, NULL);
echo $_smarty_tpl->get_value('temp')->count();
}
}
