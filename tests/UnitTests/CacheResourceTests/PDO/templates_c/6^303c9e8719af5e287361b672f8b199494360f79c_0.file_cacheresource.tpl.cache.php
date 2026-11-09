<?php
/* Smarty version 5.8.0, created on 2026-11-09 23:07:38
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_290797f1727519096a9b6809',
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
function content_290797f1727519096a9b6809 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = 'b2c1abdbc316cc33d09af126c2bd3655';
?>
cache resource test:<?php echo '/*%%SmartyNocache:b2c1abdbc316cc33d09af126c2bd3655%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:b2c1abdbc316cc33d09af126c2bd3655%%*/';?>
 compiled:32 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
