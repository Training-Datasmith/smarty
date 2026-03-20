<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Object Block Function
 * Compiles code for registered objects as block function
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile;

/**
 * Smarty Internal Plugin Compile Object Block Function Class
 *
 */
class Object_Method_Block_Compiler extends Block_Compiler
{
    /**
     * @inheritDoc
     */
    protected function get_is_callable_code($tag, $function): string
    {
        $callback_object = "\$_smarty_tpl->getSmarty()->registered_objects['{$tag}'][0]";
        return "(isset({$callback_object}) && is_callable(array({$callback_object}, '{$function}')))";
    }
    /**
     * @inheritDoc
     */
    protected function get_full_callback_code($tag, $function): string
    {
        $callback_object = "\$_smarty_tpl->getSmarty()->registered_objects['{$tag}'][0]";
        return "{$callback_object}->{$function}";
    }
    /**
     * @inheritDoc
     */
    protected function block_is_cacheable(\Smarty\Smarty $smarty, $function): bool
    {
        return true;
    }
}