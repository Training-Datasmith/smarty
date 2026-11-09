<?php

declare (strict_types=1);
namespace Smarty\Parse_Tree;

/**
 * Smarty Internal Plugin Templateparser ParseTree
 * These are classes to build parsetree in the template parser
 *
 * @author     Thue Kristensen
 * @author     Uwe Tews
 */
/**
 * @ignore
 */
abstract class Base
{
    /**
     * Buffer content
     *
     * @var mixed
     */
    public $data;
    /**
     * Subtree array
     *
     * @var array
     */
    public $subtrees = [];
    /**
     * Return buffer
     *
     *
     * @return string buffer content
     */
    abstract public function to_smarty_php(\Smarty\Parser\Template_Parser $parser);
}