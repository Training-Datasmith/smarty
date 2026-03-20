<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

/**
 * Smarty count_paragraphs modifier plugin
 * Type:     modifier
 * Name:     count_paragraphs
 * Purpose:  count the number of paragraphs in a text
 *
 * @author Uwe Tews
 */
class Count_Paragraphs_Modifier_Compiler extends Base
{
    public function compile($params, \Smarty\Compiler\Template $compiler): string
    {
        // count \r or \n characters
        return '(preg_match_all(\'#[\r\n]+#\', ' . $params[0] . ', $tmp)+1)';
    }
}