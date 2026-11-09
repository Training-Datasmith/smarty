<?php
/* Smarty version 5.8.0, created on 2026-11-09 22:10:27
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_625e43c4ba8e6f2208fbae00',
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
function content_625e43c4ba8e6f2208fbae00 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = '14a15783dd6a1e62a1eb05b142a603bd';
?>
cache resource test:<?php echo '/*%%SmartyNocache:14a15783dd6a1e62a1eb05b142a603bd%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:14a15783dd6a1e62a1eb05b142a603bd%%*/';?>
 compiled:31 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
