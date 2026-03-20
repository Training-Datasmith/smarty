<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Setfilter Class
 *
 */
class Setfilter extends Base
{
    /**
     * Compiles code for setfilter tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     * @param array $parameter array with compilation parameter
     *
     * @return string compiled code
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        $compiler->variable_filter_stack[] = $compiler->get_smarty()->get_default_modifiers();
        // The modifier_list is passed as an array of array's. The inner arrays have the modifier at index 0,
        // and, possibly, parameters at subsequent indexes, e.g. [ ['escape','"mail"'] ]
        // We will collapse them so the syntax is OK for ::setDefaultModifiers() as follows: [ 'escape:"mail"' ]
        $new_list = [];
        foreach ($parameter['modifier_list'] as $modifier) {
            $new_list[] = implode(':', $modifier);
        }
        $compiler->get_smarty()->set_default_modifiers($new_list);
        return '';
    }
}