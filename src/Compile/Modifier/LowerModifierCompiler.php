<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

/**
 * Smarty lower modifier plugin
 * Type:     modifier
 * Name:     lower
 * Purpose:  convert string to lowercase
 *
 * @author Monte Ohrt <monte at ohrt dot com>
 * @author Uwe Tews
 */
class Lower_Modifier_Compiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler): string
    {
        return 'mb_strtolower((string) ' . $params[0] . ', \'' . addslashes(\Smarty\Smarty::$_CHARSET) . '\')';
    }
}