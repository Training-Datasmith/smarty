<?php
/* Smarty version 5.8.0, created on 2026-11-09 23:06:59
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_7d9758b893bfe1f9c00b51bc',
  'has_nocache_code' => true,
  'file_dependency' => 
  array (
    '303c9e8719af5e287361b672f8b199494360f79c' => 
    array (
      0 => 'cacheresource.tpl',
      1 => 1794262000,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_7d9758b893bfe1f9c00b51bc (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = '5559e5a7c6e98ed35f5a8e8264c9e32a';
?>
cache resource test:<?php echo '/*%%SmartyNocache:5559e5a7c6e98ed35f5a8e8264c9e32a%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:5559e5a7c6e98ed35f5a8e8264c9e32a%%*/';?>
 compiled:4 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
