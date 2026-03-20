<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Block Plugin
 * Compiles code for the execution of block plugin
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile;

use Smarty\Compiler\Template;
use Smarty\Compiler_Exception;
use Smarty\Exception;
use Smarty\Smarty;
/**
 * Smarty Internal Plugin Compile Block Plugin Class
 *
 */
class Block_Compiler extends Base
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $optional_attributes = ['_any'];
    /**
     * nesting level
     *
     * @var int
     */
    private $nesting = 0;
    /**
     * Compiles code for the execution of block plugin
     *
     * @param array $args array with attributes from parser
     * @param Template $compiler compiler object
     * @param array $parameter array with compilation parameter
     * @param string $tag name of block plugin
     * @param string $function PHP function name
     *
     * @return string compiled code
     * @throws CompilerException
     * @throws Exception
     */
    public function compile($args, Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        if (!isset($tag[5]) || substr($tag, -5) !== 'close') {
            return $this->compile_opening_tag($compiler, $args, $tag, $function);
        }
        return $this->compile_closing_tag($compiler, $tag, $parameter, $function);
    }
    /**
     * Compiles code for the {$smarty.block.child} property
     *
     * @param Template $compiler compiler object
     *
     * @return string compiled code
     * @throws CompilerException
     */
    public function compile_child(\Smarty\Compiler\Template $compiler): string
    {
        if (!isset($compiler->_cache['blockNesting'])) {
            $compiler->trigger_template_error("'{\$smarty.block.child}' used outside {block} tags ", $compiler->get_parser()->lex->taglineno);
        }
        $compiler->_cache['blockParams'][$compiler->_cache['blockNesting']]['callsChild'] = true;
        $compiler->suppress_nocache_processing = true;
        $output = "<?php \n";
        $output .= '$_smarty_tpl->getInheritance()->callChild($_smarty_tpl, $this' . ");\n";
        return $output . "?>\n";
    }
    /**
     * Compiles code for the {$smarty.block.parent} property
     *
     * @param Template $compiler compiler object
     *
     * @return string compiled code
     * @throws CompilerException
     */
    public function compile_parent(\Smarty\Compiler\Template $compiler): string
    {
        if (!isset($compiler->_cache['blockNesting'])) {
            $compiler->trigger_template_error("'{\$smarty.block.parent}' used outside {block} tags ", $compiler->get_parser()->lex->taglineno);
        }
        $compiler->suppress_nocache_processing = true;
        $output = "<?php \n";
        $output .= '$_smarty_tpl->getInheritance()->callParent($_smarty_tpl, $this' . ");\n";
        return $output . "?>\n";
    }
    /**
     * Returns true if this block is cacheable.
     *
     * @param $function
     *
     */
    protected function block_is_cacheable(\Smarty\Smarty $smarty, string $function): bool
    {
        return $smarty->get_block_handler($function)->is_cacheable();
    }
    /**
     * Returns the code used for the isset check
     *
     * @param string $tag tag name
     * @param string $function base tag or method name
     */
    protected function get_is_callable_code($tag, $function): string
    {
        return '$_smarty_tpl->getSmarty()->getBlockHandler(' . var_export($function, true) . ')';
    }
    /**
     * Returns the full code used to call the callback
     *
     * @param string $tag tag name
     * @param string $function base tag or method name
     */
    protected function get_full_callback_code($tag, $function): string
    {
        return '$_smarty_tpl->getSmarty()->getBlockHandler(' . var_export($function, true) . ')->handle';
    }
    private function compile_opening_tag(Template $compiler, array $args, ?string $tag, ?string $function): string
    {
        // check and get attributes
        $_attr = $this->get_attributes($compiler, $args);
        $this->nesting++;
        unset($_attr['nocache']);
        $_params = 'array(' . implode(',', $this->format_params_array($_attr)) . ')';
        if (!$this->block_is_cacheable($compiler->get_smarty(), $function)) {
            $compiler->tag_nocache = true;
        }
        if ($compiler->tag_nocache) {
            // push a {nocache} tag onto the stack to prevent caching of this block
            $this->open_tag($compiler, 'nocache');
        }
        $this->open_tag($compiler, $tag, [$_params, $compiler->tag_nocache]);
        // compile code
        $output = '<?php $_block_repeat=true;
if (!' . $this->get_is_callable_code($tag, $function) . ") {\nthrow new \\Smarty\\Exception('block tag \\'{$tag}\\' not callable or registered');\n}\n\necho " . $this->get_full_callback_code($tag, $function) . "({$_params}, null, \$_smarty_tpl, \$_block_repeat);\nwhile (\$_block_repeat) {\n  ob_start();\n?>";
        return $output;
    }
    /**
     *
     * @throws CompilerException
     * @throws Exception
     */
    private function compile_closing_tag(Template $compiler, string $tag, array $parameter, ?string $function): string
    {
        // closing tag of block plugin, restore nocache
        $base_tag = substr($tag, 0, -5);
        [$_params, $nocache_pushed] = $this->close_tag($compiler, $base_tag);
        // compile code
        if (!isset($parameter['modifier_list'])) {
            $mod_pre = $mod_post = $mod_content = '';
            $mod_content2 = 'ob_get_clean()';
        } else {
            $mod_content2 = "\$_block_content{$this->nesting}";
            $mod_content = "\$_block_content{$this->nesting} = ob_get_clean();\n";
            $mod_pre = "ob_start();\n";
            $mod_post = 'echo ' . $compiler->compile_modifier($parameter['modifier_list'], 'ob_get_clean()') . ";\n";
        }
        $output = "<?php {$mod_content}\$_block_repeat=false;\n{$mod_pre}";
        $callback = $this->get_full_callback_code($base_tag, $function);
        $output .= "echo {$callback}({$_params}, {$mod_content2}, \$_smarty_tpl, \$_block_repeat);\n";
        $output .= "{$mod_post}}\n?>";
        if ($nocache_pushed) {
            // pop the pushed virtual nocache tag
            $this->close_tag($compiler, 'nocache');
            $compiler->tag_nocache = true;
        }
        return $output;
    }
}