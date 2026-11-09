<?php

declare (strict_types=1);
namespace Smarty\Compile;

class Default_Handler_Block_Compiler extends Block_Compiler
{
    /**
     * @inheritDoc
     */
    protected function get_is_callable_code($tag, $function): string
    {
        return "\$_smarty_tpl->get_smarty()->get_runtime('DefaultPluginHandler')->has_plugin(" . var_export($function, true) . ", 'block')";
    }
    /**
     * @inheritDoc
     */
    protected function get_full_callback_code($tag, $function): string
    {
        return "\$_smarty_tpl->get_smarty()->get_runtime('DefaultPluginHandler')->get_callback(" . var_export($function, true) . ", 'block')";
    }
    /**
     * @inheritDoc
     */
    protected function block_is_cacheable(\Smarty\Smarty $smarty, $function): bool
    {
        return true;
    }
}