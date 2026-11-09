<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Config Load
 * Compiles the {config load} tag
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
use Smarty\Smarty;
/**
 * Smarty Internal Plugin Compile Config Load Class
 *
 */
class Config_Load extends Base
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Base_Plugin
     */
    protected $required_attributes = ['file'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Base_Plugin
     */
    protected $shorttag_order = ['file', 'section'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Base_Plugin
     */
    protected $optional_attributes = ['section'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Base_Plugin
     */
    protected $option_flags = [];
    /**
     * Compiles code for the {config_load} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     * @throws \Smarty\Compiler_Exception
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        // check and get attributes
        $_attr = $this->get_attributes($compiler, $args);
        // save possible attributes
        $conf_file = $_attr['file'];
        $section = $_attr['section'] ?? 'null';
        // create config object
        return "<?php\n\$_smarty_tpl->config_load({$conf_file}, {$section});\n?>\n";
    }
}