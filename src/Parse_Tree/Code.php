<?php

declare (strict_types=1);
namespace Smarty\Parse_Tree;

/**
 * Smarty Internal Plugin Templateparser Parse Tree
 * These are classes to build parse trees in the template parser
 *
 * @author     Thue Kristensen
 * @author     Uwe Tews
 */
/**
 * Code fragment inside a tag .
 *
 * @ignore
 */
class Code extends Base
{
    /**
     * Create parse tree buffer for code fragment
     *
     * @param string $data content
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * Return buffer content in parentheses
     *
     *
     * @return string content
     */
    public function to_smarty_php(\Smarty\Parser\Template_Parser $parser): string
    {
        return sprintf('(%s)', $this->data);
    }
}