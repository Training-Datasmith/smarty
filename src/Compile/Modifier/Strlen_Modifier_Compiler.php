<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

/**
 * Smarty strlen modifier plugin
 * Type:     modifier
 * Name:     strlen
 * Purpose:  return the length of the given string
 *
 */
class Strlen_Modifier_Compiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler): string
    {
        return 'strlen((string) ' . $params[0] . ')';
    }
}