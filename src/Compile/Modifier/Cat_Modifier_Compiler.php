<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

/**
 * Smarty cat modifier plugin
 * Type:     modifier
 * Name:     cat
 * Date:     Feb 24, 2003
 * Purpose:  catenate a value to a variable
 * Input:    string to catenate
 * Example:  {$var|cat:"foo"}
 *
 * @author Uwe Tews
 */
class Cat_Modifier_Compiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler): string
    {
        return '(' . implode(').(', $params) . ')';
    }
}