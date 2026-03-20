<?php

declare (strict_types=1);
namespace Smarty\Parse_Tree;

/**
 * Smarty Internal Plugin Templateparser Parse Tree
 * These are classes to build parse tree in the template parser
 *
 * @author     Thue Kristensen
 * @author     Uwe Tews
 */
/**
 * A complete smarty tag.
 *
 * @ignore
 */
class Tag extends Base
{
    /**
     * Saved block nesting level
     *
     * @var int
     */
    public $saved_block_nesting;
    /**
     * Create parse tree buffer for Smarty tag
     *
     * @param \Smarty\Parser\TemplateParser $parser parser object
     * @param string                          $data   content
     */
    public function __construct(\Smarty\Parser\Template_Parser $parser, $data)
    {
        $this->data = $data;
        $this->saved_block_nesting = $parser->block_nesting_level;
    }
    /**
     * Return buffer content
     *
     *
     * @return string content
     */
    public function to_smarty_php(\Smarty\Parser\Template_Parser $parser)
    {
        return $this->data;
    }
    /**
     * Return complied code that loads the evaluated output of buffer content into a temporary variable
     *
     *
     * @return string template code
     */
    public function assign_to_var(\Smarty\Parser\Template_Parser $parser)
    {
        $var = $parser->compiler->get_new_prefix_variable();
        $tmp = $parser->compiler->append_code('<?php ob_start();?>', (string) $this->data);
        $tmp = $parser->compiler->append_code($tmp, "<?php {$var}=ob_get_clean();?>");
        $parser->compiler->append_prefix_code($tmp);
        return $var;
    }
}