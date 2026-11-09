<?php

declare (strict_types=1);
namespace Smarty\Parse_Tree;

/**
 * Smarty Internal Plugin Templateparser Parse Tree
 * These are classes to build parse tree in the template parser
 *
 * @author     Thue Kristensen
 * @author     Uwe Tews
 *             *
 *             template text
 * @ignore
 */
class Text extends Base
{
    /**
     * Wether this section should be stripped on output to smarty php
     * @var bool
     */
    private $to_be_stripped = false;
    /**
     * Create template text buffer
     *
     * @param string $data text
     * @param bool $toBeStripped wether this section should be stripped on output to smarty php
     */
    public function __construct($data, $to_be_stripped = false)
    {
        $this->data = $data;
        $this->to_be_stripped = $to_be_stripped;
    }
    /**
     * Wether this section should be stripped on output to smarty php
     * @return bool
     */
    public function is_to_be_stripped()
    {
        return $this->to_be_stripped;
    }
    /**
     * Return buffer content
     *
     *
     * @return string text
     */
    public function to_smarty_php(\Smarty\Parser\Template_Parser $parser)
    {
        return $this->data;
    }
}