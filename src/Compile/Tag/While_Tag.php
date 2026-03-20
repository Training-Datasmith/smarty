<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile While Class
 *
 */
class While_Tag extends Base
{
    /**
     * Compiles code for the {while} tag
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
        $compiler->loop_nesting++;
        if ($compiler->tag_nocache) {
            // push a {nocache} tag onto the stack to prevent caching of this block
            $this->open_tag($compiler, 'nocache');
        }
        $this->open_tag($compiler, 'while', $compiler->tag_nocache);
        if (!array_key_exists('if condition', $parameter)) {
            $compiler->trigger_template_error('missing while condition', null, true);
        }
        if (is_array($parameter['if condition'])) {
            if ($compiler->is_nocache_active()) {
                // create nocache var to make it know for further compiling
                if (is_array($parameter['if condition']['var'])) {
                    $var = $parameter['if condition']['var']['var'];
                } else {
                    $var = $parameter['if condition']['var'];
                }
                $compiler->set_nocache_in_variable($var);
            }
            $prefix_var = $compiler->get_new_prefix_variable();
            $assign_compiler = new Assign();
            $assign_attr = [];
            $assign_attr[]['value'] = $prefix_var;
            if (is_array($parameter['if condition']['var'])) {
                $assign_attr[]['var'] = $parameter['if condition']['var']['var'];
                $_output = "<?php while ({$prefix_var} = {$parameter['if condition']['value']}) {?>";
                $_output .= $assign_compiler->compile($assign_attr, $compiler, ['smarty_internal_index' => $parameter['if condition']['var']['smarty_internal_index']]);
            } else {
                $assign_attr[]['var'] = $parameter['if condition']['var'];
                $_output = "<?php while ({$prefix_var} = {$parameter['if condition']['value']}) {?>";
                $_output .= $assign_compiler->compile($assign_attr, $compiler, []);
            }
            return $_output;
        }
        return "<?php\n while ({$parameter['if condition']}) {?>";
    }
}