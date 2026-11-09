<?php

declare(strict_types=1);
/**
 * Smarty plugin for testing scopes
 *


 */

use Smarty\Template;

/**
 * Smarty {checkvar}
 *
 * @param array $params parameter array
 * @param Template $template template object
 *
 * @return string
 */
function smarty_function_checkvar($params, \Smarty\Template $template)
{
    $output = '';
    $types = ['template', 'data', 'global'];
    if (isset($params['types'])) {
        $types = (array)$params['types'];
    }
    $var = $params['var'];
    $ptr = $template;
    while ($ptr) {
        if (in_array('template', $types) && $ptr instanceof Template) {
            $output .= "#{$ptr->get_source()->name}:\${$var} =";
            $output .= $ptr->has_variable($var) ? preg_replace('/\s/', '', var_export($ptr->get_value($var), true)) : '>unassigned<';
            $ptr = $ptr->parent;
        } elseif (in_array('data', $types) && !($ptr instanceof Template || $ptr instanceof \Smarty\Smarty)) {
            $output .= "#data:\${$var} =";
            $output .= $ptr->has_variable($var) ? preg_replace('/\s/', '', var_export($ptr->get_value($var), true)) : '>unassigned<';
            $ptr = $ptr->parent;
        } else {
            $ptr = null;
        }
    }
    if (in_array('global', $types)) {
        $output .= "#global:\${$var} =";
        $output .= $template->get_smarty()->has_variable($var) ?
            preg_replace('/\s/', '', var_export($template->get_smarty()->get_value($var), true)) : '>unassigned<';
    }
    return $output;
}
