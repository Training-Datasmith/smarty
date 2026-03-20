<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Special Smarty Variable
 * Compiles the special $smarty variables
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile;

use Smarty\Compile\Tag\Capture;
use Smarty\Compile\Tag\Foreach_Tag;
use Smarty\Compile\Tag\Section;
use Smarty\Compiler\Template;
use Smarty\Compiler_Exception;
/**
 * Smarty Internal Plugin Compile special Smarty Variable Class
 *
 */
class Special_Variable_Compiler extends Base
{
    /**
     * Compiles code for the special $smarty variables
     *
     * @param array $args array with attributes from parser
     * @param Template $compiler compiler object
     * @param array $parameter
     *
     * @return string compiled code
     * @throws CompilerException
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        $_index = preg_split("/\\]\\[/", substr($parameter, 1, strlen($parameter) - 2));
        $variable = smarty_strtolower_ascii($compiler->get_id($_index[0]));
        if ($variable === false) {
            $compiler->trigger_template_error('special $Smarty variable name index can not be variable', null, true);
        }
        $compiled_ref = 'null';
        if (!isset($compiler->get_smarty()->security_policy) || $compiler->get_smarty()->security_policy->is_trusted_special_smarty_var($variable, $compiler)) {
            switch ($variable) {
                case 'foreach':
                    return (new Foreach_Tag())->compile_special_variable($compiler, $_index);
                case 'section':
                    return (new Section())->compile_special_variable($compiler, $_index);
                case 'capture':
                    return (new Capture())->compile_special_variable($compiler, $_index);
                case 'now':
                    return 'time()';
                case 'cookies':
                    if (isset($compiler->get_smarty()->security_policy) && !$compiler->get_smarty()->security_policy->allow_super_globals) {
                        $compiler->trigger_template_error('(secure mode) super globals not permitted');
                        break;
                    }
                    $compiled_ref = '$_COOKIE';
                    break;
                case 'get':
                case 'post':
                case 'env':
                case 'server':
                case 'session':
                case 'request':
                    if (isset($compiler->get_smarty()->security_policy) && !$compiler->get_smarty()->security_policy->allow_super_globals) {
                        $compiler->trigger_template_error('(secure mode) super globals not permitted');
                        break;
                    }
                    $compiled_ref = '$_' . smarty_strtoupper_ascii($variable);
                    break;
                case 'template':
                    return '$_smarty_tpl->template_resource';
                case 'template_object':
                    if (isset($compiler->get_smarty()->security_policy)) {
                        $compiler->trigger_template_error('(secure mode) template_object not permitted');
                        break;
                    }
                    return '$_smarty_tpl';
                case 'current_dir':
                    return '$_smarty_current_dir';
                case 'version':
                    return '\Smarty\Smarty::SMARTY_VERSION';
                case 'const':
                    if (isset($compiler->get_smarty()->security_policy) && !$compiler->get_smarty()->security_policy->allow_constants) {
                        $compiler->trigger_template_error('(secure mode) constants not permitted');
                        break;
                    }
                    if (strpos($_index[1], '$') === false && strpos($_index[1], '\'') === false) {
                        return "(defined('{$_index[1]}') ? constant('{$_index[1]}') : null)";
                    }
                    return "(defined({$_index[1]}) ? constant({$_index[1]}) : null)";
                case 'config':
                    if (isset($_index[2])) {
                        return "(is_array(\$tmp = \$_smarty_tpl->getConfigVariable({$_index[1]})) ? \$tmp[{$_index[2]}] : null)";
                    }
                    return "\$_smarty_tpl->getConfigVariable({$_index[1]})";
                case 'ldelim':
                    return '$_smarty_tpl->getLeftDelimiter()';
                case 'rdelim':
                    return '$_smarty_tpl->getRightDelimiter()';
                default:
                    $compiler->trigger_template_error('$smarty.' . trim($_index[0], "'") . ' is not defined');
                    break;
            }
            if (isset($_index[1])) {
                array_shift($_index);
                foreach ($_index as $_ind) {
                    $compiled_ref = $compiled_ref . "[{$_ind}]";
                }
            }
            return $compiled_ref;
        }
        return '';
    }
}