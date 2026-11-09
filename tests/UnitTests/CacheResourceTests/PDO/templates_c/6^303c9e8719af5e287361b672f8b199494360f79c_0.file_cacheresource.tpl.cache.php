<?php
/* Smarty version 5.8.0, created on 2026-11-09 22:10:31
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6bfe1047b5dfa99c6e3a9047',
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
function content_6bfe1047b5dfa99c6e3a9047 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = 'fb4fa9f9fa0995da29690fbfd06663dd';
?>
cache resource test:<?php echo '/*%%SmartyNocache:fb4fa9f9fa0995da29690fbfd06663dd%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:fb4fa9f9fa0995da29690fbfd06663dd%%*/';?>
 compiled:32 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
