<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Else Class
 *
 */
class Else_Tag extends Base
{
    /**
     * Compiles code for the {else} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        [$nesting, $compiler->tag_nocache] = $this->close_tag($compiler, ['if', 'elseif']);
        $this->open_tag($compiler, 'else', [$nesting, $compiler->tag_nocache]);
        return '<?php } else { ?>';
    }
}