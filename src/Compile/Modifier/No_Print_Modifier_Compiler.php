<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

/**
 * Smarty noprint modifier plugin
 * Type:     modifier
 * Name:     noprint
 * Purpose:  return an empty string
 *
 * @author Uwe Tews
 */
class No_Print_Modifier_Compiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler): string
    {
        return "''";
    }
}