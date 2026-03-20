<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Ldelim
 * Compiles the {ldelim} tag
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Ldelim Class
 *
 */
class Ldelim extends Base
{
    /**
     * Compiles code for the {ldelim} tag
     * This tag does output the left delimiter
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        $_attr = $this->get_attributes($compiler, $args);
        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', null, true);
        }
        return $compiler->get_template()->get_left_delimiter();
    }
}