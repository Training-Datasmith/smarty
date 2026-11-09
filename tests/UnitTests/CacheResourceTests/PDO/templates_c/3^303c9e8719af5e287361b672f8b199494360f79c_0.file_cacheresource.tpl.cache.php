<?php
/* Smarty version 5.8.0, created on 2026-11-09 22:09:52
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_b70005421b957ddd48eab34f',
  'has_nocache_code' => true,
  'file_dependency' => 
  array (
    '303c9e8719af5e287361b672f8b199494360f79c' => 
    array (
      0 => 'cacheresource.tpl',
      1 => 1794258573,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_b70005421b957ddd48eab34f (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = '675de5b3820e6b2a814462ac29f0d58a';
?>
cache resource test:<?php echo '/*%%SmartyNocache:675de5b3820e6b2a814462ac29f0d58a%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:675de5b3820e6b2a814462ac29f0d58a%%*/';?>
 compiled:4 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
