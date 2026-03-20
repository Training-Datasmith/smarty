<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

use Smarty\Compiler_Exception;
/**
 * Smarty is_array modifier plugin
 */
class Is_Array_Modifier_Compiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler): string
    {
        if (count($params) !== 1) {
            throw new Compiler_Exception('Invalid number of arguments for is_array. is_array expects exactly 1 parameter.');
        }
        return 'is_array(' . $params[0] . ')';
    }
}