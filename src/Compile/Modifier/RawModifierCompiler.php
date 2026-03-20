<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

/**
 * Smarty raw modifier plugin
 * Type:     modifier
 * Name:     raw
 * Purpose:  when escaping is enabled by default, generates a raw output of a variable
 *
 * @author Amaury Bouchard
 */
class Raw_Modifier_Compiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler)
    {
        $compiler->set_raw_output(true);
        return $params[0];
    }
}