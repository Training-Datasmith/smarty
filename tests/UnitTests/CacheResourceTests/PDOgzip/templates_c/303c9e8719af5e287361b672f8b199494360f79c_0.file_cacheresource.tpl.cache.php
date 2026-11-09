<?php
/* Smarty version 5.8.0, created on 2026-11-09 23:08:10
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_f7b15a129eb4e8f44337aedb',
  'has_nocache_code' => true,
  'file_dependency' => 
  array (
    '303c9e8719af5e287361b672f8b199494360f79c' => 
    array (
      0 => 'cacheresource.tpl',
      1 => 1794262086,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_f7b15a129eb4e8f44337aedb (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = 'c908c774f37c63e8d28d39a2fc007fcb';
?>
cache resource test:<?php echo '/*%%SmartyNocache:c908c774f37c63e8d28d39a2fc007fcb%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:c908c774f37c63e8d28d39a2fc007fcb%%*/';?>
 compiled:29 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
