<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Registered Function
 * Compiles code for the execution of a registered function
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile;

use Smarty\Compiler\Template;
use Smarty\Function_Handler\Attribute_Function_Handler_Interface;
/**
 * Smarty Internal Plugin Compile Registered Function Class
 */
class Function_Call_Compiler extends Base
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    public $optional_attributes = ['_any'];
    /**
     * Shorttag attribute order defined by its names
     *
     * @var array
     */
    protected $shorttag_order = [];
    /**
     * Compiles code for the execution of a registered function
     *
     * @param array $args array with attributes from parser
     * @param Template $compiler compiler object
     * @param array $parameter array with compilation parameter
     * @param string $tag name of tag
     * @param string $function name of function
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     * @throws \Smarty\Exception
     */
    public function compile($args, Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        if ($function_handler = $compiler->get_smarty()->get_function_handler($function)) {
            $attribute_overrides = [];
            if ($function_handler instanceof Attribute_Function_Handler_Interface) {
                $attribute_overrides = $function_handler->get_supported_attributes();
            }
            // check and get attributes
            $_attr = (new Attribute_Compiler($attribute_overrides['required_attributes'] ?? $this->required_attributes, $attribute_overrides['optional_attributes'] ?? $this->optional_attributes, $attribute_overrides['shorttag_order'] ?? $this->shorttag_order, $attribute_overrides['option_flags'] ?? $this->option_flags))->get_attributes($compiler, $args);
            unset($_attr['nocache']);
            $_params_array = $this->format_params_array($_attr);
            $_params = 'array(' . implode(',', $_params_array) . ')';
            // not cacheable?
            $compiler->tag_nocache = $compiler->tag_nocache || !$function_handler->is_cacheable();
            $output = '$_smarty_tpl->getSmarty()->getFunctionHandler(' . var_export($function, true) . ')';
            $output .= "->handle({$_params}, \$_smarty_tpl)";
        } else {
            $compiler->trigger_template_error("unknown function '{$function}'", null, true);
        }
        if (!empty($parameter['modifierlist'])) {
            return $compiler->compile_modifier($parameter['modifierlist'], $output);
        }
        return $output;
    }
}