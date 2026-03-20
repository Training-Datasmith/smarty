<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

use Smarty\Compiler_Exception;
/**
 * Smarty isset modifier plugin
 */
class Isset_Modifier_Compiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler): string
    {
        $params = array_filter($params, function ($v): bool {
            return !empty($v);
        });
        if (count($params) < 1) {
            throw new Compiler_Exception('Invalid number of arguments for isset. isset expects at least one parameter.');
        }
        $tests = [];
        foreach ($params as $param) {
            $tests[] = 'null !== (' . $param . ' ?? null)';
        }
        return '(' . implode(' && ', $tests) . ')';
    }
}