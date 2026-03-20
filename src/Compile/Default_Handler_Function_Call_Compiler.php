<?php

declare (strict_types=1);
namespace Smarty\Compile;

use Smarty\Compiler\Template;
class Default_Handler_Function_Call_Compiler extends Base
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    public $optional_attributes = ['_any'];
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
        // check and get attributes
        $_attr = $this->get_attributes($compiler, $args);
        unset($_attr['nocache']);
        $_params_array = $this->format_params_array($_attr);
        $_params = 'array(' . implode(',', $_params_array) . ')';
        $output = "\$_smarty_tpl->getSmarty()->getRuntime('DefaultPluginHandler')->getCallback(" . var_export($function, true) . ",'function')({$_params}, \$_smarty_tpl)";
        if (!empty($parameter['modifierlist'])) {
            $output = $compiler->compile_modifier($parameter['modifierlist'], $output);
        }
        return "<?php echo {$output};?>\n";
    }
}