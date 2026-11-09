<?php
/* Smarty version 5.8.0, created on 2026-11-09 23:07:42
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_f306143d78410527a271f310',
  'has_nocache_code' => true,
  'file_dependency' => 
  array (
    '303c9e8719af5e287361b672f8b199494360f79c' => 
    array (
      0 => 'cacheresource.tpl',
      1 => 1794262043,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_f306143d78410527a271f310 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = '593d7913c91dc4c69a7571810e4bf28d';
?>
cache resource test:<?php echo '/*%%SmartyNocache:593d7913c91dc4c69a7571810e4bf28d%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:593d7913c91dc4c69a7571810e4bf28d%%*/';?>
 compiled:2 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
