<?php
/* Smarty version 5.8.0, created on 2026-11-09 22:11:14
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_1387854d2b7680c202fcadba',
  'has_nocache_code' => true,
  'file_dependency' => 
  array (
    '303c9e8719af5e287361b672f8b199494360f79c' => 
    array (
      0 => 'cacheresource.tpl',
      1 => 1794258659,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_1387854d2b7680c202fcadba (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = 'a8427196694ccbbf11a0673ce12f7612';
?>
cache resource test:<?php echo '/*%%SmartyNocache:a8427196694ccbbf11a0673ce12f7612%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:a8427196694ccbbf11a0673ce12f7612%%*/';?>
 compiled:32 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
