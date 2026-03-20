<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile While
 * Compiles the {while} tag
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Whileclose Class
 *
 */
class While_Close extends Base
{
    /**
     * Compiles code for the {/while} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        $compiler->loop_nesting--;
        $nocache_pushed = $this->close_tag($compiler, ['while']);
        if ($nocache_pushed) {
            // pop the pushed virtual nocache tag
            $this->close_tag($compiler, 'nocache');
            $compiler->tag_nocache = true;
        }
        return "<?php }?>\n";
    }
}