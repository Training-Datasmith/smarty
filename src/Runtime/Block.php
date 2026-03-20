<?php

declare (strict_types=1);
namespace Smarty\Runtime;

/**
 * Smarty {block} tag class
 *
 * @author     Uwe Tews
 */
class Block
{
    /**
     * Block name
     *
     * @var string
     */
    public $name = '';
    /**
     * Hide attribute
     *
     * @var bool
     */
    public $hide = false;
    /**
     * Append attribute
     *
     * @var bool
     */
    public $append = false;
    /**
     * prepend attribute
     *
     * @var bool
     */
    public $prepend = false;
    /**
     * Block calls $smarty.block.child
     *
     * @var bool
     */
    public $calls_child = false;
    /**
     * Inheritance child block
     *
     * @var Block|null
     */
    public $child;
    /**
     * Inheritance calling parent block
     *
     * @var Block|null
     */
    public $parent;
    /**
     * Inheritance Template index
     *
     * @var int
     */
    public $tpl_index = 0;
    /**
     * Block constructor.
     * - if outer level {block} of child template ($state === 1) save it as child root block
     * - otherwise process inheritance and render
     *
     * @param string   $name     block name
     * @param int|null $tplIndex index of outer level {block} if nested
     */
    public function __construct($name, $tpl_index)
    {
        $this->name = $name;
        $this->tpl_index = $tpl_index;
    }
    /**
     * Compiled block code overloaded by {block} class
     */
    public function call_block(\Smarty\Template $tpl)
    {
    }
}