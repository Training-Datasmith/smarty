<?php

declare (strict_types=1);
/**
 * Smarty Internal Compile Plugin Base
 * @author     Uwe Tews
 */
namespace Smarty\Compile;

use Smarty\Compiler\Template;
use Smarty\Data;
use Smarty\Exception;
/**
 * This class does extend all internal compile plugins
 *
 */
abstract class Base implements Compiler_Interface
{
    /**
     * Array of names of required attribute required by tag
     *
     * @var array
     */
    protected $required_attributes = [];
    /**
     * Array of names of optional attribute required by tag
     * use array('_any') if there is no restriction of attributes names
     *
     * @var array
     */
    protected $optional_attributes = [];
    /**
     * Shorttag attribute order defined by its names
     *
     * @var array
     */
    protected $shorttag_order = [];
    /**
     * Array of names of valid option flags
     *
     * @var array
     */
    protected $option_flags = ['nocache'];
    /**
     * @var bool
     */
    protected $cacheable = true;
    public function is_cacheable(): bool
    {
        return $this->cacheable;
    }
    /**
     * Converts attributes into parameter array strings
     *
     *
     */
    protected function format_params_array(array $_attr): array
    {
        $_params_array = [];
        foreach ($_attr as $_key => $_value) {
            $_params_array[] = var_export($_key, true) . '=>' . $_value;
        }
        return $_params_array;
    }
    /**
     * This function checks if the attributes passed are valid
     * The attributes passed for the tag to compile are checked against the list of required and
     * optional attributes. Required attributes must be present. Optional attributes are check against
     * the corresponding list. The keyword '_any' specifies that any attribute will be accepted
     * as valid
     *
     * @param object $compiler compiler object
     * @param array $attributes attributes applied to the tag
     *
     * @return array  of mapped attributes for further processing
     */
    protected function get_attributes($compiler, $attributes)
    {
        return (new Attribute_Compiler($this->required_attributes, $this->optional_attributes, $this->shorttag_order, $this->option_flags))->get_attributes($compiler, $attributes);
    }
    /**
     * Push opening tag name on stack
     * Optionally additional data can be saved on stack
     *
     * @param Template $compiler compiler object
     * @param string $openTag the opening tag's name
     * @param mixed $data optional data saved
     */
    protected function open_tag(Template $compiler, $open_tag, $data = null)
    {
        $compiler->open_tag($open_tag, $data);
    }
    /**
     * Pop closing tag
     * Raise an error if this stack-top doesn't match with expected opening tags
     *
     * @param Template $compiler compiler object
     * @param array|string $expectedTag the expected opening tag names
     *
     * @return mixed        any type the opening tag's name or saved data
     */
    protected function close_tag(Template $compiler, $expected_tag)
    {
        return $compiler->close_tag($expected_tag);
    }
    /**
     * @param mixed $scope
     * @param array $invalidScopes
     *
     * @throws Exception
     */
    protected function convert_scope($scope): int
    {
        static $scopes = [
            'local' => Data::SCOPE_LOCAL,
            // current scope
            'parent' => Data::SCOPE_PARENT,
            // parent scope (definition unclear)
            'tpl_root' => Data::SCOPE_TPL_ROOT,
            // highest template (keep going up until parent is not a template)
            'root' => Data::SCOPE_ROOT,
            // highest scope (definition unclear)
            'global' => Data::SCOPE_GLOBAL,
            // smarty object
            'smarty' => Data::SCOPE_SMARTY,
        ];
        $_scope_name = trim($scope, '\'"');
        if (is_numeric($_scope_name) && in_array($_scope_name, $scopes)) {
            return (int) $_scope_name;
        }
        if (isset($scopes[$_scope_name])) {
            return $scopes[$_scope_name];
        }
        $err = var_export($_scope_name, true);
        throw new Exception("illegal value '{$err}' for \"scope\" attribute");
    }
    /**
     * Compiles code for the tag
     *
     * @param array                                 $args      array with attributes from parser
     * @param Template $compiler  compiler object
     * @param array                                 $parameter array with compilation parameter
     *
     * @return string compiled code as a string
     * @throws \Smarty\CompilerException
     */
    abstract public function compile($args, Template $compiler, $parameter = [], $tag = null, $function = null): string;
}