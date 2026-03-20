<?php

declare (strict_types=1);
namespace Smarty;

/**
 * Smarty Internal Plugin Data
 * This file contains the basic properties and methods for holding config and template variables
 */
class Data
{
    /**
     * define variable scopes
     */
    public const SCOPE_LOCAL = 1;
    public const SCOPE_PARENT = 2;
    public const SCOPE_TPL_ROOT = 4;
    public const SCOPE_ROOT = 8;
    public const SCOPE_SMARTY = 16;
    public const SCOPE_GLOBAL = 32;
    /**
     * Global smarty instance
     *
     * @var Smarty
     */
    protected $smarty;
    /**
     * template variables
     *
     * @var Variable[]
     */
    public $tpl_vars = [];
    /**
     * parent data container (if any)
     *
     * @var Data
     */
    public $parent;
    /**
     * configuration settings
     *
     * @var string[]
     */
    public $config_vars = [];
    /**
     * This variable will hold a stack of template variables.
     *
     * @var null|array
     */
    private $_var_stack = [];
    /**
     * This variable will hold a stack of config variables.
     *
     * @var null|array
     */
    private $_config_stack = [];
    /**
     * Default scope for new variables
     * @var int
     */
    protected $default_scope = self::SCOPE_LOCAL;
    /**
     * create Smarty data object
     *
     * @param Smarty|array $_parent parent template
     * @param Smarty|Template $smarty global smarty instance
     *
     * @throws Exception
     */
    public function __construct($_parent = null, $smarty = null)
    {
        $this->smarty = $smarty;
        if (is_object($_parent)) {
            // when object set up back pointer
            $this->parent = $_parent;
        } elseif (is_array($_parent)) {
            // set up variable values
            foreach ($_parent as $_key => $_val) {
                $this->assign($_key, $_val);
            }
        } elseif ($_parent !== null) {
            throw new Exception('Wrong type for template variables');
        }
    }
    /**
     * assigns a Smarty variable
     *
     * @param array|string $tpl_var the template variable name(s)
     * @param mixed $value the value to assign
     * @param boolean $nocache if true any output of this variable will be not cached
     * @param int $scope one of self::SCOPE_* constants
     *
     * @return Data current Data (or Smarty or \Smarty\Template) instance for
     *                              chaining
     */
    public function assign($tpl_var, $value = null, $nocache = false, $scope = null): self
    {
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $_key => $_val) {
                $this->assign($_key, $_val, $nocache, $scope);
            }
            return $this;
        }
        switch ($scope ?? $this->get_default_scope()) {
            case self::SCOPE_GLOBAL:
            case self::SCOPE_SMARTY:
                $this->get_smarty()->assign($tpl_var, $value);
                break;
            case self::SCOPE_TPL_ROOT:
                $ptr = $this;
                while (isset($ptr->parent) && $ptr->parent instanceof Template) {
                    $ptr = $ptr->parent;
                }
                $ptr->assign($tpl_var, $value);
                break;
            case self::SCOPE_ROOT:
                $ptr = $this;
                while (isset($ptr->parent) && !$ptr->parent instanceof Smarty) {
                    $ptr = $ptr->parent;
                }
                $ptr->assign($tpl_var, $value);
                break;
            case self::SCOPE_PARENT:
                if ($this->parent) {
                    $this->parent->assign($tpl_var, $value);
                } else {
                    // assign local as fallback
                    $this->assign($tpl_var, $value);
                }
                break;
            case self::SCOPE_LOCAL:
            default:
                if (isset($this->tpl_vars[$tpl_var])) {
                    $this->tpl_vars[$tpl_var]->set_value($value);
                    if ($nocache) {
                        $this->tpl_vars[$tpl_var]->set_nocache(true);
                    }
                } else {
                    $this->tpl_vars[$tpl_var] = new Variable($value, $nocache);
                }
        }
        return $this;
    }
    /**
     * appends values to template variables
     *
     * @param array|string $tpl_var the template variable name(s)
     * @param mixed        $value   the value to append
     * @param bool         $merge   flag if array elements shall be merged
     * @param bool         $nocache if true any output of this variable will
     *                              be not cached
     *
     * @api  Smarty::append()
     */
    public function append($tpl_var, $value = null, $merge = false, $nocache = false): self
    {
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $_key => $_val) {
                $this->append($_key, $_val, $merge, $nocache);
            }
        } else {
            $new_value = $this->get_value($tpl_var) ?? [];
            if (!is_array($new_value)) {
                $new_value = (array) $new_value;
            }
            if ($merge && is_array($value)) {
                foreach ($value as $_mkey => $_mval) {
                    $new_value[$_mkey] = $_mval;
                }
            } else {
                $new_value[] = $value;
            }
            $this->assign($tpl_var, $new_value, $nocache);
        }
        return $this;
    }
    /**
     * assigns a global Smarty variable
     *
     * @param string  $varName the global variable name
     * @param mixed   $value   the value to assign
     * @param boolean $nocache if true any output of this variable will be not cached
     *
     * @return Data
     * @deprecated since 5.0
     */
    public function assign_global($var_name, $value = null, $nocache = false)
    {
        trigger_error(__METHOD__ . ' is deprecated. Use \Smarty\Smarty::assign() to assign a variable ' . ' at the Smarty level.', E_USER_DEPRECATED);
        return $this->get_smarty()->assign($var_name, $value, $nocache);
    }
    /**
     * Returns a single or all template variables
     *
     * @param string                                                  $varName       variable name or null
     * @param bool $searchParents include parent templates?
     *
     * @return mixed variable value or or array of variables
     * @api  Smarty::getTemplateVars()
     *
     */
    public function get_template_vars($var_name = null, $search_parents = true)
    {
        if (isset($var_name)) {
            return $this->get_value($var_name, $search_parents);
        }
        return array_merge($this->parent && $search_parents ? $this->parent->get_template_vars() : [], array_map(function (Variable $var) {
            return $var->get_value();
        }, $this->tpl_vars));
    }
    /**
     * Wrapper for ::getVariable()
     *
     * @deprecated since 5.0
     *
     * @param $varName
     * @param $searchParents
     * @param $errorEnable
     *
     * @return void
     */
    public function _get_variable($var_name, $search_parents = true, $error_enable = true)
    {
        trigger_error('Using ::_getVariable() to is deprecated and will be ' . 'removed in a future release. Use getVariable() instead.', E_USER_DEPRECATED);
        return $this->get_variable($var_name, $search_parents, $error_enable);
    }
    /**
     * Gets the object of a Smarty variable
     *
     * @param string $varName the name of the Smarty variable
     * @param bool $searchParents search also in parent data
     * @param bool $errorEnable
     *
     * @return Variable
     */
    public function get_variable($var_name, $search_parents = true, $error_enable = true)
    {
        if (isset($this->tpl_vars[$var_name])) {
            return $this->tpl_vars[$var_name];
        }
        if ($search_parents && $this->parent) {
            return $this->parent->get_variable($var_name, $search_parents, $error_enable);
        }
        if ($error_enable && $this->get_smarty()->error_unassigned) {
            // force a notice
            $x = ${$var_name};
        }
        return new Undefined_Variable();
    }
    /**
     * Directly sets a complete Variable object in the variable with the given name.
     * @param $varName
     *
     */
    public function set_variable($var_name, Variable $variable_object): void
    {
        $this->tpl_vars[$var_name] = $variable_object;
    }
    /**
     * Indicates if given variable has been set.
     * @param $varName
     */
    public function has_variable($var_name): bool
    {
        return !$this->get_variable($var_name, true, false) instanceof Undefined_Variable;
    }
    /**
     * Returns the value of the Smarty\Variable given by $varName, or null if the variable does not exist.
     *
     * @param $varName
     * @param bool $searchParents
     *
     * @return mixed|null
     */
    public function get_value($var_name, $search_parents = true)
    {
        $variable = $this->get_variable($var_name, $search_parents);
        return isset($variable) ? $variable->get_value() : null;
    }
    /**
     * load config variables into template object
     */
    public function assign_config_vars(array $new_config_vars, array $sections = []): void
    {
        // copy global config vars
        foreach ($new_config_vars['vars'] as $variable => $value) {
            if ($this->get_smarty()->config_overwrite || !isset($this->config_vars[$variable])) {
                $this->config_vars[$variable] = $value;
            } else {
                $this->config_vars[$variable] = array_merge((array) $this->config_vars[$variable], (array) $value);
            }
        }
        foreach ($sections as $tpl_section) {
            if (isset($new_config_vars['sections'][$tpl_section])) {
                foreach ($new_config_vars['sections'][$tpl_section]['vars'] as $variable => $value) {
                    if ($this->get_smarty()->config_overwrite || !isset($this->config_vars[$variable])) {
                        $this->config_vars[$variable] = $value;
                    } else {
                        $this->config_vars[$variable] = array_merge((array) $this->config_vars[$variable], (array) $value);
                    }
                }
            }
        }
    }
    /**
     * Get Smarty object
     *
     * @return Smarty
     */
    public function get_smarty()
    {
        return $this->smarty;
    }
    /**
     * clear the given assigned template variable(s).
     *
     * @param string|array $tpl_var the template variable(s) to clear
     *
     *
     * @api  Smarty::clearAssign()
     */
    public function clear_assign($tpl_var): self
    {
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $curr_var) {
                unset($this->tpl_vars[$curr_var]);
            }
        } else {
            unset($this->tpl_vars[$tpl_var]);
        }
        return $this;
    }
    /**
     * clear all the assigned template variables.
     *
     *
     * @api  Smarty::clearAllAssign()
     */
    public function clear_all_assign(): self
    {
        $this->tpl_vars = [];
        return $this;
    }
    /**
     * clear a single or all config variables
     *
     * @param string|null $name variable name or null
     *
     *
     * @api  Smarty::clearConfig()
     */
    public function clear_config($name = null): self
    {
        if (isset($name)) {
            unset($this->config_vars[$name]);
        } else {
            $this->config_vars = [];
        }
        return $this;
    }
    /**
     * Gets a config variable value
     *
     * @param string $varName the name of the config variable
     *
     * @return mixed  the value of the config variable
     * @throws Exception
     */
    public function get_config_variable($var_name)
    {
        if (isset($this->config_vars[$var_name])) {
            return $this->config_vars[$var_name];
        }
        $return_value = $this->parent ? $this->parent->get_config_variable($var_name) : null;
        if ($return_value === null && $this->get_smarty()->error_unassigned) {
            throw new Exception("Undefined variable {$var_name}");
        }
        return $return_value;
    }
    public function has_config_variable($var_name): bool
    {
        try {
            return $this->get_config_variable($var_name) !== null;
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * Returns a single or all config variables
     *
     * @param string $varname variable name or null
     *
     * @return mixed variable value or or array of variables
     * @throws Exception
     *
     * @api  Smarty::getConfigVars()
     */
    public function get_config_vars($varname = null)
    {
        if (isset($varname)) {
            return $this->get_config_variable($varname);
        }
        return array_merge($this->parent ? $this->parent->get_config_vars() : [], $this->config_vars);
    }
    /**
     * load a config file, optionally load just selected sections
     *
     * @param string $config_file filename
     * @param mixed                                                   $sections    array of section names, single
     *                                                                             section or null
     * @returns $this
     * @throws \Exception
     *
     * @api  Smarty::configLoad()
     */
    public function config_load($config_file, $sections = null): self
    {
        $template = $this->get_smarty()->do_create_template($config_file, null, null, $this, null, null, true);
        $template->caching = Smarty::CACHING_OFF;
        $template->assign('sections', (array) ($sections ?? []));
        // trigger a call to $this->assignConfigVars
        $template->fetch();
        return $this;
    }
    /**
     * Sets the default scope for new variables assigned in this template.
     *
     * @return void
     */
    protected function set_default_scope(int $scope)
    {
        $this->default_scope = $scope;
    }
    /**
     * Returns the default scope for new variables assigned in this template.
     */
    public function get_default_scope(): int
    {
        return $this->default_scope;
    }
    /**
     * @return Data|Smarty|null
     */
    public function get_parent()
    {
        return $this->parent;
    }
    /**
     * @param Data|Smarty|null $parent
     */
    public function set_parent($parent): void
    {
        $this->parent = $parent;
    }
    public function push_stack(): void
    {
        $stack_list = [];
        foreach ($this->tpl_vars as $name => $variable) {
            $stack_list[$name] = clone $variable;
            // variables are stored in Variable objects
        }
        $this->_var_stack[] = $this->tpl_vars;
        $this->tpl_vars = $stack_list;
        $this->_config_stack[] = $this->config_vars;
    }
    public function pop_stack(): void
    {
        $this->tpl_vars = array_pop($this->_var_stack);
        $this->config_vars = array_pop($this->_config_stack);
    }
}