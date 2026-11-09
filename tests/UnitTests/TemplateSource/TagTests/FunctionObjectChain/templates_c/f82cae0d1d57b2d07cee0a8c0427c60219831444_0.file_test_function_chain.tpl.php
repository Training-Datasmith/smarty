<?php
/* Smarty version 5.8.0, created on 2026-11-09 22:11:33
  from 'file:test_function_chain.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->get_compiled()->is_fresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_7bd397774d537f2cc84b1f08',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f82cae0d1d57b2d07cee0a8c0427c60219831444' => 
    array (
      0 => 'test_function_chain.tpl',
      1 => 1794257111,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_7bd397774d537f2cc84b1f08 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/app/tests/UnitTests/TemplateSource/TagTests/FunctionObjectChain/templates';
echo $_smarty_tpl->get_smarty()->get_modifier_callback('collect')($_smarty_tpl->get_value('data'))->filter()->values()->toJson();?>

<?php echo $_smarty_tpl->get_smarty()->get_modifier_callback('collect')($_smarty_tpl->get_value('data'))->filter()->count();
}
}
