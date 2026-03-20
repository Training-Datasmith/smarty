<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Foreach
 * Compiles the {foreach} {foreachelse} {/foreach} tags
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Foreachclose Class
 *
 */
class Foreach_Close extends Base
{
    /**
     * Compiles code for the {/foreach} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        $compiler->loop_nesting--;
        [$open_tag, $nocache_pushed, $local_variable_prefix, $item, $restore] = $this->close_tag($compiler, ['foreach', 'foreachelse']);
        if ($nocache_pushed) {
            // pop the pushed virtual nocache tag
            $this->close_tag($compiler, 'nocache');
            $compiler->tag_nocache = true;
        }
        $output = "<?php\n";
        if ($restore) {
            $output .= "\$_smarty_tpl->setVariable('{$item}', {$local_variable_prefix}Backup);\n";
        }
        $output .= "}\n";
        /* @var \Smarty\Compile\Tag\ForeachTag $foreachCompiler */
        $foreach_compiler = $compiler->get_tag_compiler('foreach');
        $output .= $foreach_compiler->compile_restore(1);
        return $output . '?>';
    }
}