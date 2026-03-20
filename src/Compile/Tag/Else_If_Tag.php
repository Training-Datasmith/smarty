<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile ElseIf Class
 *
 */
class Else_If_Tag extends Base
{
    /**
     * Compiles code for the {elseif} tag
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
        [$nesting, $nocache_pushed] = $this->close_tag($compiler, ['if', 'elseif']);
        if (!isset($parameter['if condition'])) {
            $compiler->trigger_template_error('missing elseif condition', null, true);
        }
        $assign_code = '';
        $var = '';
        if (is_array($parameter['if condition'])) {
            $condition_by_assign = true;
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
            $assign_code = "<?php {$prefix_var} = {$parameter['if condition']['value']};?>\n";
            $assign_compiler = new Assign();
            $assign_attr = [];
            $assign_attr[]['value'] = $prefix_var;
            if (is_array($parameter['if condition']['var'])) {
                $assign_attr[]['var'] = $parameter['if condition']['var']['var'];
                $assign_code .= $assign_compiler->compile($assign_attr, $compiler, ['smarty_internal_index' => $parameter['if condition']['var']['smarty_internal_index']]);
            } else {
                $assign_attr[]['var'] = $parameter['if condition']['var'];
                $assign_code .= $assign_compiler->compile($assign_attr, $compiler, []);
            }
        } else {
            $condition_by_assign = false;
        }
        $prefix_code = $compiler->get_prefix_code();
        if (empty($prefix_code)) {
            if ($condition_by_assign) {
                $this->open_tag($compiler, 'elseif', [$nesting + 1, $compiler->tag_nocache]);
                $_output = $compiler->append_code("<?php } else {\n?>", $assign_code);
                return $compiler->append_code($_output, "<?php if ({$prefix_var}) {?>");
            }
            $this->open_tag($compiler, 'elseif', [$nesting, $nocache_pushed]);
            return "<?php } elseif ({$parameter['if condition']}) {?>";
        }
        $_output = $compiler->append_code("<?php } else {\n?>", $prefix_code);
        $this->open_tag($compiler, 'elseif', [$nesting + 1, $nocache_pushed]);
        if ($condition_by_assign) {
            $_output = $compiler->append_code($_output, $assign_code);
            return $compiler->append_code($_output, "<?php if ({$prefix_var}) {?>");
        }
        return $compiler->append_code($_output, "<?php if ({$parameter['if condition']}) {?>");
    }
}