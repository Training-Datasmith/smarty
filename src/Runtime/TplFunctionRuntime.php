<?php

declare (strict_types=1);
namespace Smarty\Runtime;

use Smarty\Exception;
use Smarty\Template;
use Smarty\Template_Base;
/**
 * TplFunction Runtime Methods callTemplateFunction
 *
 * @author     Uwe Tews
 **/
class Tpl_Function_Runtime
{
    /**
     * Call template function
     *
     * @param \Smarty\Template $tpl template object
     * @param string $name template function name
     * @param array $params parameter array
     * @param bool $nocache true if called nocache
     *
     * @throws \Smarty\Exception
     */
    public function call_template_function(Template $tpl, $name, $params, $nocache): void
    {
        $func_param = $tpl->tpl_functions[$name] ?? $tpl->get_smarty()->tpl_functions[$name] ?? null;
        if (!isset($func_param)) {
            throw new \Smarty\Exception("Unable to find template function '{$name}'");
        }
        if (!$tpl->caching || $tpl->caching && $nocache) {
            $function = $func_param['call_name'];
        } else if (isset($func_param['call_name_caching'])) {
            $function = $func_param['call_name_caching'];
        } else {
            $function = $func_param['call_name'];
        }
        if (!function_exists($function) && !$this->add_tpl_func_to_cache($tpl, $name, $function)) {
            throw new \Smarty\Exception("Unable to find template function '{$name}'");
        }
        $tpl->push_stack();
        $function($tpl, $params);
        $tpl->pop_stack();
    }
    /**
     * Register template functions defined by template
     *
     * @param \Smarty|\Smarty\Template|\Smarty\TemplateBase $obj
     * @param array $tplFunctions source information array of
     *                                                                                      template functions defined
     *                                                                                      in template
     * @param bool $override if true replace existing
     *                                                                                      functions with same name
     */
    public function register_tpl_functions(Template_Base $obj, $tpl_functions, $override = true): void
    {
        $obj->tpl_functions = $override ? array_merge($obj->tpl_functions, $tpl_functions) : array_merge($tpl_functions, $obj->tpl_functions);
        // make sure that the template functions are known in parent templates
        if ($obj->_is_sub_tpl()) {
            $this->register_tpl_functions($obj->parent, $tpl_functions, false);
        } else {
            $obj->get_smarty()->tpl_functions = $override ? array_merge($obj->get_smarty()->tpl_functions, $tpl_functions) : array_merge($tpl_functions, $obj->get_smarty()->tpl_functions);
        }
    }
    /**
     * Return source parameter array for single or all template functions
     *
     * @param \Smarty\Template $tpl template object
     * @param null|string $name template function name
     *
     * @return array|bool|mixed
     */
    public function get_tpl_function(Template $tpl, $name = null)
    {
        if (isset($name)) {
            return $tpl->tpl_functions[$name] ?? $tpl->get_smarty()->tpl_functions[$name] ?? false;
        }
        return empty($tpl->tpl_functions) ? $tpl->get_smarty()->tpl_functions : $tpl->tpl_functions;
    }
    /**
     * Add template function to cache file for nocache calls
     *
     * @param string $_name template function name
     * @param string $_function PHP function name
     * @throws Exception
     */
    private function add_tpl_func_to_cache(Template $tpl, $_name, $_function): bool
    {
        $func_param = $tpl->tpl_functions[$_name];
        if (is_file($func_param['compiled_filepath'])) {
            // read compiled file
            $code = file_get_contents($func_param['compiled_filepath']);
            // grab template function
            if (preg_match("/\\/\\* {$_function} \\*\\/([\\S\\s]*?)\\/\\*\\/ {$_function} \\*\\//", $code, $match)) {
                // grab source info from file dependency
                preg_match("/\\s*'{$func_param['uid']}'([\\S\\s]*?)\\),/", $code, $match1);
                unset($code);
                // make PHP function known
                eval($match[0]);
                if (function_exists($_function)) {
                    // Some magic code existed here, testing if the cached property had been set
                    // and then bubbling up until it found a parent template that had the cached property.
                    // This is no longer possible, so somehow this might break.
                    // add template function code to cache file
                    $content = $tpl->get_cached()->read_cache($tpl);
                    if ($content) {
                        // check if we must update file dependency
                        if (!preg_match("/'{$func_param['uid']}'(.*?)'nocache_hash'/", $content, $match2)) {
                            $content = preg_replace("/('file_dependency'(.*?)\\()/", "\\1{$match1[0]}", $content);
                        }
                        $tpl->get_cached()->write_cache($tpl, preg_replace('/\s*\?>\s*$/', "\n", $content) . "\n" . preg_replace(['/^\s*<\?php\s+/', '/\s*\?>\s*$/'], "\n", $match[0]));
                    }
                    return true;
                }
            }
        }
        return false;
    }
}