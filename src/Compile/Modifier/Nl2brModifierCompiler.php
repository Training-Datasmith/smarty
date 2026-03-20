<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

/**
 * Smarty nl2br modifier plugin
 * Type:     modifier
 * Name:     nl2br
 * Purpose:  insert HTML line breaks before all newlines in a string
 *
 */
class Nl2br_Modifier_Compiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler): string
    {
        return 'nl2br((string) ' . $params[0] . ', (bool) ' . ($params[1] ?? true) . ')';
    }
}