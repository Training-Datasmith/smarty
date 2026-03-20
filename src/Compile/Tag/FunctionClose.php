<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Function
 * Compiles the {function} {/function} tags
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Functionclose Class
 *
 */
class Function_Close extends Base
{
    /**
     * Compiler object
     *
     * @var object
     */
    private $compiler;
    /**
     * Compiles code for the {/function} tag
     *
     * @param array $args array with attributes from parser
     * @param object|\Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        $this->compiler = $compiler;
        $saved_data = $this->close_tag($compiler, ['function']);
        $_attr = $saved_data[0];
        $_name = trim($_attr['name'], '\'"');
        $parent_compiler = $compiler->get_parent_compiler();
        $parent_compiler->tpl_function[$_name]['compiled_filepath'] = $parent_compiler->get_template()->get_compiled()->filepath;
        $parent_compiler->tpl_function[$_name]['uid'] = $compiler->get_template()->get_source()->uid;
        $_parameter = $_attr;
        unset($_parameter['name']);
        // default parameter
        $_params_array = $this->format_params_array($_attr);
        $_params_code = (new \Smarty\Compiler\Code_Frame($compiler->get_template()))->insert_local_variables();
        if (!empty($_params_array)) {
            $_params = 'array(' . implode(',', $_params_array) . ')';
            $_params_code .= "\$params = array_merge({$_params}, \$params);\n";
        }
        $_function_code = $compiler->get_parser()->current_buffer;
        // setup buffer for template function code
        $compiler->get_parser()->current_buffer = new \Smarty\Parse_Tree\Template();
        $_func_name = "smarty_template_function_{$_name}_{$compiler->get_template()->get_compiled()->nocache_hash}";
        $_func_name_caching = $_func_name . '_nocache';
        if ($compiler->get_template()->get_compiled()->get_nocache_code()) {
            $parent_compiler->tpl_function[$_name]['call_name_caching'] = $_func_name_caching;
            $output = "<?php\n";
            $output .= $compiler->c_style_comment(" {$_func_name_caching} ") . "\n";
            $output .= "if (!function_exists('{$_func_name_caching}')) {\n";
            $output .= "function {$_func_name_caching} (\\Smarty\\Template \$_smarty_tpl,\$params) {\n";
            $output .= "ob_start();\n";
            $output .= "\$_smarty_tpl->getCompiled()->setNocacheCode(true);\n";
            $output .= $_params_code;
            $output .= "foreach (\$params as \$key => \$value) {\n\$_smarty_tpl->assign(\$key, \$value);\n}\n";
            $output .= "\$params = var_export(\$params, true);\n";
            $output .= "echo \"/*%%SmartyNocache:{$compiler->get_template()->get_compiled()->nocache_hash}%%*/<?php ";
            $output .= "\\\$_smarty_tpl->pushStack();\nforeach (\$params as \\\$key => \\\$value) {\n\\\$_smarty_tpl->assign(\\\$key, \\\$value);\n}\n?>";
            $output .= "/*/%%SmartyNocache:{$compiler->get_template()->get_compiled()->nocache_hash}%%*/\";?>";
            $compiler->get_parser()->current_buffer->append_subtree($compiler->get_parser(), new \Smarty\Parse_Tree\Tag($compiler->get_parser(), $output));
            $compiler->get_parser()->current_buffer->append_subtree($compiler->get_parser(), $_function_code);
            $output = "<?php echo \"/*%%SmartyNocache:{$compiler->get_template()->get_compiled()->nocache_hash}%%*/<?php ";
            $output .= "\\\$_smarty_tpl->popStack();?>\n";
            $output .= "/*/%%SmartyNocache:{$compiler->get_template()->get_compiled()->nocache_hash}%%*/\";\n?>";
            $output .= "<?php echo str_replace('{$compiler->get_template()->get_compiled()->nocache_hash}', \$_smarty_tpl->getCompiled()->nocache_hash ?? '', ob_get_clean());\n";
            $output .= "}\n}\n";
            $output .= $compiler->c_style_comment("/ {$_func_name}_nocache ") . "\n\n";
            $output .= "?>\n";
            $compiler->get_parser()->current_buffer->append_subtree($compiler->get_parser(), new \Smarty\Parse_Tree\Tag($compiler->get_parser(), $output));
            $_function_code = new \Smarty\Parse_Tree\Tag($compiler->get_parser(), preg_replace_callback("/((<\\?php )?echo '\\/\\*%%SmartyNocache:{$compiler->get_template()->get_compiled()->nocache_hash}%%\\*\\/([\\S\\s]*?)\\/\\*\\/%%SmartyNocache:{$compiler->get_template()->get_compiled()->nocache_hash}%%\\*\\/';(\\?>\n)?)/", [$this, 'removeNocache'], $_function_code->to_smarty_php($compiler->get_parser())));
        }
        $parent_compiler->tpl_function[$_name]['call_name'] = $_func_name;
        $output = "<?php\n";
        $output .= $compiler->c_style_comment(" {$_func_name} ") . "\n";
        $output .= "if (!function_exists('{$_func_name}')) {\n";
        $output .= "function {$_func_name}(\\Smarty\\Template \$_smarty_tpl,\$params) {\n";
        $output .= $_params_code;
        $output .= "foreach (\$params as \$key => \$value) {\n\$_smarty_tpl->assign(\$key, \$value);\n}\n";
        $output .= "?>\n";
        $compiler->get_parser()->current_buffer->append_subtree($compiler->get_parser(), new \Smarty\Parse_Tree\Tag($compiler->get_parser(), $output));
        $compiler->get_parser()->current_buffer->append_subtree($compiler->get_parser(), $_function_code);
        $output = "<?php\n}}\n";
        $output .= $compiler->c_style_comment("/ {$_func_name} ") . "\n\n";
        $output .= "?>\n";
        $compiler->get_parser()->current_buffer->append_subtree($compiler->get_parser(), new \Smarty\Parse_Tree\Tag($compiler->get_parser(), $output));
        $parent_compiler->block_or_function_code .= $compiler->get_parser()->current_buffer->to_smarty_php($compiler->get_parser());
        // restore old buffer
        $compiler->get_parser()->current_buffer = $saved_data[1];
        // restore old status
        $compiler->get_template()->get_compiled()->set_nocache_code($saved_data[2]);
        $compiler->get_template()->caching = $saved_data[3];
        return '';
    }
    /**
     * Remove nocache code
     *
     * @param $match
     *
     * @return string
     */
    public function remove_nocache($match)
    {
        $hash = $this->compiler->get_template()->get_compiled()->nocache_hash;
        $code = preg_replace("/((<\\?php )?echo '\\/\\*%%SmartyNocache:{$hash}%%\\*\\/)|(\\/\\*\\/%%SmartyNocache:{$hash}%%\\*\\/';(\\?>\n)?)/", '', $match[0]);
        return str_replace(['\\\'', '\\\\\''], ['\'', '\\\''], $code);
    }
}