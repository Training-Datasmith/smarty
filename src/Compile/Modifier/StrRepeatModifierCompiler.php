<?php

declare(strict_types=1);

namespace Smarty\Compile\Modifier;

/**
 * Smarty str_repeat modifier plugin
 * Type:     modifier
 * Name:     str_repeat
 * Purpose:  returns string repeated times times
 *
 */

class StrRepeatModifierCompiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler): string
    {
        return 'str_repeat((string) ' . $params[0] . ', (int) ' . $params[1] . ')';
    }

}
