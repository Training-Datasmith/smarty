<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Object Function
 * Compiles code for registered objects as function
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile;

/**
 * Smarty Internal Plugin Compile Object Function Class
 *
 */
class Object_Method_Call_Compiler extends Base
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $optional_attributes = ['_any'];
    /**
     * Compiles code for the execution of function plugin
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     * @param array $parameter array with compilation parameter
     * @param string $tag name of function
     * @param string $function name of method to call
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     * @throws \Smarty\Exception
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        // check and get attributes
        $_attr = $this->get_attributes($compiler, $args);
        unset($_attr['nocache']);
        $_assign = null;
        if (isset($_attr['assign'])) {
            $_assign = $_attr['assign'];
            unset($_attr['assign']);
        }
        // method or property ?
        if (is_callable([$compiler->get_smarty()->registered_objects[$tag][0], $function])) {
            // convert attributes into parameter array string
            if ($compiler->get_smarty()->registered_objects[$tag][2]) {
                $_params_array = $this->format_params_array($_attr);
                $_params = 'array(' . implode(',', $_params_array) . ')';
                $output = "\$_smarty_tpl->getSmarty()->registered_objects['{$tag}'][0]->{$function}({$_params},\$_smarty_tpl)";
            } else {
                $_params = implode(',', $_attr);
                $output = "\$_smarty_tpl->getSmarty()->registered_objects['{$tag}'][0]->{$function}({$_params})";
            }
        } else {
            // object property
            $output = "\$_smarty_tpl->getSmarty()->registered_objects['{$tag}'][0]->{$function}";
        }
        if (!empty($parameter['modifierlist'])) {
            $output = $compiler->compile_modifier($parameter['modifierlist'], $output);
        }
        if (empty($_assign)) {
            return "<?php echo {$output};?>\n";
        }
        return "<?php \$_smarty_tpl->assign({$_assign},{$output});?>\n";
    }
}