<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Break
 * Compiles the {break} tag
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Break Class
 *
 */
class Break_Tag extends Base
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $optional_attributes = ['levels'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $shorttag_order = ['levels'];
    /**
     * Tag name may be overloaded by ContinueTag
     *
     * @var string
     */
    protected $tag = 'break';
    /**
     * Compiles code for the {break} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        [$levels, $foreach_levels] = $this->check_levels($args, $compiler);
        $output = '<?php ';
        if ($foreach_levels > 0 && $this->tag === 'continue') {
            $foreach_levels--;
        }
        if ($foreach_levels > 0) {
            /* @var ForeachTag $foreachCompiler */
            $foreach_compiler = $compiler->get_tag_compiler('foreach');
            $output .= $foreach_compiler->compile_restore($foreach_levels);
        }
        return $output . "{$this->tag} {$levels};?>";
    }
    /**
     * check attributes and return array of break and foreach levels
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @throws \Smarty\CompilerException
     */
    public function check_levels($args, \Smarty\Compiler\Template $compiler): array
    {
        static $_is_loopy = ['for' => true, 'foreach' => true, 'while' => true, 'section' => true];
        // check and get attributes
        $_attr = $this->get_attributes($compiler, $args);
        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', null, true);
        }
        if (isset($_attr['levels'])) {
            if (!is_numeric($_attr['levels'])) {
                $compiler->trigger_template_error('level attribute must be a numeric constant', null, true);
            }
            $levels = $_attr['levels'];
        } else {
            $levels = 1;
        }
        $level_count = $levels;
        $tag_stack = $compiler->get_tag_stack();
        $stack_count = count($tag_stack) - 1;
        $foreach_levels = 0;
        $last_tag = '';
        while ($level_count > 0 && $stack_count >= 0) {
            if (isset($_is_loopy[$tag_stack[$stack_count][0]])) {
                $last_tag = $tag_stack[$stack_count][0];
                if ($level_count === 0) {
                    break;
                }
                $level_count--;
                if ($tag_stack[$stack_count][0] === 'foreach') {
                    $foreach_levels++;
                }
            }
            $stack_count--;
        }
        if ($level_count !== 0) {
            $compiler->trigger_template_error("cannot {$this->tag} {$levels} level(s)", null, true);
        }
        if ($last_tag === 'foreach' && $this->tag === 'break' && $foreach_levels > 0) {
            $foreach_levels--;
        }
        return [$levels, $foreach_levels];
    }
}