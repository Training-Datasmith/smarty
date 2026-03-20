<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Function Class
 *
 */
class Function_Tag extends Base
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $required_attributes = ['name'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $shorttag_order = ['name'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $optional_attributes = ['_any'];
    /**
     * Compiles code for the {function} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        // check and get attributes
        $_attr = $this->get_attributes($compiler, $args);
        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', null, true);
        }
        unset($_attr['nocache']);
        $_name = trim($_attr['name'], '\'"');
        if (!preg_match('/^[a-zA-Z0-9_\x80-\xff]+$/', $_name)) {
            $compiler->trigger_template_error("Function name contains invalid characters: {$_name}", null, true);
        }
        $compiler->get_parent_compiler()->tpl_function[$_name] = [];
        $save = [$_attr, $compiler->get_parser()->current_buffer, $compiler->get_template()->get_compiled()->get_nocache_code(), $compiler->get_template()->caching];
        $this->open_tag($compiler, 'function', $save);
        // Init temporary context
        $compiler->get_parser()->current_buffer = new \Smarty\Parse_Tree\Template();
        $compiler->get_template()->get_compiled()->set_nocache_code(false);
        return '';
    }
}