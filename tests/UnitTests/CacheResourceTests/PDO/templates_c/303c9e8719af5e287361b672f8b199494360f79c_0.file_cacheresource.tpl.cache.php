<?php
/* Smarty version 5.8.0, created on 2026-11-09 23:07:27
  from 'file:cacheresource.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_a69636e897688d48cf80273b',
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
function content_a69636e897688d48cf80273b (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/CacheResourceTests/_shared/templates';
$_smarty_tpl->get_compiled()->nocache_hash = 'c282531ef5c13e41844911ad7a017c4c';
?>
cache resource test:<?php echo '/*%%SmartyNocache:c282531ef5c13e41844911ad7a017c4c%%*/<?php echo $_smarty_tpl->get_value(\'test\');?>
/*/%%SmartyNocache:c282531ef5c13e41844911ad7a017c4c%%*/';?>
 compiled:29 rendered:<?php echo $_smarty_tpl->get_value('test');
}
}
