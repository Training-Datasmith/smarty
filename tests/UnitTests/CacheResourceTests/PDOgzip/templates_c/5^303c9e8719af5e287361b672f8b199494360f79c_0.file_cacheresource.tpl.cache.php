<?php
/* Smarty version 5.8.0, created on 2026-11-09 23:08:17
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_3a3e1248e4f92ae185037ef7',
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
function content_3a3e1248e4f92ae185037ef7 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = '0cc3ae683dad3afd628b8845ede82435';
?>
cache resource test:<?php echo '/*%%SmartyNocache:0cc3ae683dad3afd628b8845ede82435%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:0cc3ae683dad3afd628b8845ede82435%%*/';?>
 compiled:31 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
