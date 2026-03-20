<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile If Class
 *
 */
class If_Tag extends Base
{
    /**
     * Compiles code for the {if} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     * @param array $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        if ($compiler->tag_nocache) {
            // push a {nocache} tag onto the stack to prevent caching of this block
            $this->open_tag($compiler, 'nocache');
        }
        $this->open_tag($compiler, 'if', [1, $compiler->tag_nocache]);
        if (!isset($parameter['if condition'])) {
            $compiler->trigger_template_error('missing if condition', null, true);
        }
        if (is_array($parameter['if condition'])) {
            if (is_array($parameter['if condition']['var'])) {
                $var = $parameter['if condition']['var']['var'];
            } else {
                $var = $parameter['if condition']['var'];
            }
            if ($compiler->is_nocache_active()) {
                // create nocache var to make it know for further compiling
                $compiler->set_nocache_in_variable($var);
            }
            $prefix_var = $compiler->get_new_prefix_variable();
            $_output = "<?php {$prefix_var} = {$parameter['if condition']['value']};?>\n";
            $assign_attr = [];
            $assign_attr[]['value'] = $prefix_var;
            $assign_compiler = new Assign();
            if (is_array($parameter['if condition']['var'])) {
                $assign_attr[]['var'] = $parameter['if condition']['var']['var'];
                $_output .= $assign_compiler->compile($assign_attr, $compiler, ['smarty_internal_index' => $parameter['if condition']['var']['smarty_internal_index']]);
            } else {
                $assign_attr[]['var'] = $parameter['if condition']['var'];
                $_output .= $assign_compiler->compile($assign_attr, $compiler, []);
            }
            return $_output . "<?php if ({$prefix_var}) {?>";
        }
        return "<?php if ({$parameter['if condition']}) {?>";
    }
}