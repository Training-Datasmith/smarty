<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Modifier
 * Compiles code for modifier execution
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile;

use Smarty\Compiler_Exception;
/**
 * Smarty Internal Plugin Compile Modifier Class
 *
 */
class Modifier_Compiler extends Base
{
    /**
     * Compiles code for modifier execution
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     * @param array $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     * @throws \Smarty\Exception
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        $output = $parameter['value'];
        // loop over list of modifiers
        foreach ($parameter['modifierlist'] as $single_modifier) {
            /* @var string $modifier */
            $modifier = $single_modifier[0];
            $modifier_params = array_values($single_modifier);
            $modifier_params[0] = $output;
            $params = implode(',', $modifier_params);
            if (!is_object($compiler->get_smarty()->security_policy) || $compiler->get_smarty()->security_policy->is_trusted_modifier($modifier, $compiler)) {
                if ($handler = $compiler->get_modifier_compiler($modifier)) {
                    $output = $handler->compile($modifier_params, $compiler);
                } elseif ($compiler->get_smarty()->get_modifier_callback($modifier)) {
                    $output = sprintf('$_smarty_tpl->getSmarty()->getModifierCallback(%s)(%s)', var_export($modifier, true), $params);
                } elseif ($callback = $compiler->get_plugin_from_default_handler($modifier, \Smarty\Smarty::PLUGIN_MODIFIERCOMPILER)) {
                    $output = (new \Smarty\Compile\Modifier\Bc_Plugin_Wrapper($callback))->compile($modifier_params, $compiler);
                } elseif ($function = $compiler->get_plugin_from_default_handler($modifier, \Smarty\Smarty::PLUGIN_MODIFIER)) {
                    if (!is_array($function)) {
                        $output = "{$function}({$params})";
                    } else {
                        $operator = is_object($function[0]) ? '->' : '::';
                        $output = $function[0] . $operator . $function[1] . '(' . $params . ')';
                    }
                } else {
                    $compiler->trigger_template_error("unknown modifier '{$modifier}'", null, true);
                }
            }
        }
        return (string) $output;
    }
    /**
     * Wether this class will be able to compile the given modifier.
     *
     * @throws CompilerException
     */
    public function can_compile_for_modifier(string $modifier, \Smarty\Compiler\Template $compiler): bool
    {
        if ($compiler->get_modifier_compiler($modifier)) {
            return true;
        }
        if ($compiler->get_smarty()->get_modifier_callback($modifier)) {
            return true;
        }
        if ($compiler->get_plugin_from_default_handler($modifier, \Smarty\Smarty::PLUGIN_MODIFIERCOMPILER)) {
            return true;
        }
        return (bool) $compiler->get_plugin_from_default_handler($modifier, \Smarty\Smarty::PLUGIN_MODIFIER);
    }
}