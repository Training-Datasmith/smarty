<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Shared Inheritance
 * Shared methods for {extends} and {block} tags
 *
 * @author     Uwe Tews
 */
/**
 * Smarty Internal Plugin Compile Shared Inheritance Class
 *
 */
abstract class Inheritance extends Base
{
    /**
     * Compile inheritance initialization code as prefix
     *
     * @param bool|false                            $initChildSequence if true force child template
     */
    public static function post_compile(\Smarty\Compiler\Template $compiler, $init_child_sequence = false): void
    {
        $compiler->prefix_compiled_code .= '<?php $_smarty_tpl->getInheritance()->init($_smarty_tpl, ' . var_export($init_child_sequence, true) . ");\n?>\n";
    }
    /**
     * Register post compile callback to compile inheritance initialization code
     *
     * @param bool|false                            $initChildSequence if true force child template
     */
    public function register_init(\Smarty\Compiler\Template $compiler, $init_child_sequence = false): void
    {
        if ($init_child_sequence || !isset($compiler->_cache['inheritanceInit'])) {
            $compiler->register_post_compile_callback([self::class, 'postCompile'], [$init_child_sequence], 'inheritanceInit', $init_child_sequence);
            $compiler->_cache['inheritanceInit'] = true;
        }
    }
}