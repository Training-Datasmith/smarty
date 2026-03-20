<?php

declare (strict_types=1);
namespace Smarty\Parse_Tree;

/**
 * Smarty Internal Plugin Templateparser Parse Tree
 * These are classes to build parse tree  in the template parser
 *
 * @author     Thue Kristensen
 * @author     Uwe Tews
 */
/**
 * Raw chars as part of a double-quoted string.
 *
 * @ignore
 */
class Dq_Content extends Base
{
    /**
     * Create parse tree buffer with string content
     *
     * @param string $data string section
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * Return content as double-quoted string
     *
     *
     * @return string doubled quoted string
     */
    public function to_smarty_php(\Smarty\Parser\Template_Parser $parser): string
    {
        return '"' . $this->data . '"';
    }
}