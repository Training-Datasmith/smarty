<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

use Smarty\Compiler_Exception;
/**
 * Smarty empty modifier plugin
 */
class Empty_Modifier_Compiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler): string
    {
        if (count($params) !== 1) {
            throw new Compiler_Exception('Invalid number of arguments for empty. empty expects exactly 1 parameter.');
        }
        return 'empty(' . $params[0] . ')';
    }
}