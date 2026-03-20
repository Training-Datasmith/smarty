<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Parse_Tree\Template;
/**
 * Smarty Internal Plugin Compile BlockClose Class
 */
class Block_Close extends Inheritance
{
    /**
     * Compiles code for the {/block} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     * @param array $parameter array with compilation parameter
     *
     * @return bool true
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        [$_attr, $_nocache, $_buffer, $_has_nocache_code, $_class_name] = $this->close_tag($compiler, ['block']);
        $_block = [];
        if (isset($compiler->_cache['blockParams'])) {
            $_block = $compiler->_cache['blockParams'][$compiler->_cache['blockNesting']] ?? [];
            unset($compiler->_cache['blockParams'][$compiler->_cache['blockNesting']]);
        }
        $_name = $_attr['name'];
        $_assign = $_attr['assign'] ?? null;
        unset($_attr['assign'], $_attr['name']);
        foreach ($_attr as $name => $stat) {
            if (is_bool($stat) && $stat !== false || !is_bool($stat) && $stat !== 'false') {
                $_block[$name] = 'true';
            }
        }
        // get compiled block code
        $_function_code = $compiler->get_parser()->current_buffer;
        // setup buffer for template function code
        $compiler->get_parser()->current_buffer = new Template();
        $output = "<?php\n";
        $output .= $compiler->c_style_comment(" {block {$_name}} ") . "\n";
        $output .= "class {$_class_name} extends \\Smarty\\Runtime\\Block\n";
        $output .= "{\n";
        foreach ($_block as $property => $value) {
            $output .= "public \${$property} = " . var_export($value, true) . ";\n";
        }
        $output .= "public function callBlock(\\Smarty\\Template \$_smarty_tpl) {\n";
        $output .= (new \Smarty\Compiler\Code_Frame($compiler->get_template()))->insert_local_variables();
        if ($compiler->get_template()->get_compiled()->get_nocache_code()) {
            $output .= "\$_smarty_tpl->getCached()->hashes['{$compiler->get_template()->get_compiled()->nocache_hash}'] = true;\n";
        }
        if (isset($_assign)) {
            $output .= "ob_start();\n";
        }
        $output .= "?>\n";
        $compiler->get_parser()->current_buffer->append_subtree($compiler->get_parser(), new \Smarty\Parse_Tree\Tag($compiler->get_parser(), $output));
        $compiler->get_parser()->current_buffer->append_subtree($compiler->get_parser(), $_function_code);
        $output = "<?php\n";
        if (isset($_assign)) {
            $output .= "\$_smarty_tpl->assign({$_assign}, ob_get_clean());\n";
        }
        $output .= "}\n";
        $output .= "}\n";
        $output .= $compiler->c_style_comment(" {/block {$_name}} ") . "\n\n";
        $output .= "?>\n";
        $compiler->get_parser()->current_buffer->append_subtree($compiler->get_parser(), new \Smarty\Parse_Tree\Tag($compiler->get_parser(), $output));
        $compiler->block_or_function_code .= $compiler->get_parser()->current_buffer->to_smarty_php($compiler->get_parser());
        $compiler->get_parser()->current_buffer = new Template();
        // restore old status
        $compiler->get_template()->get_compiled()->set_nocache_code($_has_nocache_code);
        $compiler->tag_nocache = $_nocache;
        $compiler->get_parser()->current_buffer = $_buffer;
        $output = "<?php \n";
        if ($compiler->_cache['blockNesting'] === 1) {
            $output .= "\$_smarty_tpl->getInheritance()->instanceBlock(\$_smarty_tpl, '{$_class_name}', {$_name});\n";
        } else {
            $output .= "\$_smarty_tpl->getInheritance()->instanceBlock(\$_smarty_tpl, '{$_class_name}', {$_name}, \$this->tplIndex);\n";
        }
        $output .= "?>\n";
        --$compiler->_cache['blockNesting'];
        if ($compiler->_cache['blockNesting'] === 0) {
            unset($compiler->_cache['blockNesting']);
        }
        $compiler->suppress_nocache_processing = true;
        return $output;
    }
}