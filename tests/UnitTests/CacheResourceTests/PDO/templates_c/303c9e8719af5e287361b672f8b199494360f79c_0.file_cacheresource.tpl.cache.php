<?php
/* Smarty version 5.8.0, created on 2026-11-09 22:10:20
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_57b7b0a94f197e99dc372f4f',
  'has_nocache_code' => true,
  'file_dependency' => 
  array (
    '303c9e8719af5e287361b672f8b199494360f79c' => 
    array (
      0 => 'cacheresource.tpl',
      1 => 1794258616,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_57b7b0a94f197e99dc372f4f (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = '1d467a43ac910e356cbb0530378201ef';
?>
cache resource test:<?php echo '/*%%SmartyNocache:1d467a43ac910e356cbb0530378201ef%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:1d467a43ac910e356cbb0530378201ef%%*/';?>
 compiled:29 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
