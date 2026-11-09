<?php
/* Smarty version 5.8.0, created on 2026-11-09 23:07:34
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_54e0550aa8ed6e112cc54e4a',
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
function content_54e0550aa8ed6e112cc54e4a (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = 'a65e01e74022a41ebafdc10f89ef4094';
?>
cache resource test:<?php echo '/*%%SmartyNocache:a65e01e74022a41ebafdc10f89ef4094%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:a65e01e74022a41ebafdc10f89ef4094%%*/';?>
 compiled:31 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
