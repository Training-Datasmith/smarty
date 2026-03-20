<?php

declare (strict_types=1);
/**
 * This file is part of Smarty.
 *
 * (c) 2015 Uwe Tews
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Smarty\Compile\Tag;

use Smarty\Parse_Tree\Template;
/**
 * Smarty Internal Plugin Compile Block Class
 *
 * @author Uwe Tews <uwe.tews@googlemail.com>
 */
class Block extends Inheritance
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    public $required_attributes = ['name'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    public $shorttag_order = ['name'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $option_flags = ['hide', 'nocache'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    public $optional_attributes = ['assign'];
    /**
     * Compiles code for the {block} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     * @param array $parameter array with compilation parameter
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        if (!isset($compiler->_cache['blockNesting'])) {
            $compiler->_cache['blockNesting'] = 0;
        }
        if ($compiler->_cache['blockNesting'] === 0) {
            // make sure that inheritance gets initialized in template code
            $this->register_init($compiler);
            $this->option_flags = ['hide', 'nocache', 'append', 'prepend'];
        } else {
            $this->option_flags = ['hide', 'nocache'];
        }
        // check and get attributes
        $_attr = $this->get_attributes($compiler, $args);
        ++$compiler->_cache['blockNesting'];
        $_class_name = 'Block_' . preg_replace('![^\w]+!', '_', uniqid(mt_rand(), true));
        $this->open_tag($compiler, 'block', [$_attr, $compiler->tag_nocache, $compiler->get_parser()->current_buffer, $compiler->get_template()->get_compiled()->get_nocache_code(), $_class_name]);
        $compiler->get_parser()->current_buffer = new Template();
        $compiler->get_template()->get_compiled()->set_nocache_code(false);
        $compiler->suppress_nocache_processing = true;
        return '';
    }
}